<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table\Column;

class CreateProductsTable extends AbstractMigration
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

        $products = $this->table('products', $options);
        $products
            ->addColumn($column)
            ->addColumn('body_html', 'text', ['null' => true, 'default' => null])
            ->addColumn('created_at', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('handle', 'string', ['limit' => 145, 'null' => false])
            ->addColumn('images', 'text', ['null' => true, 'default' => null])
            ->addColumn('options', 'string', ['limit' => 245, 'null' => true, 'default' => null])
            ->addColumn('product_type', 'string', ['limit' => 145, 'null' => false])
            ->addColumn('tags', 'text', ['null' => true, 'default' => null])
            ->addColumn('vendor', 'string', ['null' => true, 'default' => null])
            ->addColumn('color_count', 'integer', ['null' => true, 'default' => null])
            ->addColumn('shop_id', 'integer', ['null' => false])
            ->addIndex('shop_id')
            ->create();
    }
}
