<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы ColModel
* функции возвращают массив пригодный для 
* добавления в конфиг ColModel
*/
namespace Admin\Service;

use Zend\Stdlib\ArrayUtils;
use Zend\Json\Expr;


/*"gridComplete"=>new Expr("function (){alert(123);}"),*/

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
                "dataInit"=>new Expr('function (el){$(el).ckeditor();}'),
            ],
            "editrules"=>[
                "edithidden"=>true,
            ],
        ],$options);
    }
    
    /**
    * вывод даты-времени + виджет выбора
    * 
    */
    public static function datetime(string $name, array $options=[])
    {
        return ArrayUtils::merge([/*формат дата + выбор даты*/
            "name" => $name,
            "editable" => true,
            "edittype" => "text",
            "formatter" => "datetime",
            "editoptions" => [
                "dataInit"=>new Expr('function (el){$(el).datetimepicker({timeInput: true,timeFormat: "HH:mm:ss",dateFormat:"dd.mm.yy"});}'),
                "defaultValue" =>new Expr('function(){var formatter = new Intl.DateTimeFormat("ru",{day:"numeric",year:"numeric",month:"numeric",hour: "numeric",minute: "numeric",second: "numeric"});return formatter.format(new Date()).replace(",","");}'),
                "size" => 50,
            ],
        ],$options);
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