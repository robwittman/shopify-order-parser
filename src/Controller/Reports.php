<?php

namespace App\Controller;

use App\Model\Shop;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductVariant;
use App\Model\LineItem;

use Carbon\Carbon;

class Reports
{
    const GARMENT_REPORT = 'sales_by_garment';
    const PRINT_SCHEDULE = 'print_worksheet';

    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {
        //Render our index page
        $shops = Shop::all();
        $this->view->render($response, 'reports/index.html', [
            'shops' => $shops
        ]);
    }

    public function create($request, $response, $arguments)
    {
        $params = $request->getParsedBody();

        $start = !empty($params['start_date']) ? $params['start_date'] : null;
        $end = !empty($params['end_date']) ? $params['end_date'] : null;

        $type = $params['report_type'];
        $stores = array_map(function ($shop) {
            return (int) $shop;
        }, $params['stores']);
        switch($type) {
            case self::GARMENT_REPORT:
                $report = $this->createGarmentReport($stores, $start, $end);
                break;
            case self::PRINT_SCHEDULE:
                $report = $this->getPrintSchedule($stores, $start, $end);
                break;
            default:
                throw new \Exception("Unsupported report type {$type}");
        }

        return $this->view->render($response, "reports/{$type}.html" , array(
            'results' => $report,
            'start' => $start,
            'end' => $end,
            'date' => $date
        ));
    }

    /**
     * Take all; orders for given time frame, and
     * parse ordered products into array.
     * return [
     *     'Long Sleeve' => [
     *         'White' => [
     *             'L' => 4,
     *             'XL' => 3
     *         ],
     *         'Black' => [
     *              'S' => 2,
     *              'L' => 3
     *         ]
     *     ],
     *     'Tee' => [
     *         'Navy' => [
     *              'XS' => 1,
     *              'M' => 8
     *         ],
     *         'Pink' => [
     *              'S' => 3,
     *              'L' => 4
     *         ]
     *     ]
     * ]
     * @param integer   $store ID of the store to user
     * @param  string $start DateTime in 'c' format
     * @param  string $end   DateTime in 'c' format
     * @return array
     */
    public function createGarmentReport($stores, $start = null, $end = null)
    {
        $qb = Order::whereIn('shop_id', $stores);
        if (!is_null($start)) {
            $qb->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $start)->toDateString());
        }
        if (!is_null($end)) {
            $qb->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $end)->toDateString());
        }
        $orders = $qb->get();
        $result = array();

        foreach ($orders as $order) {
            $line_items = LineItem::where('order_id', '=', $order->id)
                                    ->where('vendor', '=', 'BPP')
                                    ->get();
            foreach ($line_items as $line_item) {
                $size = null;
                $color = null;
                $style = null;
                $product = Product::find($line_item->product_id);
                $variant = ProductVariant::find($line_item->variant_id);
                if (empty($product) || empty($variant)) {
                    throw new \Exception("Product or variant were missing!");
                }
                foreach ($product->options as $option) {
                    $opt = 'option'.$option->position;
                    if ($option->name == 'Size') {
                        $size = $opt;
                    } else if($option->name == 'Color') {
                        $color = $opt;
                    } else if ($option->name == 'Style') {
                        $style = $opt;
                    }
                }

                $varSize = $variant->{$size};
                $varColor = $variant->{$color};
                if (is_null($style)) {

                } else {
                    $varStyle = $variant->{$style};
                }
                if (!isset($result[$varStyle])) {
                    $result[$varStyle] = array();
                }
                if (!isset($result[$varStyle][$varColor])) {
                    $result[$varStyle][$varColor] = array();
                }
                if (!isset($result[$varStyle][$varColor][$varSize])) {
                    $result[$varStyle][$varColor][$varSize] = 0;
                }
                $result[$varStyle][$varColor][$varSize] += $line_item->quantity;
            }
        }
        return $result;
    }

    /**
     * Get the print schedule for a given day
     * Example result
     *
     * @param integer   $store ID of the store to user
     * @param  DateTime $start DateTime in 'c' format'
     * @param  DateTime $end   DateTime in 'c' format'
     * @return array
     */
    public function getPrintSchedule($stores, $start = null, $end = null)
    {
        $qb = Order::whereIn('shop_id', $stores);
        if (!is_null($start)) {
            $qb->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $start)->toDateString());
        }
        if (!is_null($end)) {
            $qb->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $end)->toDateString());
        }
        $orders = $qb->get();
        $result = array();
        foreach ($orders as $order) {
            $line_items = LineItem::where('order_id', '=', $order->id)
                                    ->where('vendor', '=', 'BPP')
                                    ->get();
            foreach ($line_items as $line_item) {
                $size = null;
                $color = null;
                $style = null;
                $product = Product::find($line_item->product_id);
                $variant = ProductVariant::find($line_item->variant_id);
                if (empty($product)) {
                    throw new \Exception("Product {$line_item->product_id} was missing!");
                }
                foreach ($product->options as $option) {
                    $opt = 'option'.$option->position;
                    if ($option->name == 'Size') {
                        $size = $opt;
                    } else if($option->name == 'Color') {
                        $color = $opt;
                    } else if ($option->name == 'Style') {
                        $style = $opt;
                    }
                }
                if (!isset($result[$product->id])) {
                    $result[$product->id] = array(
                        'title' => $line_item->title,
                        'quantity' => 0,
                        'color_count' => $product->color_count,
                        'product_id' => $product->id,
                        'breakdown' => array();
                    );
                }
                $varSize = $line_item->{$size};
                $varColor = $line_item->{$color};
                if (is_null($style)) {

                } else {
                    $varStyle = $variant->{$style};
                }
                if (!isset($result[$product->id['breakdown'][$varStyle])) {
                    $result[$product->id['breakdown'][$varStyle] = array();
                }
                if (!isset($result[$product->id['breakdown'][$varStyle][$varColor])) {
                    $result[$product->id['breakdown'][$varStyle][$varColor] = array();
                }
                if (!isset($result[$product->id['breakdown'][$varStyle][$varColor][$varSize])) {
                    $result[$product->id['breakdown'][$varStyle][$varColor][$varSize] = 0;
                }
                $result[$product->id['breakdown'][$varStyle][$varColor][$varSize] += $line_item->quantity;
                $result[$product->id]['quantity'] += $line_item->quantity;
            }
        }

        usort($result, function($a, $b) {
            return $a['quantity'] < $b['quantity'];
        });
        return $result;
    }
}
