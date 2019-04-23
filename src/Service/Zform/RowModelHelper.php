<?php
/*
* Помощник конфигурации Zform
* помогает формировать элементы RowModel
* функции возвращают массив пригодный для 
* добавления в конфиг RowModel
*/
namespace Admin\Service\Zform;

use Zend\Stdlib\ArrayUtils;
//use Zend\Json\Expr;
//use Zend\Session\Container;
use Zend\Form\Element;
use Zend\Validator\Hostname;


class RowModelHelper
{

    /**
    * вывод однострочного эл-та
    */
    public static function text(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            'spec' => [
                'type' => Element\Text::class,
                'name' => $name,
                'options' => [
                    'label' => '',
                ]
            ],
        ],$options);
    }
    /**
    * списка
    */
    public static function select(string $name, array $options=[])
    {
        return ArrayUtils::merge([
            'spec' => [
                'type' => Element\Select::class,
                'name' => $name,
                'options' => [
                    'label' => '',
                ]
            ],
        ],$options);
    }

}