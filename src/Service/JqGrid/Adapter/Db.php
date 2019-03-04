<?php
namespace Admin\Service\JqGrid\Adapter;

/*
*/
use ADO\Service\RecordSet;
use ADO\Service\Command;
use Zend\Stdlib\ArrayUtils;

use Exception;


class Db
{
	protected $connection;
    protected $options;
    protected $def_options_read=[
        "sql"=>"",
        "PrimaryKey"=>null,
    ];
    protected $def_options_write=[
        "sql"=>"",
        "PrimaryKey"=>null,
    ];

    
    public function __construct($connection) 
    {
		$this->connection=$connection;
    }
    



/**
* чтение из базы
* $options - опции из секции чтения (read) конфига интерфейса
* $get - массив того что посылает JqGrid как есть
* используются опции по умолчанию
    protected $def_options_read=[
        "adapter"=>"db",
        "options"=>[
            "sql"=>"",
            "PrimaryKey"=>null,
        ],
    ];
* возвращает массив формата грида, который к упаковке в json
*/
public function read(array $get,array $options)
{
    $options=ArrayUtils::merge($this->def_options_read,$options);
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;
    $rs->PageSize=(int)$get["rows"];

    $sql=$options["sql"];
    $sql_sort=[];
    //добавим в SQL сортировку, что бы не грузить всю таблицу в память!
    foreach ($get["sidx"] as $k=>$field ){
        if (isset($get["sord"][$k]) && $field){
            $sql_sort[]=" $field ".$get["sord"][$k];
        }
    }
    if (!empty($sql_sort)){
        $sql.=" order by ".implode(",",$sql_sort);
    }
    //print_r($get);
    $rs->Open($sql,$this->connection);
    $rez["total"]=$rs->PageCount; //кол-во строк всего в базе
    $rez["records"]=$rs->RecordCount;
    $rez["total"]=$rs->PageCount;
    
    $rs->AbsolutePage=(int)$get["page"];
    $rez["page"]=$rs->AbsolutePage;
    $rez["rows"]=[];
    if (!$options["PrimaryKey"]){
        //ищем первичный ключ, если есть, в опциях он не задак конекретно
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            if ($columninfo->PrimaryKey){
                $options["PrimaryKey"]=$column_name;
                break;
            }
        }
    }
    $rez["id"]=$options["PrimaryKey"];
            
    $c=$rs->PageSize;
    while (!$rs->EOF && $c>0){
        $r=[];
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            $r[$column_name]=$rs->Fields->Item[$column_name]->Value;
            
        }
        $rez["rows"][]=$r;
        $rs->MoveNext();
        $c--;
    }
    return $rez;    
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
public function write(array $postParameters,array $options)
{
    $options=ArrayUtils::merge($this->def_options_write,$options);
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
                $rs->Fields->Item[$k]->Value=$v;
            }
            $rs->Update();
            break;
        }
        default:{
            throw new  Exception($postParameters["oper"]." - не известная операция записи/редактирования JqGrid");
        }
    }
    

}



}