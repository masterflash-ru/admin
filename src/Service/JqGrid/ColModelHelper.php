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
                "editOptions"=>[
                    "closeOnEscape"=>true,
                    "width"=>"auto",
                ],
                "delOptions"=>[
                    
                ],
            ],
        ],$options);
    }


    /**
    * вывод ссылки для перехода
    * 
    */
    public static function showLink(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            "name" => $name,
            "formatter"=>'showlink',
            "label"=>"Фотогалерея",
            "formatoptions" =>[
                "baseLinkUrl" => '/adm/universal-interface/',
                "showAction" => '',
                "addParam" => '',
                "idName" => 'id',
                "target" => '_blank'
            ],
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
    * вывод выпадающего списка статично через модель колонки
    * массив данных читается и передается прямо в сетку штатным форматтером
    * есть недостаток, данные не меняются пока не будет перезагружена страница
    */
    public static function select(string $name, array $options=[])
    {
        $def=[
            "name" => $name,
            "editable" => true,
            "edittype" => "select",
            "editoptions"=>[
                "value"=>[]
            ],
            "formatter"=>"select",
        ];
        //опции для чтения данных с сервера при редактировании
        $def_editoptions=[
            "dataUrl"=>null,
            "cacheUrlData"=>false,
        ];

        $options=ArrayUtils::merge($def,$options);
        
        if (isset($options["plugins"]["ajaxRead"])){
            foreach ($options["plugins"]["ajaxRead"] as $plugin_alias=>$plugin_options){
                $plugin_options=ArrayUtils::merge($def_editoptions,$plugin_options);
                $plugin_options["dataUrl"]="/adm/io-jqgrid-plugin/".$plugin_alias;
                $plugin_options["buildSelect"]=new Expr("buildSelect");
            }
            $options["editoptions"]=ArrayUtils::merge($options["editoptions"],$plugin_options);
            unset($options["plugins"]["ajaxRead"]);
        }
        return $options;
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
    * вывод файла из хранилища по ID
    * 
    */
    public static function files(string $name, array $options=[])
    {
        $def=[
           "name" => $name,
            "editable" => true,         
            "edittype"=>"custom",
            "editoptions"=>[
                "custom_element"=>new Expr('fileEdit'),
                "custom_value"=>new Expr('fileSave'),

            ],
            "plugins"=>[
                "read"=>[
                    "Files" =>[
                        "file_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "storage_item_rule_name"=>""            //имя элемента из секции хранилища
                    ],
                ],
                "edit"=>[
                    "Files"=>[
                        "file_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "storage_item_rule_name"=>""            //имя элемента из секции хранилища
                    ],
                ],
                "add"=>[
                    "Files"=>[
                        "file_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "database_table_name"=>"",              //имя таблицы SQL куда вставляем новые записи (НЕ ФОТО)!, нужно для новых записей
                        "storage_item_rule_name"=>""            //имя элемента из секции хранилища
                    ],
                ],
                "del"=>[
                    "Files"=>[
                        "file_id"=>"id",                        //имя поля с ID
                        "storage_item_name" => "",              //имя секции в хранилище
                        "storage_item_rule_name"=>""            //имя элемента из секции хранилища
                    ],
                ],
            ],
           // "formatter"=>"file",
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
                        "toformat"=>"Y-m-d H:i:s",
                    ],
                ],
                "add"=>[
                    "datetime"=>[
                        "toformat"=>"Y-m-d H:i:s",
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
                        "toformat"=>"Y-m-d",
                    ],
                ],
                "add"=>[
                    "datetime"=>[
                        "toformat"=>"Y-m-d",
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

    /**
    * вывод кодов доступов
    * 
    */
    public static function permissions(string $name, array $options=[])
    {
        $def=[
            "name" => $name,
            "formatter" => "permissions",
            "plugins"=>[
                "read"=>[
                    "Permissions" =>[],
                ],
                "edit"=>[
                    "Permissions" =>[],
                ],
                "add"=>[
                    "Permissions" =>[],
                ],
                "colModel"=>[//плагин срабатывает при генерации сетки, вызывается в помощнике сетки
                    "Permissions"=>[]
                ]
            ],
            "editable" => true,
            "edittype"=>"custom",
            "editoptions"=>[
                "custom_element"=>new Expr('permissionsEdit'),
                "custom_value"=>new Expr('permissionsSave'),
            ],
        ];
        foreach (["read","add","edit","colModel"] as $act){
            if (isset($options["plugins"][$act])){
                unset($def["plugins"][$act]);
            }
        }
        return ArrayUtils::merge($def,$options);

    }

    /**
    * вывод настроек SEO
    * 
    */
    public static function seo(string $name, array $options=[])
    {
        $def=[
            "name" => $name,
            "width"=>250,
            "formatter" => "seo",
            "editable" => true,
            "edittype"=>"custom",
            "editoptions"=>[
                "custom_element"=>new Expr('seoEdit'),
                "custom_value"=>new Expr('seoSave'),
            ],
        ];
        return ArrayUtils::merge($def,$options);

    }
    /**
    * вывод кнопок для перехода к другому интерфейсу
    * 
    */
    public static function interfaces(string $name, array $options=[])
    {
        $def=[
            "name" => $name,
            "width"=>500,
            "formatter" => "interfaces",
            "editable" => false,
            "formatoptions" => [
                "items"=>[
                    "button1"=> [
                        "label"=>"Кнопка 1",
                        "interface"=>"/adm/universal-interface/usergroups",
                        "get_parameter_name"=>"id",
                        "icon"=> "ui-icon-heart",
                        "classes"=>[ "ui-button"=>""],
                        "dialog"=>[
                            "modal"=>true,
                            "resizable"=>true,
                            "closeOnEscape"=>true,
                            "title"=>"Заголовок окна",
                            "width"=>"auto",
                            "position"=>[
                                "my"=>"left top",
                                "at"=>"left top",
                                "of"=>"#contant-container"
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return ArrayUtils::merge($def,$options);

    }

}