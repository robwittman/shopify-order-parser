<?php

use Phinx\Migration\AbstractMigration;

class AddColumnsToShopsTable extends AbstractMigration
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
            ->addColumn('currency', 'string', ['limit' =>  15, 'null' => true, 'default' => null])
            ->addColumn('email', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('report_email', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('name', 'string', ['limit' => 145, 'null' => true, 'default' => null])
            ->addColumn('timezone', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->addColumn('iana_timezone', 'string', ['limit' => 45, 'null' => true, 'default' => null])
            ->update();
    }
}
