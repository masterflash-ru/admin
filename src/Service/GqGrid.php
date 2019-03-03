<?php
namespace Admin\Service;

/*
*/
use ADO\Service\RecordSet;
use ADO\Service\Command;

use Zend\Stdlib\ArrayUtils;
use Exception;
use DateTime;

class GqGrid
{
	protected $connection;
    protected $options;
    /*описание колонок из конфига
    * нужно для обработки даты, т.к. присылаются они сюда в ru локали
    */
    protected $colModel=[];
    
    /*имена колонок (POST ключей) 
    *для конвертирования
    */
    protected $convert_fields=[];
    
    /*локаль в которой работает сетка*/
    protected $locale="ru";
    
    protected $default_getparameters=[
        "_search"=>false,
        "rows"=>10,
        "page"=>1,
        "sidx"=>[],
        "sord"=>[]
    ];
    /**/
    protected $def_options_read=[
        "adapter"=>"db",
        "options"=>[
            "sql"=>"",
            "PrimaryKey"=>null,
        ],
    ];
    protected $def_options_write=[
        "adapter"=>"db",
        "options"=>[
            "sql"=>"",
            "PrimaryKey"=>null,
        ],
    ];

    
    public function __construct($connection,$config) 
    {
		$this->connection=$connection;
    }
    


    /**
    * установка опций из выбранного интерфейса
    * обязательно секция options из конфига!!!!
    */
    public function setOptions(array $options)
    {
        $this->options=$options;
       /*if (isset($options["locale"])){
            $this->locale=$options["locale"];
        }*/
        //смотрим описание колонок
        $this->colModel=$options["layout"]["colModel"];
        //пробежим по колонкам и поищем что конвертировать
        foreach ($options["layout"]["colModel"] as $col){
            if (!isset($col["formatter"])){
                continue;
            }
            switch ($col["formatter"]){
                case "date":{
                    $this->convert_fields[$col["name"]]="_dateConvert";
                    break;
                }
                case "datetime":{
                    $this->convert_fields[$col["name"]]="_datetimeConvert";
                    break;
                }
            }
            
        }
    }


    /**
    * чтение массива данных
    */
    public function load(array $getParameters=[])
    {
        //нормализация
        $get=[];
        foreach ($getParameters as $key=>$get_item){
            switch ($key){
                case "_search":{
                    if ($get_item=="true"){
                        $v=true;
                    } else {
                        $v=false;
                    }
                    break;
                }
                case "rows":
                case "page":{
                    $v=(int)$get_item;
                    break;
                }
                case "sidx":
                case "sord":{
                    if (!is_array($get_item)){
                        $v=[$get_item];
                    } else {
                        $v=$get_item;
                    }
                    break;
                }
            }
            $get[$key]=$v;
        }
        /*нормализуем GET параметры из грида*/
        $get=ArrayUtils::merge($this->default_getparameters,$get);
        $options=ArrayUtils::merge($this->def_options_read,$this->options["read"]);
    
        switch ($options["adapter"]){
            case "db":{/*адаптер прямое чтение из базы*/
                return $this->readDb($get,$options["options"]);
                break;
            }
            default:{
                throw new  Exception($options["adapter"]." - неизвестный адаптер чтения данных в таблицу JqGrid");
            }
        }
    }


    
    
    
    
    
    
    
    /**
    * редактирование записей
    Array
(
- массив самих данных
    [oper] => edit - операция
    [id] => 15197 - ID
)
    */
    public function edit(array $postParameters=[])
    {
        $options=ArrayUtils::merge($this->def_options_write,$this->options["write"]);
        /*пробежим по полям и если нужно, конвертируем
        * надо или нет конвертировать проверяется из метаописаний colModel
        **/
        foreach ($postParameters as $k=>$v){
            if (array_key_exists($k,$this->convert_fields)){
                $cc=$this->convert_fields[$k];
                $postParameters[$k]=$this->$cc($postParameters[$k]);
            }
        }
    
        switch ($options["adapter"]){
            case "db":{/*адаптер прямое чтение из базы*/
                return $this->writeDb($postParameters,$options["options"]);
                break;
            }
            default:{
                throw new  Exception($options["adapter"]." - - неизвестный адаптер записи данных в таблицу JqGrid");
            }
        }


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
protected function readDb(array $get,array $options)
{
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
protected function writeDb(array $postParameters,array $options)
{
    $options=ArrayUtils::merge($this->def_options_write["options"],$this->options["write"]["options"]);
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
                //$rs->Fields->Item[$k]->Value=$v;
            }
            //$rs->Update();
            break;
        }
        default:{
            throw new  Exception($postParameters["oper"]." - не изместная операция записи/редактирования JqGrid");
        }
    }
    

}


/**
* внутренняя для конвертирования даты из ru в ISO
* $in - входное значение
* возвращает конвертированный вариант
*/
protected function _dateConvert($in)
{
    $in=trim($in);
    if (empty($in)){return null;}
    $d=new DateTime($in);
    return (string)$d->format('Y-m-d');
}

/**
* внутренняя для конвертирования даты-времени из ru в ISO
* $in - входное значение
* возвращает конвертированный вариант
*/
protected function _datetimeConvert($in)
{
    $in=trim($in);
    if (empty($in)){return null;}
    $d=new DateTime($in);
    return (string)$d->format('Y-m-d H:i:s');
}

}