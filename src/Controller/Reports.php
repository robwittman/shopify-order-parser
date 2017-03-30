<?php

namespace App\Controller;

use App\Model\Shop;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductVariant;
use App\Model\LineItem;

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
        $date = $request->getParsedBody()['report_date'];
        $type = $request->getParsedBody()['report_type'];
        $store = $request->getParsedBody()['store'];
        $start = (new \DateTime($date))->setTime(00,00,00)->format('c');
        $end = (new \DateTime($date))->setTime(23,59,59)->format('c');
        switch($type) {
            case self::GARMENT_REPORT:
                $report = $this->createGarmentReport($store, $start, $end);
                break;
            case self::PRINT_SCHEDULE:
                $report = $this->getPrintSchedule($store, $start, $end);
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
    public function createGarmentReport($store, $start, $end)
    {
        $orders = Order::where('created_at','<=',$end)
                       ->where('created_at', '>=', $start)
                       ->where('shop_id', '=', $store)
                       ->get();
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
    public function getPrintSchedule($store, $start, $end)
    {
        $orders = Order::where('created_at','<=',$end)
                       ->where('created_at', '>=', $start)
                       ->where('shop_id', '=', $store)
                       ->get();
        $result = array();
        foreach ($orders as $order) {
            $line_items = LineItem::where('order_id', '=', $order->id)
                                    ->where('vendor', '=', 'BPP')
                                    ->get();
            foreach ($line_items as $line_item) {
                $product = Product::find($line_item->product_id);
                if (empty($product)) {
                    throw new \Exception("Product {$line_item->product_id} was missing!");
                }
                if (!isset($result[$product->id])) {
                    $result[$product->id] = array(
                        'title' => $line_item->title,
                        'quantity' => 0,
                        'color_count' => $product->color_count
                    );
                }
                $result[$product->id]['quantity'] += $line_item->quantity;
            }
        }
        return $result;
    }
}
