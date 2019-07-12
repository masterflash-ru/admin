<?php
namespace Admin\Service\Zform\Plugin;
use Zend\Form\FormInterface;
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
    * преобразование элементов rowModel, например, для генерации списков
    * $rowModel - элемент $rowModel из конфигурации
    * возвращает тот же $rowModel, с внесенными изменениями
    */
    public function rowModel(array $rowModel,FormInterface $form)
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
        
        $form->get($rowModel["name"])->setValueOptions($rez);
    }





}