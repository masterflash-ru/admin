<?php

namespace Admin;

use Mf\Migrations\AbstractMigration;
use Mf\Migrations\MigrationInterface;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql;


class Version20181105153009 extends AbstractMigration implements MigrationInterface
{
    public static $description = "Migration description";

    public function up($schema, $adapter)
    {
        $this->mysql_add_create_table=" ENGINE=MyIsam DEFAULT CHARSET=utf8";
        
        $table = new Ddl\CreateTable("admin_menu");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('name', 255,true,null,["COMMENT"=>"Элемент меню"]));
        $table->addColumn(new Ddl\Column\Char('url', 255,true,null,["COMMENT"=>"URL"]));
        $table->addColumn(new Ddl\Column\Integer('level',false,0,["COMMENT"=>"уровень"]));
        $table->addColumn(new Ddl\Column\Integer('subid',false,0,["COMMENT"=>"ID родителя"]));
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['level'],'level')
        );
        $table->addConstraint(
            new Ddl\Index\Index(['subid'],'subid')
        );
        $this->addSql($table);

        $insert = new Sql\Insert("admin_menu");
        $insert->columns(['id', 'name', 'level', 'subid', 'url']);
        $insert->values([1, 'Система управления', 0, 0, '']);
        $this->addSql($insert);
        $insert->values([2, 'Меню администраторов', 1, 1, '/adm/universal-interface/admin_menu']);
        $this->addSql($insert);
        $insert->values([3, 'Навигация/структура сайта', 0, 0, '']);
        $this->addSql($insert);
        $insert->values([4, 'Резервир./восстановл. базы', 1, 1, '/adm/backuprestore']);
        $this->addSql($insert);
        $insert->values([5, 'Меню сайта', 1, 3, '/adm/universal-interface/menu']);
        $this->addSql($insert);
        $insert->values([6, 'Интерфейсы (устарело)', 1, 1, '']);
        $this->addSql($insert);
        $insert->values([7, 'Линейные интерфейсы', 2, 6, '/adm/constructorline']);
        $this->addSql($insert);
        $insert->values([8, 'Древовидные интерфесы', 2, 6, '/adm/constructortree']);
        $this->addSql($insert);
        $insert->values([9, 'Генератор Entity', 1, 1,  '/adm/entity']);
        $this->addSql($insert);
        $insert->values([10, 'Пользователи и группы', 1, 1,  '']);
        $this->addSql($insert);
        $insert->values([11, 'Системные группы польз.', 2, 10,  '/adm/universal-interface/systemgroups']);
        $this->addSql($insert);
        $insert->values([12, 'Группы пользователей', 2, 10,  '/adm/universal-interface/usergroups']);
        $this->addSql($insert);
        $insert->values([13, 'Пользователи', 2, 10, '/adm/universal-interface/users']);
        $this->addSql($insert);
        $insert->values([14, 'Доступы', 1, 1, '']);
        $this->addSql($insert);
        $insert->values([15, 'Пользоват. доступы', 2, 14,  '/adm/universal-interface/permissions']);
        $this->addSql($insert);
        $insert->values([16, 'Системные доступы', 2, 14,  '/adm/universal-interface/permissions_from_config']);
        $this->addSql($insert);

        $table = new Ddl\CreateTable("design_tables");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('interface_name', 127,false,""));
        $table->addColumn(new Ddl\Column\Char('table_name', 255,false,""));
        $table->addColumn(new Ddl\Column\Integer('table_type',false,0));
        $table->addColumn(new Ddl\Column\Char('col_name',255,false,""));
        $table->addColumn(new Ddl\Column\Char('caption_style',255,true));
        $table->addColumn(new Ddl\Column\Integer('row_type',true,0));
        $table->addColumn(new Ddl\Column\Integer('col_por',true,0));
        $table->addColumn(new Ddl\Column\Text('pole_spisok_sql',null,true));
        $table->addColumn(new Ddl\Column\Char('pole_global_const',255,true));
        $table->addColumn(new Ddl\Column\Char('pole_prop',255,true));
        $table->addColumn(new Ddl\Column\Char('pole_type',255,true));
        $table->addColumn(new Ddl\Column\Char('pole_style',255,true));
        $table->addColumn(new Ddl\Column\Char('pole_name',255,true));
        $table->addColumn(new Ddl\Column\Text('default_sql',null,true));
        $table->addColumn(new Ddl\Column\Char('functions_befo',50,true));
        $table->addColumn(new Ddl\Column\Char('functions_after',50,true));
        $table->addColumn(new Ddl\Column\Char('functions_befo_out',50,true));
        $table->addColumn(new Ddl\Column\Char('functions_befo_del',50,true));
        $table->addColumn(new Ddl\Column\Text('properties',null,true));
        $table->addColumn(new Ddl\Column\Char('value',255,true));
        $table->addColumn(new Ddl\Column\Char('validator',255,true));
        $table->addColumn(new Ddl\Column\Integer('sort_item_flag',true,0));
        $table->addColumn(new Ddl\Column\Text('col_function_array',null,true));
        
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['table_name'],'table_name')
        );
        $table->addConstraint(
            new Ddl\Index\Index(['interface_name'],'interface_name')
        );
        $this->addSql($table);
        
        $table = new Ddl\CreateTable("design_tables_text_interfase");
        $table->addColumn(new Ddl\Column\Integer('id',false,null,["AUTO_INCREMENT"=>true]));
        $table->addColumn(new Ddl\Column\Char('language', 10,false,""));
        $table->addColumn(new Ddl\Column\Integer('table_type',false,0));
        $table->addColumn(new Ddl\Column\Char('interface_name',255,false,""));
        $table->addColumn(new Ddl\Column\Char('item_name',255,true));
        $table->addColumn(new Ddl\Column\Text('text',null,true));
        
        $table->addConstraint(
            new Ddl\Constraint\PrimaryKey(['id'])
        );
        $table->addConstraint(
            new Ddl\Index\Index(['language'],'language')
        );
        $table->addConstraint(
            new Ddl\Index\Index(['interface_name'],'interface_name')
        );
        $table->addConstraint(
            new Ddl\Index\Index(['item_name'],'item_name')
        );
        $this->addSql($table);
    }

    public function down($schema, $adapter)
    {
        $drop = new Ddl\DropTable('admin_menu');
        $this->addSql($drop);
        $drop = new Ddl\DropTable('design_tables');
        $this->addSql($drop);
        $drop = new Ddl\DropTable('design_tables_text_interfase');
        $this->addSql($drop);
    }
}
