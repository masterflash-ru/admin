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
        "field_label"=>"name",
        "emptyFirstItem"=>false,
        "emptyFirstItemLabel"=>"",
        "emptyFirstItemValue"=>0
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
        if ($this->options["emptyFirstItem"]){
            $rez[$this->options["emptyFirstItemValue"]]=$this->options["emptyFirstItemLabel"];
        } else {
            $rez=[];
        }
        $rs=$this->connection->Execute($this->options["sql"]);
        while (!$rs->EOF){
            $rez[$rs->Fields->Item[$this->options["field_id"]]->Value]=$rs->Fields->Item[$this->options["field_label"]]->Value;
            $rs->MoveNext();
        }
        
        $colModel["editoptions"]["value"]=$rez;
        
        return $colModel;
    }





}