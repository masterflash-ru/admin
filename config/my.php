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
                "sql"=>"select * from users",
                "options"=>[
                    
                ],
            ],
            
        ],
        
        
        
        "caption" => "Это заголовок грида",
        "height" => "auto",
        "width" => 1000,
        "rowNum" => 10,
        "rowList" => [10,20,50],
        "sortname" => "id",
       // "sortorder" => "desc",
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
            "имя",
            "дата регистрации",
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
                "id" => "name",
                "hidden" => false,
                "name" => "name",
                "width" => 200,
                "editable" => true,
                "edittype" => "textarea",
                "formoptions" => [
                    "rowpos" => 2,
                    "colpos" => true,
                    "elmprefix" => "*",
                    "elmsuffix" =>"" ,
                    "label" => "Новый текст",
                ],
            ],
            [
                "id" => "date_registration",
                "hidden" => false,
                "name" => "date_registration",
                "width" => 200,
                "editable" => true,
                "edittype" => true,
                "formatter" => "date",
                "editoptions" => [
                    "dataInit" => "datepicker",
                    "defaultValue" => "defaultdatetime",
                    "size" => 50,
                ],
            ],
            [
                "id" => "myac",
                "name" => "myac",
                "formatter" => "actions",
                "formatoptions" => [
                    "keys" => true,
                ]
            ],
        ],
    ],
];