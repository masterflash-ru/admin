Для автоматизации и упрощения редактирования информации используется понятие универсального интерфейса, представляет собой комплекс объектов для вывода
информации в виде линейных таблиц, древовидных структур и форм.


для создания нужного типа интерфейса используется конфигурационный файл, в котором описан тип интерфейса и его параметры.

## Сетка

Для генерации используется сетка JqGrid, ввод-вывод производится асинхронно
пример для табличного интерфейса.
```php

use Admin\Service\JqGrid\ColModelHelper;    //попомщник для создания колонок
use Admin\Service\JqGrid\NavGridHelper;     //помощник для создания средств навигации сетки
use Admin\Service\Zform\RowModelHelper;     //помощник для дополнения в шапке сетки форм
use Laminas\Json\Expr;                         //упаковка в формате json - для передачи функций JS


return [
        "type" => "ijqgrid",                                    //тип интерфейса
        "description"=>"Список товара",                         //пояснение, если пусто, в админке не выводится в списке интерфейсов
        "options" => [
            "container" => "catalog_tovar",                     //имя инетрфейса
            "caption" => "",                                    //текст перед всей таблицей
            "podval" => "",                                     //текст после всей таблицы
            
            "read"=>[                                           //плагины которые загружают данные в сетку
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from catalog_tovar",
                    "PrimaryKey"=>"id",
                ],
            ],

            "edit"=>[                                           //плагины которые записывают данные из сетки
                "cache" =>[
                    "tags"=>["catalog_tovar"],
                    "keys"=>["catalog_tovar"],
                ],

                "db"=>[
                    "sql"=>"select * from catalog_tovar",
                    "PrimaryKey"=>"id",
                ],

            ],
            "add"=>[                                            //плагины которые записывают данные при создании новой записи
                "db"=>[
                    "sql"=>"select * from catalog_tovar",
                    "PrimaryKey"=>"id",
                ],
            ],

            "del"=>[                                            //плагины удаляющие записи
                "cache" =>[
                    "tags"=>["catalog_tovar"],
                    "keys"=>["catalog_tovar"],
                ],
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from catalog_tovar",
                    "PrimaryKey"=>"id",
                ],
            ],

            "layout"=>[                                         //секция внешнего вида, см. опции jqGrid, можно передавать любые
                "caption" => "Список всего товара", 
                "height" => "auto",
                //"width" => "1100px",
                "rowNum" => 20,
                "rowList" => [20,50,100,500],
                "sortname" => "name",
                "sortorder" => "asc",
                "viewrecords" => true,
                "autoencode" => false,
                //"autowidth"=>true,
                "hidegrid" => false,
                "toppager" => true,
                "rownumbers" => false,
                
                
                "treeGrid"=>true,                               //группа опций для генерации дерева
                "ExpandColumn"=>"label",
                "ExpandColClick"=>true,
                "treeGridModel"=>"adjacency",
                "treeIcons"=>[
                    "plus"  =>"ui-icon-triangle-1-e",
                    "minus"=>"ui-icon-triangle-1-s",
                    "leaf"=>"ui-icon-bullet",
                ],
                "treeReader"  =>[                               //поля для генерации дерева
                    "parent_id_field" => "subid",
                    "level_field" => "level",
                ], 

                
                
                "navgrid" => [                                  //опции для вывода стандартных кнопок навигации/редактирования вначале и конце сетки
                    "button" => NavGridHelper::Button(["search"=>false,"edit"=>false,"add"=>false,"del"=>false]),
                    "editOptions"=>NavGridHelper::editOptions(),
                    "addOptions"=>NavGridHelper::addOptions(),
                    "delOptions"=>NavGridHelper::delOptions(),
                ],
                
                "navButtonAdd"=>[                               //опции пользовательских кнопок в начало/конец сетки
                    NavGridHelper::ButtonAdd(["caption"=>"Заголовок","title"=>"Добавить товар","onClickButton"=>new Expr('newTovar')]),
                    NavGridHelper::ButtonAdd(["caption"=>"Заголовок1","title"=>"Добавить товар1"]),
                ],
                
                
                                                                //область перед телом сетки, toolbar все настройки как в Zform
                "toolbar"=> [true,"top"],
                "toolbarModel"=>[
                    "rowModel" => [
                        'elements' => [
                            RowModelHelper::select("locale",[
                                "plugins"=>[
                                    "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                        "Locale"=>[],
                                    ],
                                ],
                                'options'=>[
                                    "label"=>"Локаль: "
                                ],
                                "attributes"=>["onchange"=>"change_toolbar()"]
                            ]),
                            RowModelHelper::select("sysname",[
                                "plugins"=>[
                                    "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                        Service\Admin\Zform\Plugin\MenuNames::class=>[],
                                    ],
                                ],
                                'options'=>[
                                    "label"=>"Имя меню: "
                                ],
                                    /*функция JS которая вызывается при изменении элемента*/
                                "attributes"=>["onchange"=>"change_toolbar()"]
                            ]),
                        ],
                    ],
                    "read"=>[//наолняет элементы toolbar начальными значениями
                        Service\Admin\Zform\Plugin\ToolBarInit::class=>[ ],
                    ],
                ],

                
                "colModel" => [                                //модели-описание колонок таблицы
                    ColModelHelper::text("id",["label"=>"ID","width"=>80]),
                    ColModelHelper::text("name",["label"=>"Название товара","width"=>300]),
                    ColModelHelper::text("url",["label"=>"URL карточки",
                        "width"=>250,
                       
                    ]),
                    ColModelHelper::text("poz",["label"=>"Порядок","width"=>70,"editable"=>false]),
                    ColModelHelper::checkbox("public",["label"=>"Публ.","width"=>50]),
                    ColModelHelper::interfaces("id",
                                         [
                                             "label"=>"Редактировать",
                                             "width"=>160,
                                             "formatoptions" => [
                                                 "items"=>[
                                                    "button1"=> [
                                                        "label"=>"Подробности",
                                                        "interface"=>"/adm/universal-interface/tovar_detal",
                                                        "icon"=> "ui-icon-contact",
                                                        "dialog"=>[
                                                            "title"=>"Подробности",
                                                            "resizable"=>true,
                                                            "closeOnEscape"=>true,
                                                            "width"=>"680",
                                                            "position"=>[
                                                                "my"=>"left top",
                                                                "at"=>"left top",
                                                                "of"=>"#contant-container"
                                                            ],

                                                        ],
                                                     ],
                                                 ],
                                             ]
                                         ]),
                    
                    ColModelHelper::jscellActions("myaction",["formatoptions"=>["onEdit"=>"editTovar"]]),
                ],
            ],
        ],
];
```
Для работы с данными используются плагины, они могут быть 2-х типов: для всей сетки или для отдельной колонки. Можно создать универсальный плагин в виде одного файла
для конкретноой операции вызывается нужный метод.
Стандартные плагины собраты в менеджер плагинов, однако можно добавлять свои в конфиге приложения:
```php
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
            Service\Admin\JqGrid\Plugin\GetAdminUrls::class=>Service\Admin\JqGrid\Plugin\FactoryGetAdminUrls::class,
        ],
        'aliases' =>[
            "GetAdminUrls"=>Service\Admin\JqGrid\Plugin\GetAdminUrls::class,
        ],
    ],
```
Плагины оформляются как обычные объекты в Laminas, с фабриками и т.д.


