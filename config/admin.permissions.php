<?php
namespace Admin;

use Admin\Service\JqGrid\ColModelHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Таблица доступов",
        "options" => [
            "container" => "acl",
            "podval" =>"<br/><b>Формат данных:<br>Владелец:Группа код_доступа в восьмеричной системе аналогично UNIX</b>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*редактирование*/
            "edit"=>[
                "cache" =>[
                    "tags"=>["permissions"],
                    "keys"=>["permissions"],
                ],
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            "add"=>[
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            //удаление записи
            "del"=>[
                "cache" =>[
                    "tags"=>["permissions"],
                    "keys"=>["permissions"],
                ],
                "db"=>[ 
                    "sql"=>"select * from permissions",
                    "PrimaryKey"=>"id",
                ],
            ],
            /*внешний вид*/
            "layout"=>[
                "caption" => "Таблица доступов",
                "height" => "auto",
                "width" => 1000,
                "rowNum" => 20,
                "rowList" => [10,30,100],
                "sortname" => "name",
                "sortorder" => "asc",
                "viewrecords" => true,
                "autoencode" => true,
                //"autowidth"=>true,
                "hidegrid" => false,
                "toppager" => true,
                "rownumbers" => false,
                "navgrid" => [
                    "button" => [
                        "edit" => true,
                        "add" => true,
                        "del" => true, 
                        "view" => false,
                        "cloneToTop" => true,
                        "search" => true,
                    ],
                ],
                "colModel" => [
                    ColModelHelper::text("name",["label"=>"Имя объекта","width"=>400]),
                    ColModelHelper::text("object",["label"=>"Объект","width"=>400]),
                    ColModelHelper::permissions("mode",["label"=>"Доступ","width"=>400]),
                    ColModelHelper::cellActions(),
                ],
            ],
        ],
];