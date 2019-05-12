<?php
namespace Admin\Service\JqGrid\Plugin;

/*
* плагин попомщник для получения level значения для построения сетки в старом формате
* сетка не работает вообще с этим полем
*/




class TreeLevel extends AbstractPlugin
{
	protected $connection;
    protected $def_options =[
        "id_field"=>"id",                   // ID таблицы
        "parent_id_field" => "subid",       //ID внутри которой создается подуровень, 0- корневой
    ];

    public function __construct($connection) 
    {
		$this->connection=$connection;
    }
    

/**
* $value - значение для данного поля
* $postParameters - весь массив POST данных из сетки
*/
public function add($value,$postParameters)
{\Zend\Debug\Debug::dump($postParameters);
    $rs=$this->connection->execute("select  level  from admin_menu where id=".(int)$postParameters[$this->options["parent_id_field"]]);
    if ($rs->EOF){
        return 0;
    }
    return $rs->Fields->Item["level"]->Value+1;
}



}