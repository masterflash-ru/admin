<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы ColModel
* функции возвращают массив пригодный для 
* добавления в конфиг ColModel
*/
namespace Admin\Service;

use Zend\Stdlib\ArrayUtils;

class GqGridColModelHelper
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
    * в сетке он скрыт
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
    * в сетке он скрыт
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
                "dataInit" => "ckeditor",
            ],
            "editrules"=>[
                "edithidden"=>true,
            ],
        ],$options);
    }
    
    /**
    * вывод даты-времени + виджет выбора
    * в сетке он скрыт
    */
    public static function datetime(string $name, array $options=[])
    {
        return ArrayUtils::merge([/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "datetime", /*date*/
            "editoptions" => [
                "dataInit" => "datetimepicker",
                "defaultValue" => "defaultdatetime",
                "size" => 50,
            ],
        ],$options);
    }
    /**
    * вывод даты + виджет выбора
    * в сетке он скрыт
    */
    public static function date(string $name, array $options=[])
    {
        return ArrayUtils::merge([/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "date",
            "editoptions" => [
                "dataInit" => "datepicker",
                "defaultValue" => "defaultdate",
                "size" => 50,
            ],
        ],$options);
    }
    
}