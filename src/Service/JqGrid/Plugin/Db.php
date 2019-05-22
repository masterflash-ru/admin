<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/
use ADO\Service\RecordSet;
use ADO\Service\Command;
use Zend\Stdlib\ArrayUtils;

use Exception;


class Db extends AbstractPlugin
{
	protected $connection;          // соедитнение с базой
    protected $last_insert_id=0;    //ID последней вставленной записи
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
public function read(array $get)
{
    $options=ArrayUtils::merge($this->def_options_read,$this->options);
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;
    $rs->PageSize=(int)$get["rows"];

    $sql=$options["sql"];
    preg_match_all("/[=<>]['\"]?:([a-zA-Z0-9_]+)['\"]?/iu",$sql,$mm);
    $mm=array_unique($mm[1]);
    foreach ($get as $n=>$g){
        if (in_array($n,$mm)){
            $sql=str_replace(":{$n}",$g,$sql);
        }
    }
    //если нет соотвествуюего GET параметра, для строковых вставим пустою строку, для чисел 0
    $sql=preg_replace('/[\'"]{1}:[a-zA-Z0-9_]+[\'"]{1}/ui','""',$sql);
    $sql=preg_replace('/:[a-zA-Z0-9_]+/ui',0,$sql);
   // $rez["sql"]=$sql;
   // $rez["GET"]=$get;
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
            //для генерации стандартного дерева
            if ($column_name=="isLeaf"){
                $r["isLeaf"]=(boolean)$r["isLeaf"];
            }
        }
        $rez["rows"][]=$r;
        $rs->MoveNext();
        $c--;
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
            $this->last_insert_id=$rs->Fields->Item[$options["PrimaryKey"]]->Value;
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