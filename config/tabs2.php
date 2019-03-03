<?php


return [
        /*TABS вкладки*/
        "type" => "itabs",
        "description"=>"Описание интерфейса",
        "options" => [
            "container" => "my2",
            "caption" => "<h1>Это заголовок перед всем111</h1>",
            "podval" => "Это информация в конце интерфейса111",
            "tabs"=>[
                [
                    "label"=>"элемент 1",
                    "interface"=>"grid"
                ],
            ],
        ],
];