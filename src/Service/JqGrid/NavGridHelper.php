<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы NavGridHelper
* функции возвращают массив пригодный для прямой передачи в сетку
*/
namespace Admin\Service\JqGrid;

use Laminas\Stdlib\ArrayUtils;
use Laminas\Json\Expr;



class NavGridHelper
{
    /**
    * кнопки пользователя в панель сетки
    * вызывает функцию JS:
jqgridnavGrid.navButtonAdd('#<?=$options["container"]?>_pager',
                {
                    caption:"",
                    buttonicon:"ui-icon-locked",
                    onClickButton:function(){$("#dialog_permission").dialog("open");},
                    position: "last",
                    title:"Доступ к интерфейсу",
                    id : "",
                    cursor: "pointer"
                });

    */
    public static function ButtonAdd( array $options=[])
    {
        return  ArrayUtils::merge([
            "caption" => "",
            "buttonicon"=>"",
            "onClickButton" =>"function(){}",
            "position"=>"last",
            "title" => "", 
            "id"=>"",
            "cursor" => "pointer",
        ],$options);
    }

    /**
    * кнопки действия
    */
    public static function Button( array $options=[])
    {
        return  ArrayUtils::merge([
            "edit" => true,
            "edittext"=>"",
            "add" => true,
            "addtext"=>"",
            "del" => true, 
            "deltext"=>"",
            "view" => false,
            "viewtext" => "",
            "cloneToTop" => true,
            "search" => true,
            "searchtext"=>"",
            "refresh"=>false,
            "refreshtext"=>"",
            "closeOnEscape"=>true
        ],$options);
    }

    /**
    * опции для редактирования
    */
    public static function editOptions( array $options=[])
    {
        return  ArrayUtils::merge([
            "width"=>"auto",
            "closeOnEscape"=>true,
            "closeAfterEdit"=> true,
            "recreateForm"=> true
        ],$options);
    }
    /**
    * опции для добавления
    */
    public static function addOptions( array $options=[])
    {
        return  ArrayUtils::merge([
            "width"=>"auto",
            "closeOnEscape"=>true,
            "closeAfterEdit"=> true,
            "recreateForm"=> true
        ],$options);
    }
    /**
    * опции для удаления
    */
    public static function delOptions( array $options=[])
    {
        return  ArrayUtils::merge([
            "closeOnEscape"=>true,
            "recreateForm"=> true
        ],$options);
    }

    /**
    * опции для просмотра в окне
    */
    public static function viewOptions( array $options=[])
    {
        return  ArrayUtils::merge([
            "closeOnEscape"=>true,
            "width"=>"auto"
        ],$options);
    }

    /**
    * опции для окошка поиска
    */
    public static function searchOptions( array $options=[])
    {
        return  ArrayUtils::merge([
            "multipleSearch"=>false,
        ],$options);
    }

}