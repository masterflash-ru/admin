<?php
namespace Admin;

use Admin\Service\GqGridColModelHelper;

return [
        /*TABS вкладки*/
        "type" => "ijqgrid",
        "description"=>"Описание интерфейса",
        "options" => [
            "container" => "my1",
            "caption" => "<h1>Это заголовок перед всем111</h1>",
            "podval" => "Это информация в конце интерфейса111",
            
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "adapter"=>"db", /*SQL выборка*/
                "options"=>[ 
                    "sql"=>"select * from news",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*все что касается записи*/
            "write"=>[
                "adapter"=>"db", /*SQL выборка*/
                "options"=>[ 
                    "sql"=>"select * from news limit 1",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*внешний вид*/
            "layout"=>[
                "caption" => "Это заголовок грида",
                "height" => "auto",
                "width" => 1000,
                "rowNum" => 10,
                "rowList" => [10,20,50],
                //"sortname" => "id",
                //"sortorder" => "desc",
                "viewrecords" => true,
                "autoencode" => true,
               // "hidegrid" => false,
                "toppager" => true,
               // "multiselect" => true,
                "rownumbers" => false,
                "navgrid" => [
                    "button" => [
                        "edit" => true,
                        "add" => true,
                        "del" => true, 
                        "view" => true,
                        "cloneToTop" => true,
                    ],
                ],
                "colNames" => [
                    "id",
                    "заголовок",
                    "дата публикации",
                    "публ.",
                    "Полная новость",
                    "Другой интерфейс",
                    "операция",
                ],
                "colModel" => [
                    [
                        "id" => "id",
                        "hidden" => true,
                        "name" => "id",
                        "editable" => false,
                        "key"=>true
                    ],
                    [
                        "name" => "caption",
                        "width" => 200,
                        "editable" => true,
                        "edittype" => "text",
                        "formoptions" => [
                            // "rowpos" => 2,
                            // "colpos" => true,
                            "elmprefix" => "*",
                            "elmsuffix" =>"" ,
                        ],
                        "editoptions" => [
                            "size" => 80,
                        ],
                        "editrules"=>[
                            "required"=>true,
                        ],
                    ],
                    [/*формат дата + выбор даты*/
                        "name" => "date_public",
                        "editable" => true,
                        "edittype" => "text",
                        "formatter" => "datetime", /*date*/
                        "editoptions" => [
                            "dataInit" => "datetimepicker",
                            "defaultValue" => "defaultdatetime",
                            "size" => 50,
                        ],
                    ],
                    /*[/*флажок* /
                        "name" => "public",
                        "editable" => true,
                        "edittype" => "checkbox",
                        "editoptions"=>["value"=>"1:0"],/*значение флажка (установлен-сброшен)* /
                        "formatter"=>"checkbox",
                    ],*/
                    [/*выпадающий список*/
                        "name" => "public",
                        "editable" => true,
                        "edittype" => "select",
                        "editoptions"=>[
                            "value"=>[1=>"Да",0=>"Нет"],/*значение выпадающего списка Фиксировано*/
                            //"dataUrl"=>"/adm/ddddd"
                        ],
                        "formatter"=>"select",
                    ],
                    
                    GqGridColModelHelper::ckeditor("full_news"),
                  
                    
                    [/*Кнопки перехода на другой интерфейс*/
                        "name" => "counter",
                        "editable" => false,
                        //"formatter" => "interface",
                        "interfaces"=>[
                            "sysname1",
                            "sysname2"
                        ]
                    ],

                    GqGridColModelHelper::cellActions(),
                    
                
                ],
            ],
        ],
];