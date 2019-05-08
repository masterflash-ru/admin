<?php
namespace Admin;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Меню админ панели",
        "options" => [
            "container" => "admin_menu",
            "podval" =>"",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select t.*,
                        (not EXISTS(select id from admin_menu as st where st.subid=t.id)) as isLeaf
                            from admin_menu as t where subid=:nodeid",
                ],
            ],
             "edit1"=>[
                 "SaveUser"=>[]
             ],
             "add1"=>[
                 "SaveUser"=>[]
             ],
             "del1"=>[
                 "SaveUser"=>[]
             ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Меню админ панели",
                "height" => "auto",
                //"width" => "auto",
                "sortname" => "id",
                "sortorder" => "asc",
                "hidegrid" => false,
                "treeGrid"=>true,
                "ExpandColumn"=>"name",
                "ExpandColClick"=>true,
                "treeIcons"=>[
                    "plus"  =>"ui-icon-triangle-1-e",
                    "minus"=>"ui-icon-triangle-1-s",
                    "leaf"=>"ui-icon-bullet",
                ],
                "navgrid" => [
                    "button" => NavGridHelper::Button(["search"=>false,"add"=>true,"edit"=>true,"del"=>true]),
                    "editOptions"=>NavGridHelper::editOptions(),
                    "addOptions"=>NavGridHelper::addOptions(),
                    "delOptions"=>NavGridHelper::delOptions(),

                ],
                "colModel" => [
                    ColModelHelper::hidden("id"),
                    ColModelHelper::text("name",
                                         [
                                             "label"=>"Имя",
                                             "width"=>250,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::select("url",["label"=>"Переход",
                                                  "width"=>"500",
                                                  "plugins"=>[
                                                      "colModel"=>["GetAdminUrls"=>[]],
                                                      "ajaxRead"=>["GetAdminUrls"=>[]],
                                                      ],
                                                  ]),
                    
                ],
            ],
        ],
];