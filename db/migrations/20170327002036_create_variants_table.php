<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Db\Table\Column;

class CreateVariantsTable extends AbstractMigration
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
               ->setType('string')
               ->setLimit(45)
               ->setIdentity(true);
        $options = array(
            'id'            => false,
            'primary_key'   => 'id'
        );
        $variants = $this->table('variants',$options);
        $variants
            ->addColumn('id', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('shop_id', 'integer', ['null' => false])
            ->addColumn('product_id', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('barcode', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('fufillment_service', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('grams', 'integer', ['limit' => 45, 'null' => true])
            ->addColumn('image_id', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn("inventory_management", "string", ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('inventory_policy', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('option1', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('option2', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('option3', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('position', 'integer', ['null' => false])
            ->addColumn('price', 'string', ['limit' => 45, 'null' => false])
            ->addColumn('sku', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('title', 'string', ['limit' => 145, 'null' =>true, 'default' => null])
            ->addIndex('shop_id')
            ->addIndex('product_id')
            ->create();
    }
}
