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
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Text::class,
            'name' => $name,
            'options' => [
                'label' => '',
            ]
        ],$options)];
    }
    /**
    * списка
    */
    public static function select(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Select::class,
            'name' => $name,
            'options' => [
                'label' => '',
            ],
        ],$options)];
    }
    /**
    * вывод кнопки submit
    */
    public static function submit(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Submit::class,
            'name' => $name,
            'attributes' => [
                'value' => 'Submit',
                "class"=>"btn btn-primary btn-sm",
            ],
        ],$options)];
    }

}