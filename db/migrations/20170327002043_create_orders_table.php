<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Db\Table\Column;

class CreateOrdersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $column = new Column();
        $column->setName('id')
               ->setType('biginteger')
               ->setIdentity(true);
        $options = array(
            'id'            => false,
            'primary_key'   => 'id'
        );
        $orders = $this->table('orders', $options);
        $orders
            ->addColumn($column)
            ->addColumn('shop_id', 'integer', ['null' => false])
            ->addColumn('created_at', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('closed_at', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('cancelled_at', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('email', 'string', ['limit' => 145, 'null' => true])
            ->addColumn('financial_status', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('fulfillment_status', 'string', ['limit' => 45, 'null' => true])
            ->addColumn('tags', 'text', ['null' => true, 'default' => null])
            ->addColumn('name', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('number', 'integer', ['null' => false])
            ->addColumn('order_number', 'integer', ['null' => false])
            ->addColumn('processed_at', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('subtotal_price', 'string', ['limit' =>45 , 'null' => false])
            ->addColumn('total_discounts', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('total_line_items_price', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('total_price', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('total_tax', 'string', ['limit' => 45, 'null' => false])
            ->addIndex('shop_id')
            ->create();

    }
}
