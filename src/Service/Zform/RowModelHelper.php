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

//use Admin\Service\Zform\Element as myElement;

class RowModelHelper
{
    protected static $DynamicArray=0;
    protected static $Caption=0;
     /**
    * вывод текущего изображения
    */
    public static function uploadimage(string $name, array $options=[])
    {
        return [
            'spec' =>ArrayUtils::merge([
                'type' => 'uploadImg',
                'name' => $name,
                'options' => [
                    'label' => '',
                ],
                'attributes' => [
                ],
                "plugins"=>[
                    "read"=>[
                        "Images" =>[
                            "storage_item_name" => "",              //имя секции в хранилище
                            "storage_item_rule_name"=>"admin_img",  //имя правила из хранилища
                            "image_id"=>"id"                        //в этой операции не используется
                        ],
                    ],
                    "edit"=>[
                        "Images"=>[
                            "storage_item_name" => "",              //имя секции в хранилище
                            "image_id"=>"id"                        //из какого поля брать ID записи для записи в хранилище
                        ],
                    ],
                    /*"add"=>[
                        "Images"=>[
                            "storage_item_name" => "",              //имя секции в хранилище
                            "database_table_name"=>""               //имя таблицы SQL куда вставляем новые записи (НЕ ФОТО)!, нужно для новых записей
                        ],
                    ],
                    "del"=>[
                        "Images"=>[
                            "storage_item_name" => "",              //имя секции в хранилище
                        ],
                    ],*/
                ],

            ],$options)
        ];
    }

    
    
    
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
        return [
            'spec' =>ArrayUtils::merge([
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
    * вывод многострочного эл-та
    */
    public static function textarea(string $name, array $options=[])
    {
        return [
            'spec' =>ArrayUtils::merge([
                'type' => Element\Textarea::class,
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
    * вывод разделительного заголовка, посредством hiden
    * при генерации формы идет подмена
    */
    public static function caption(string $name=null, array $options=[])
    {
        static::$Caption++;
        if (empty($name)){
            $name="Caption".static::$Caption;
        }

        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Hidden::class,
            'name' => $name,
            'attributes' => [
                "change"=>"caption",
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
            //'type' => Element\Text::class,
            /*'options' => [
                    'label' => 'HIDDEN:',
                ],*/

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
    * Radio
    */
    public static function Radio(string $name, array $options=[])
    {
        return ['spec' =>ArrayUtils::merge([
            'type' => Element\Radio::class,
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
            'attributes' => [
                'id' => $name,
            ],
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
    
    /**
    * вывод массива динамических полей, вид из допустимых Zend
    *  $name - имя группы, если пусто - внутреннее (пока нигде не используется)
    */
    public static function DynamicArray(string $name=null, array $options=[])
    {
        static::$DynamicArray++;
        if (empty($name)){
            $name="DynamicArray".static::$DynamicArray;
        }
        
        return [
            'spec' =>ArrayUtils::merge([
                'type' => "DynamicArray",
                'name' => $name,
                'fields' => [
                    //статичный массив, вроде такого:
                    //RowModelHelper::text("xml_id",['options'=>["label"=>"xml_id"]]),
                    //RowModelHelper::text("xml_id111",['options'=>["label"=>"xml_id111"]]),
                ],
                "plugins"=>[
                ],
        ],$options)];
    }

}