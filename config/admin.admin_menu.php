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
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select t.*,
                        (not EXISTS(select id from admin_menu as st where st.subid=t.id)) as isLeaf
                            from admin_menu as t where subid=:nodeid",
                    "interface_name"=>"admin_menu",
                ],
            ],
             "edit"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from admin_menu",
                    "interface_name"=>"admin_menu",
                ],
                "cache" =>[
                    "tags"=>["admin_menu"],
                    "keys"=>["admin_menu"],
                ],
             ],
             "add"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from admin_menu",
                    "parent_id_field" => "subid",
                    "interface_name"=>"admin_menu",
                ],
                "cache" =>[
                    "tags"=>["admin_menu"],
                    "keys"=>["admin_menu"],
                ],
             ],
             "del"=>[
                "TreeAdjacency"=>[//плагин выборки из базы
                    "sql"=>"select * from admin_menu",
                    "interface_name"=>"admin_menu",
                ],
                "cache" =>[
                    "tags"=>["admin_menu"],
                    "keys"=>["admin_menu"],
                ],
             ],
            
            /*события, создаются в виде 
            $("#<?=$options["container"]?>").bind("jqGridAddEditAfterSubmit", function () {  });
            */
            "bind"=>[
              "jqGridAddEditAfterSubmit"=>new Expr("function () {print_admin_menu()}"),
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
               "treeGridModel"=>"adjacency",
                "gridview"=>false,
                "treeIcons"=>[
                    "plus"  =>"ui-icon-triangle-1-e",
                    "minus"=>"ui-icon-triangle-1-s",
                    "leaf"=>"ui-icon-bullet",
                ],
                "treeReader"  =>[
                    "parent_id_field" => "subid",
                    "level_field" => "level",
                ], 
                "navgrid" => [
                    "button" => NavGridHelper::Button(["search"=>false,"add"=>true,"edit"=>true,"del"=>true,"refresh"=>true]),
                    "editOptions"=>NavGridHelper::editOptions(["reloadAfterSubmit"=>false,]),
                    "addOptions"=>NavGridHelper::addOptions(["reloadAfterSubmit"=>false,"closeAfterAdd"=>true]),
                    "delOptions"=>NavGridHelper::delOptions(),
                ],
                "colModel" => [
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
                                                      "colModel"=>["GetAdminUrls"=>[]], //вывод в форматтере select
                                                      "ajaxRead"=>["GetAdminUrls"=>[]], //подгрузка при редактированиив форме
                                                      ],
                                                  ]),
                    ColModelHelper::hidden("id"),
                ],
            ],
        ],
];