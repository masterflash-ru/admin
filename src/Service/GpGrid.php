<?php
namespace Admin\Service;

/*
*/
use ADO\Service\RecordSet;
use ADO\Service\Command;

use Zend\Stdlib\ArrayUtils;


class GpGrid
{
	protected $connection;
    protected $options;
    protected $default_getparameters=[
        "_search"=>false,
        "rows"=>10,
        "page"=>1,
        "sidx"=>[],
        "sord"=>[]
    ];
    protected $default_options=[
        "db"=>[
            "sql"=>"",
            "options"=>[
                "PrimaryKey"=>null
            ],
        ],
    ];
	
    public function __construct($connection,$config) 
    {
		$this->connection=$connection;
        
    }
    


    /**
    * установка опций из выбранного интерфейса
    */
    public function setOptions(array $options)
    {
        $this->options=$options;
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
        
        if (isset($this->options["read"]["db"]["sql"])){
            //чтение из базы
            $options=ArrayUtils::merge($this->default_options["db"],$this->options["read"]["db"]);
            $options=$options["options"];
            $rs=new RecordSet();
            $rs->CursorType =adOpenKeyset;
            $rs->PageSize=(int)$get["rows"];
            
            $sql=$this->options["read"]["db"]["sql"];
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
    }


    
    
    
    
    
    
    
    /**
    * 
    */

}