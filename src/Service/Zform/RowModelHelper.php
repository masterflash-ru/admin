<?php
/*
* Помощник конфигурации Zform
* помогает формировать элементы RowModel
* функции возвращают массив пригодный для 
* добавления в конфиг RowModel
*/
namespace Admin\Service\Zform;

use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container;
use Zend\Form\Element;
use Zend\Validator\Hostname;


class RowModelHelper
{
    /**
    * вывод однострочного эл-та ввода даты и времени
    */
    public static function datetime(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Text::class,
            'name' => $name,
            'options' => [
                'label' => '',
            ],
            'attributes' => [
                "class"=>"dtpicker form-control form-control-sm",
            ],
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
                "read"=>[
                    "datetime"=>[
                        "toformat"=>"d.m.Y H:i:s",
                    ],
                ],
            ],


        ],$options)];
    }
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
                
            ],
            'attributes' => [
                "class"=>"form-control form-control-sm",
            ],

        ],$options)];
    }
    
     /**
    * вывод многострочного эл-та + ckeditor
    */
    public static function ckeditor(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Textarea::class,
            'name' => $name,
            'options' => [
                'label' => '',
                
            ],
            'attributes' => [
                "class"=>"ckeditor",
            ],

        ],$options)];
    }
   
    
    
    
    /**
    * вывод скрытого эл-та
    */
    public static function hidden(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Hidden::class,
            'name' => $name,
        ],$options)];
    }

    /**
    * MultiCheckbox
    */
    public static function MultiCheckbox(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\MultiCheckbox::class,
            'name' => $name,
            'options' => [
                'label' => '',
                "value_options"=>[],
            ],
        
        ],$options)];
    }

    /**
    * Checkbox
    */
    public static function Checkbox(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Checkbox::class,
            'name' => $name,
            'options' => [
                'label' => '',
                "value_options"=>[],
            ],
        
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
                "value_options"=>[],
               // "empty_option"=>"Выберите",
            ],
            'attributes' => [
                "class"=>"form-control form-control-sm",
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
    /**
    * вывод кнопки button
    */
    public static function button(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Button::class,
            'name' => $name,
            'attributes' => [
                'value' => 'button',
                "class"=>"btn btn-primary btn-sm",
            ],
        ],$options)];
    }

}