<?php
namespace Admin\Service\JqGrid\Plugin;

/*
* плагин попомщник для работы с класическим типом дерева
* 
*/
use ADO\Service\RecordSet;
use Laminas\Session\Container;


class TreeAdjacency extends Db
{
    protected $connection;
    protected $ids=[];
    protected $rs_tree;
    protected $def_options =[
        "id_field"=>"id",                   // ID таблицы
        "parent_id_field" => "subid",       //ID внутри которой создается подуровень, 0- корневой
        "level_field" => "level",
        "sql"=>"",
        "interface_name"=>null
    ];
    
public function __construct($connection) 
{
    $this->connection=$connection;
}

    
public function iread(array $get)
{
    if (empty($get["nodeid"])){
        //чистим сессию только когда перечитываем сетку
        $tmpid=new Container($this->options["interface_name"]);
        $tmpid->newIds=[];
        $tmpid->newLevels=[];
    }
    return parent::iread($get);
}
    
/**
* $postParameters - весь массив POST данных из сетки
*/
public function iadd(array $postParameters)
{
    $tmpid=new Container($this->options["interface_name"]);
    
    if (false===strpos($postParameters[$this->options["parent_id_field"]],"jqg")) {
        //временный идентификатора нет, работаем с базой
        $rs=$this->connection->execute($this->options["sql"]);
        $rs->Find($this->options["id_field"]."=".$postParameters[$this->options["parent_id_field"]]);
        if ($rs->EOF){
            //добавляется в корневой раздел
            $postParameters[$this->options["level_field"]]=0;
        } else {
            $postParameters[$this->options["level_field"]]=$rs->Fields->Item[$this->options["level_field"]]->Value+1;
        }
    } else {
        //в сетке временный идентификатор, сопоставим его с данными в сессии
        $id=(int)str_replace("jqg","",$postParameters[$this->options["parent_id_field"]]);
        $postParameters[$this->options["parent_id_field"]]=$tmpid->newIds[$id];
        $postParameters[$this->options["level_field"]]=$tmpid->newLevels[$id]+1;
    }
    
    $rez= parent::iadd($postParameters);
    if (!count($tmpid->newIds)){
        $id=$this->getLastId();
        $tmpid->newIds[1]=$id;
        $tmpid->newLevels[1]=$postParameters[$this->options["level_field"]];
    } else {
        $id=$this->getLastId();
        $tmpid->newIds[]=$id;
        $tmpid->newLevels[]=$postParameters[$this->options["level_field"]];
    }
    return $rez;
 
}

/**
* $postParameters - весь массив POST данных из сетки
*/
public function iedit(array $postParameters)
{
     $tmpid=new Container($this->options["interface_name"]);
    if (false!==strpos($postParameters[$this->options["id_field"]],"jqg")) {
        $id=(int)str_replace("jqg","",$postParameters[$this->options["id_field"]]);
        $postParameters["id"]=$tmpid->newIds[$id];
    }
    return parent::iedit($postParameters);
}

/**
* $postParameters - весь массив POST данных из сетки
*/
public function idel(array $postParameters)
{
    $tmpid=new Container($this->options["interface_name"]);
    if (false!==strpos($postParameters[$this->options["id_field"]],"jqg")) {
        $id=(int)str_replace("jqg","",$postParameters[$this->options["id_field"]]);
        $postParameters["id"]=$tmpid->newIds[$id];
    }
    //вначале полуим все вложения данного узла
    $this->rs_tree=new RecordSet();
    $this->rs_tree->MaxRecords=0;
    $this->rs_tree->open($this->options["sql"],$this->connection);
    $this->get_tree_ids($postParameters["id"]);
    $this->ids[]=$postParameters["id"];
    foreach ($this->ids as $id){
        //пройдем по всем узлам и удалим там записи
        $postParameters["id"]=$id;
        parent::idel($postParameters);
        if (isset($tmpid->newIds[$id])){
            $tmpid->newIds[$id]=null;
            $tmpid->newLevels[$id]=null;
        }
    }
}

/**
* получить список ID который расположены ниже заданного
*/
protected function get_tree_ids ($id=0)
{
    $rs=clone $this->rs_tree;
    $rs->Filter="subid=$id";
    while (!$rs->EOF){
        $this->ids[]=$rs->Fields->Item["id"]->Value;
        $this->get_tree_ids ($rs->Fields->Item["id"]->Value);
        $rs->MoveNext();
    }
    $rs->Close();
    $rs=null;

}

/**
*получить ID последней вставленной записи
*/    
protected function getLastId()
{
    return $this->last_insert_id;
}

}