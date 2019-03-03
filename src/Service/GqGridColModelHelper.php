<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы ColModel
* функции возвращают массив пригодный для 
* добавления в конфиг ColModel
*/
namespace Admin\Service;

class GqGridColModelHelper
{
    
    
    
    
    /**
    * посточные кнопки действия
    */
    public static function cellActions(string $name="myactions", array $options=[])
    {
        return  [
            "name" => $name,
            "width"=>80,
            "formatter" => "actions",
            "sortable"=>false,
            "formatoptions" => [
                "keys" => true,
            ]
        ];
    }
    
    
    /**
    * вывод редактора ckeditor
    * в сетке он скрыт
    */
    public static function ckeditor(string $name, array $options=[])
    {
        return [
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
        ];
    }
    
}