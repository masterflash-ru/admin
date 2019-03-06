<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/




class SelectFromDb extends AbstractPlugin
{
	protected $connection;
    protected $def_options =[
        "sql"=>"",
        "field_id" =>"id",
        "field_label"=>"name"
    ];

    public function __construct($connection) 
    {
		$this->connection=$connection;
    }
    

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel)
    {

        $rez=[];
        $rs=$this->connection->Execute($this->options["sql"]);
        while (!$rs->EOF){
            $rez[$rs->Fields->Item[$this->options["field_id"]]->Value]=$rs->Fields->Item[$this->options["field_label"]]->Value;
            $rs->MoveNext();
        }
        $colModel["editoptions"]["value"]=$rez;
        
        return $colModel;
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