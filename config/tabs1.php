<?php


return [
        /*TABS вкладки*/
        "type" => "itabs",
        "description"=>"Описание интерфейса",
        "options" => [
            "container" => "my1",
            "caption" => "<h1>Это заголовок перед всем</h1>",
            "podval" => "Это информация в конце интерфейса",
            "tabs"=>[
                [
                    "label"=>"элемент 1",
                    "interface"=>"tabs2"
                ],
            ],
        ],
];