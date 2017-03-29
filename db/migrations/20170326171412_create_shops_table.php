<?php

use Phinx\Migration\AbstractMigration;

class CreateShopsTable extends AbstractMigration
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
        $shops = $this->table('shops');
        $shops
            ->addColumn('myshopify_domain', 'string', array('limit' => 145, 'null' => false))
            ->addColumn('api_key', 'string', array('limit' => 145, 'null' => false))
            ->addColumn('password', 'string', array('limit' => 145, 'null' => false))
            ->addColumn('shared_secret', 'string', array('limit' => 145, 'null' => false))
            ->addIndex('myshopify_domain', array('unique' => true))
            ->create();
    }
}
