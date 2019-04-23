<?php
namespace Admin\Service\Zform\Plugin;

/*
*/
use ADO\Service\RecordSet;
use ADO\Service\Command;
use Zend\Stdlib\ArrayUtils;

use Exception;


class Db extends AbstractPlugin
{
	protected $connection;
    protected $def_options_read=[
        "sql"=>"",
    ];
    protected $def_options_write=[
        "sql"=>"",
    ];

    
    public function __construct($connection) 
    {
		$this->connection=$connection;
    }
    



/**
* чтение из базы
* $options - опции из секции чтения (read) конфига интерфейса
* возвращает массив
*/
public function read(array $get)
{
    $options=ArrayUtils::merge($this->def_options_read,$this->options);
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;

    $sql=$options["sql"];

    $rs->Open($sql,$this->connection);
    $rez=[];
    foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            $rez[$column_name]=$rs->Fields->Item[$column_name]->Value;
            
    }
    return $rez;    
}

public function add(array $postParameters)
{
    return $this->edit($postParameters);
}

/**
* запись в базу
* $postParameters - массив того что посылает JqGrid как есть:
*[
- массив самих данных
    [oper] => edit|add - операция
    [id] => 15197 | _empty  - ID
]

* используются опции по умолчанию
    protected $def_options_write=[
        "adapter"=>"db",
        "options"=>[
            "sql"=>"",
            "PrimaryKey"=>null,
        ],
    ];
* $options - опции из секции write секции конфига
*/
public function edit(array $postParameters)
{//print_r(array_keys($_POST));print_r($_FILES);print_r($_POST["img"]);
    $options=ArrayUtils::merge($this->def_options_write,$this->options);
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;
    $sql=$options["sql"];
    $rs->Open($sql,$this->connection);
    if (!$options["PrimaryKey"]){
        //ищем первичный ключ, если есть, в опциях он не задак конекретно
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            if ($columninfo->PrimaryKey){
                $options["PrimaryKey"]=$column_name;
                break;
            }
        }
    }
    switch ($postParameters["oper"]){
        case "edit":{/*редактирование, находим запись по ключу*/
            //найдем нужную запись
            $rs->Find($options["PrimaryKey"]."='".$postParameters[$options["PrimaryKey"]]."'");
            if ($rs->EOF) {
                throw new  Exception("Запись ".$options["PrimaryKey"]."='".$postParameters[$options["PrimaryKey"]]."' не найдена!");
            }
            foreach ($postParameters as $k=>$v){
                if (in_array($k,["oper",$options["PrimaryKey"]])){continue;}
                if (array_key_exists($k,$rs->DataColumns->Item_text)){
                    $rs->Fields->Item[$k]->Value=$v;
                }
            }
            $rs->Update();
            break;
        }
        case "add":{
            $rs->AddNew();
            foreach ($postParameters as $k=>$v){
                if (in_array($k,["oper",$options["PrimaryKey"]])){continue;}
                if (array_key_exists($k,$rs->DataColumns->Item_text)){
                    $rs->Fields->Item[$k]->Value=$v;
                }
            }
            $rs->Update();
            break;
        }
        case "del":{/*редактирование, находим запись по ключу*/
            //найдем нужную запись
            $rs->Find($options["PrimaryKey"]."='".$postParameters[$options["PrimaryKey"]]."'");
            if ($rs->EOF) {
                throw new  Exception("Запись ".$options["PrimaryKey"]."='".$postParameters[$options["PrimaryKey"]]."' не найдена!");
            }
            $rs->Delete();
            $rs->Update();
            break;
        }

        default:{
            throw new  Exception($postParameters["oper"]." - не известная операция записи/редактирования JqGrid");
        }
    }
}

/**
* удаление записи
*/
public function del(array $postParameters)
{
    return $this->edit($postParameters);
}
}