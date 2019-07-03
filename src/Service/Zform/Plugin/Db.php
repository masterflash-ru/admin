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
         "PrimaryKey"=>[],
    ];
    protected $def_options_write=[
        "sql"=>"",
         "PrimaryKey"=>[],
        "addIfNotFount"=>false,
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
{//print_r($get);
    $options=ArrayUtils::merge($this->def_options_read,$this->options);
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;

    $sql=$options["sql"];


    preg_match_all("/[=<>]:([a-zA-Z0-9_]+)/iu",$sql,$mm);
    $mm=array_unique($mm[1]);
    foreach ($get as $n=>$g){
        if (in_array($n,$mm)){
            $sql=str_replace(":{$n}",$g,$sql);
        }
    }
    
    //\Zend\Debug\Debug::dump($sql);
    
    $rs->Open($sql,$this->connection);
    $rez=[];
    foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
        $rez[$column_name]=$rs->Fields->Item[$column_name]->Value;            
    }
    if (!$options["PrimaryKey"]){
        $options["PrimaryKey"]=[];
        //ищем первичный ключ, если есть, в опциях он не задак конекретно
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            if ($columninfo->PrimaryKey){
                $options["PrimaryKey"][]=$column_name;
            }
        }
    }
    $rez["PrimaryKeyName"]=$options["PrimaryKey"];

    return $rez;    
}

public function add(array $postParameters,array $get=[])
{
    $postParameters["oper"]="add";
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
public function edit(array $postParameters,array $get=[])
{
    if (!isset($postParameters["oper"])){
        $postParameters["oper"]="edit";
    }
    $options=ArrayUtils::merge($this->def_options_write,$this->options); //получаем опции
    $postParameters=ArrayUtils::merge($get,$postParameters);            //получаем данные, на GET накладываем POST
    $rs=new RecordSet();
    $rs->CursorType =adOpenKeyset;
    $sql=$options["sql"];
    $rs->Open($sql,$this->connection);
    if (!$options["PrimaryKey"]){
        $options["PrimaryKey"]=[];
        $keys=[];
        //ищем первичный ключ, если есть, в опциях он не задак конекретно
        foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
            if ($columninfo->PrimaryKey){
                $options["PrimaryKey"][]=$column_name;
            }
            if ($columninfo->Key){
                //обычные ключи, на случай, если первичных не будет вовсе
                $keys[]=$column_name;
            }

        }
        if(empty($options["PrimaryKey"])){
            //если первичного ключа нет, тогда массив обычных ключей и будет общим первичным
            $options["PrimaryKey"]=$keys;
        }
    }
    switch ($postParameters["oper"]){
        case "edit":{
            /*редактирование, находим запись по ключу*/
            //найдем нужную запись КЛЮЧ МАССИВ, Т.К. может быть в ключе несколько полей
            $rs_search=[];
            foreach ($options["PrimaryKey"]  as $key_item){
                if (isset($postParameters[$key_item])){
                    $rs_search[]=$key_item."='".$postParameters[$key_item]."'";
                }
            }

            $rs->Find(implode(" and ",$rs_search));
            if ($rs->EOF) {
                //запись не найдена
                if ($options["addIfNotFount"]){
                    //добавить новую, т.к. указывает на это флаг
                    $rs->AddNew();
                } else {
                    throw new  Exception("Запись ".implode(" and ",$rs_search)."' не найдена!");
                }
            }
            $skip=[];
            $skip[]="oper";
            foreach ($postParameters as $k=>$v){
                if (in_array($k,$skip)){continue;}
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
                if (in_array($k,["oper"])){continue;}
                if (array_key_exists($k,$rs->DataColumns->Item_text)){
                    $rs->Fields->Item[$k]->Value=$v;
                }
            }
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