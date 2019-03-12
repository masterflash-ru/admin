<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы ColModel
* функции возвращают массив пригодный для 
* добавления в конфиг ColModel
*/
namespace Admin\Service\JqGrid;

use Zend\Stdlib\ArrayUtils;
use Zend\Json\Expr;


class ColModelHelper
{

    /**
    * посточные кнопки действия
    */
    public static function cellActions(string $name="myactions", array $options=[])
    {
        return  ArrayUtils::merge([
            "name" => $name,
            "label"=>"Операция",
            "width"=>80,
            "formatter" => "actions",
            "sortable"=>false,
            "formatoptions" => [
                "keys" => true,
                "editformbutton"=>false,
                "editbutton"=>true,
                "delbutton"=>true,
            ]
        ],$options);
    }
    
    /**
    * вывод однострочного эл-та
    * в сетке он скрыт
    */
    public static function text(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            "name" => $name,
            //"width" => 200,
            "editable" => true,
            "edittype" => "text",
            "formoptions" => [
                // "rowpos" => 2,
                // "colpos" => true,
               // "elmprefix" => "*",
                //"elmsuffix" =>"" ,
            ],
            "editoptions" => [
                "size" => 100,
            ],
            "editrules"=>[
                "required"=>false,
            ],
        ],$options);
    }
    
    /**
    * вывод многострочного эл-та
    * 
    */
    public static function textarea(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            "name" => $name,
            //"width" => 200,
            "editable" => true,
            "edittype" => "textarea",
            "formoptions" => [
                // "rowpos" => 2,
                // "colpos" => true,
               // "elmprefix" => "*",
                //"elmsuffix" =>"" ,
            ],
            "editoptions" => [
                "cols" => 120,
                "rows"=>5
            ],
            "editrules"=>[
                "required"=>false,
            ],
        ],$options);
    }

    
    /**
    * вывод одиночного флажка
    * 
    */
    public static function checkbox(string $name, array $options=[])
    {
        return ArrayUtils::merge([
           "name" => $name,
            "editable" => true,
            "edittype" => "checkbox",
            "editoptions"=>[
                "value"=>"1:0"/*значение флажка (установлен-сброшен)*/
            ],
            "formatter"=>"checkbox",
        ],$options);
    }

    /**
    * вывод выпадающего списка
    * 
    */
    public static function select(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            "name" => $name,
            "editable" => true,
            "edittype" => "select",
            "editoptions"=>[
                "value"=>[]
            ],
            "formatter"=>"select",
        ],$options);
    }

    /**
    * вывод редактора ckeditor
    * в сетке он скрыт
    */
    public static function ckeditor(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            "name" => $name,
            "hidden" => true,
            "editable" => true,
            "edittype" => "textarea",
            "editoptions" => [
               //"dataInit"=>new Expr('function (el){$(el).ckeditor();}'),
            ],
            "editrules"=>[
                "edithidden"=>true,
            ],
        ],$options);
    }

    /**
    * вывод фото из хранилища по ID
    * 
    */
    public static function image(string $name, array $options=[])
    {
        $def=[
           "name" => $name,
            "editable" => true,
            
            "edittype"=>"custom",
            "editoptions"=>[
                "custom_element"=>new Expr('imageEdit'),
                "custom_value"=>new Expr('imageSave'),
        
            ],
            "plugins"=>[
                "read"=>[
                    "Images" =>[
                        "image_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "storage_item_rule_name"=>"admin_img"   //имя правила из хранилища
                    ],
                ],
                "write"=>[
                    "Images"=>[
                        "image_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "database_table_name"=>""               //имя таблицы SQL куда вставляем, нужно для новых записей
                    ],
                ],
            ],
            "formatter"=>"image",
            "classes"=>"jqgrid-img"
        ];
        if (isset($options["plugins"]["read"])){
            unset($def["plugins"]["read"]);
        }
        if (isset($options["plugins"]["write"])){
            unset($def["plugins"]["write"]);
        }
        
        return ArrayUtils::merge($def,$options);
        
    }

    /**
    * вывод даты-времени + виджет выбора
    * 
    */
    public static function datetime(string $name, array $options=[])
    {
        $def=[/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "datetime",
            "plugins"=>[
                "write"=>[
                    "datetime"=>[
                        "toformat"=>"'Y-m-d H:i:s'",
                    ],
                ],
            ],

            "editoptions" => [
                "dataInit"=>new Expr('function (el){$(el).datetimepicker({timeInput: true,timeFormat: "HH:mm:ss",dateFormat:"dd.mm.yy"});}'),
                "defaultValue" =>new Expr('function(){var formatter = new Intl.DateTimeFormat("ru",{day:"numeric",year:"numeric",month:"numeric",hour: "numeric",minute: "numeric",second: "numeric"});return formatter.format(new Date()).replace(",","");}'),
                "size" => 50,
            ],
        ];
        if (isset($options["plugins"]["write"])){
            unset($def["plugins"]["write"]);
        }
        return ArrayUtils::merge($def,$options);

    }
    /**
    * вывод даты + виджет выбора
    *
    */
    public static function date(string $name, array $options=[])
    {
        return ArrayUtils::merge([/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "date",
            "editoptions" => [
                "dataInit"=>new Expr('function (el){$(el).datepicker({dateFormat:"dd.mm.yy"});}'),
                "defaultValue" =>new Expr('function(){var formatter = new Intl.DateTimeFormat("ru");return formatter.format(new Date());}'),
                "size" => 40,
            ],
        ],$options);
    }
    
}