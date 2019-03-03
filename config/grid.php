<?php
namespace Admin;

use Admin\Service\GqGridColModelHelper;

return [
        /*jqgrid - сетка*/
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
                "colModel" => [
                    [
                        "id" => "id",
                        "hidden" => true,
                        "name" => "id",
                        "editable" => false,
                        "key"=>true
                    ],
                    GqGridColModelHelper::text("caption",["label"=>"Заголовок"]),
                    GqGridColModelHelper::datetime("date_public",["label"=>"Дата публикации"]),
                    GqGridColModelHelper::checkbox("public"),

                    /*[/*выпадающий список* /
                        "name" => "public",
                        "editable" => true,
                        "edittype" => "select",
                        "editoptions"=>[
                            "value"=>[1=>"Да",0=>"Нет"],/*значение выпадающего списка Фиксировано* /
                            //"dataUrl"=>"/adm/ddddd"
                        ],
                        "formatter"=>"select",
                    ],*/
                    
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