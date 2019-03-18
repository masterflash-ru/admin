<?php
/*
* Помощник конфигурации jqGrid 
* помогает формировать элементы NavGridHelper
* функции возвращают массив пригодный для прямой передачи в сетку
*/
namespace Admin\Service\JqGrid;

use Zend\Stdlib\ArrayUtils;
use Zend\Json\Expr;



class NavGridHelper
{

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