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
use Zend\Session\Container;


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

        $def=[
            "name" => $name,
            "hidden" => true,
            "editable" => true,
            "edittype" => "textarea",
            "editoptions" => [
               "dataInit"=>new Expr('function (el){$(el).ckeditor();}'),
                "Path_File"=>"media/files",
                "Path_Image"=>"media/pic",
            ],
            "editrules"=>[
                "edithidden"=>true,
            ],
        ];
        $options=ArrayUtils::merge($def,$options);
        $fck_connector_config = new Container('fck_connector_config');
		$fck_connector_config->Enabled=true;
		$fck_connector_config->FileTypesPath_File=$options["editoptions"]["Path_File"];
		$fck_connector_config->FileTypesPath_Image=$options["editoptions"]["Path_Image"];
        return $options;

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
                "edit"=>[
                    "Images"=>[
                        "image_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                    ],
                ],
                "add"=>[
                    "Images"=>[
                        "image_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "database_table_name"=>""               //имя таблицы SQL куда вставляем новые записи (НЕ ФОТО)!, нужно для новых записей
                    ],
                ],
                "del"=>[
                    "Images"=>[
                        "image_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                    ],
                ],
            ],
            "formatter"=>"image",
            "classes"=>"jqgrid-img"
        ];
        foreach (["read","add","edit","del"] as $act){
            if (isset($options["plugins"][$act])){
                unset($def["plugins"][$act]);
            }
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
                "edit"=>[
                    "datetime"=>[
                        "toformat"=>"'Y-m-d H:i:s'",
                    ],
                ],
                "add"=>[
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
        foreach (["read","add","edit","del"] as $act){
            if (isset($options["plugins"][$act])){
                unset($def["plugins"][$act]);
            }
        }
        return ArrayUtils::merge($def,$options);

    }
    /**
    * вывод даты + виджет выбора
    *
    */
    public static function date(string $name, array $options=[])
    {
        $def=[/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "date",
            "editoptions" => [
                "dataInit"=>new Expr('function (el){$(el).datepicker({dateFormat:"dd.mm.yy"});}'),
                "defaultValue" =>new Expr('function(){var formatter = new Intl.DateTimeFormat("ru");return formatter.format(new Date());}'),
                "size" => 40,
            ],
            "plugins"=>[
                "edit"=>[
                    "datetime"=>[
                        "toformat"=>"'Y-m-d'",
                    ],
                ],
                "add"=>[
                    "datetime"=>[
                        "toformat"=>"'Y-m-d'",
                    ],
                ],
            ],
        ];
        foreach (["read","add","edit","del"] as $act){
            if (isset($options["plugins"][$act])){
                unset($def["plugins"][$act]);
            }
        }
        return ArrayUtils::merge($def,$options);

    }
    
}