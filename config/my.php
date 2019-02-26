<?php


return [
    "main" => [
        "name" => "interface_name",
        "caption" => "<h1>Это заголовок перед всем</h1>",
        "podval" => "Это информация в конце интерфейса",
        "container" => "my",
        "type" => [
            "jpgrid"=>"jpgrid_type"
        ],
    ],
    "jpgrid_type" => [
        "container" => "myjpgrid",
        /*чтение опции*/
        "read"=>[
            "db"=>[ /*SQL выборка*/
                "sql"=>"select * from news",
                "options"=>[
                    "PrimaryKey"=>"id"
                ],
            ],
            
        ],
        
        
        
        "caption" => "Это заголовок грида",
        "height" => "auto",
        "width" => 1000,
        "rowNum" => 10,
        "rowList" => [10,20,50],
        "sortname" => "id",
        "sortorder" => "desc",
        "viewrecords" => true,
        "autoencode" => true,
        "hidegrid" => false,
        "toppager" => true,
        "multiselect" => true,
        "rownumbers" => false,
        "navgrid" => [
            "button" => [
                "edit" => true,
                "add" => true,
                "del" => true,
                "cloneToTop" => true,
            ],
        ],
        "colNames" => [
            "id",
            "заголовок",
            "дата публикации",
            "операция",
        ],
        "colModel" => [
            [
                "id" => "id",
                "hidden" => false,
                "name" => "id",
                "width" => 200,
                "editable" => false,
            ],
            [
                "id" => "caption",
                "hidden" => false,
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
            ],
            [
                "id" => "date_public",
                "hidden" => false,
                "name" => "date_public",
                "width" => 200,
                "editable" => true,
                "edittype" => true,
                "formatter" => "date",
                "editoptions" => [
                    "dataInit" => "datetimepicker",
                    "defaultValue" => "defaultdatetime",
                    "size" => 50,
                ],
            ],
            [
                "id" => "myac",
                "name" => "myac",
                "formatter" => "actions",
                "sortable"=>false,
                "formatoptions" => [
                    "keys" => true,
                ]
            ],
        ],
    ],
];