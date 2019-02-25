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
        "sidx"=>[null],
        "sord"=>["asc"]
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
                case "rows":{
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
        $get=ArrayUtils::merge($this->default_getparameters,$get);
        
        if (isset($this->options["read"]["db"]["sql"])){
            //чтение из базы
            $rs=new RecordSet();
            $rs->CursorType =adOpenKeyset;
            $sql=$this->options["read"]["db"]["sql"];
            //добавим в SQL сортировку, что бы не грузить всю таблицу в память!
            foreach ($get["sidx"] as $field ){
                //if ()
            }
            
            $rs->Open($sql,$this->connection);//\Zend\Debug\Debug::dump($rs->DataColumns->Item_text);
            $rez["rows"]=[];
            $PrimaryKey=null;
            //ищем первичный ключ, если есть
            foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
                if ($columninfo->PrimaryKey){
                    $PrimaryKey=$column_name;
                    break;
                }
            }
            
            while (!$rs->EOF){
                $r=[];
                foreach ($rs->DataColumns->Item_text as $column_name=>$columninfo) {
                    $r[$column_name]=$rs->Fields->Item[$column_name]->Value;
                }
                $rez["rows"][]=$r;
                $rs->MoveNext();
            }
            
            
            
            return $rez;
        }
    }


    
    
    
    
    
    
    
    /**
    * 
    */

}