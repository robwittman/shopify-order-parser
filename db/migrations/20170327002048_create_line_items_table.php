<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Db\Table\Column;

class CreateLineItemsTable extends AbstractMigration
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
        $lineItems = $this->table('line_items', $options);
        $lineItems
            ->addColumn($column)
            ->addColumn('product_id', 'biginteger', ['null' => true, 'default' => null])
            ->addColumn('variant_id', 'biginteger', ['null' => true, 'default' => null])
            ->addColumn('vendor', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('variant_title', 'string', ['limit' => 145, 'null' => false])
            ->addColumn('quantity', 'integer', ['null' => false])
            ->addColumn('price', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 145, 'null' => false])
            ->addColumn('order_id', 'biginteger', ['null' => false])
            ->addColumn('shop_id', 'integer', ['null' => false])
            ->addIndex('shop_id')
            ->addIndex('order_id')
            ->create();
    }
}