## Формы

```php
use Admin\Service\Zform\RowModelHelper;                     //помощник элементов


return [

        "type" => "izform",                                 //тип Zform
        //"description"=>"",                                //если пусто, тогда не выводится в адмиинке в общем списке
        "options" => [
            "container" => "profile",                       //имя интерфейса
            "podval" =>"",
            "container-attr"=>"style='width:500px'",
        
            "read"=>[                                       //плагины загрузки начальных данных в форму
                "db"=>[
                    "sql"=>"select users.*, 
                        (select group_concat(users_group) from users2group where users=users.id) as gr 
                            from users where id=:id",  
                ],
            ],
            "edit"=>[                                       //плагины для записи данных
                "EditUserProfile"=>[],
            ],
            
            "actionsEvent"=>[                               //события формы, FormAfterSubmitOkEvent - успешная запись
                "FormAfterSubmitOkEvent"=>'$("#usersall").trigger("reloadGrid");setTimeout(function(){$("#interfacesDialog").dialog("close");},500);',
            ],

            "layout"=>[                                     //внешний вид, аналогично сетке
                "caption" => "Базовый профиль",
                "rowModel" => [                             //модели-описание элементов формы
                    'elements' => [
                        RowModelHelper::text("login",['options'=>["label"=>"Логин"]]),
                        RowModelHelper::text("name",['options'=>["label"=>"Имя"]]),
                        RowModelHelper::text("full_name",['options'=>["label"=>"Полное имя"]]),
                        RowModelHelper::select("status",[
                            "plugins"=>[
                                "rowModel"=>[               //плагин срабатывает при генерации формы до ее вывода
                                    "GetUserStatus"=>[],
                                ],
                            ],
                            'options'=>[
                                "label"=>"Статус"
                            ],
                        ]),
                        RowModelHelper::datetime("date_registration",['options'=>["label"=>"Дата регистрации"]]),
                        RowModelHelper::multicheckbox("gr",[
                            "plugins"=>[
                                "rowModel"=>[               //плагин срабатывает при генерации формы до ее вывода
                                    "selectfromdb"=>[
                                        "sql"=>"select id,name from users_group order by name",
                                        "emptyFirstItem"=>false
                                    ],
                                ],
                                "read"=>[                   //конвертирует строку со списком ID групп в массив
                                    "StringToArray"=>[]
                                ],
                            ],
                            'options'=>[
                                "label"=>"Член групп",
                                
                            ],
                        ]),
                        RowModelHelper::submit("submit",[
                            'attributes'=>['value' => 'Записать'],
                        ]),
                        //это ID юзера
                        RowModelHelper::hidden("id"),
                    ],

                ],
            ],
        ],
];
```

Аналогично сетке используются плагины для работы с данными, для добавления сових используется конфиг приложения:
```php
    /*плагины для Zform*/
    "ZformPlugin"=>[
        'factories' => [
            Service\Admin\Zform\Plugin\GetUserStatus::class=>Service\Admin\Zform\Plugin\FactoryGetUserStatus::class,
            Service\Admin\Zform\Plugin\EditUserProfile::class=>Service\Admin\Zform\Plugin\FactoryEditUserProfile::class,
            Service\Admin\Zform\Plugin\EditUserPassword::class=>Service\Admin\Zform\Plugin\FactoryEditUserProfile::class,
        ],
        'aliases' =>[
            "GetUserStatus" => Service\Admin\Zform\Plugin\GetUserStatus::class,
            "EditUserProfile" => Service\Admin\Zform\Plugin\EditUserProfile::class,
            "EditUserPassword" => Service\Admin\Zform\Plugin\EditUserPassword::class,
        ],
    ],
```

