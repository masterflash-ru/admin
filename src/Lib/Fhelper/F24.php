<?php
/*
Cпециальное поле настройки СЕО параметров
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F24 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $category=100;
	protected $hname="Опции для СЕО";
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$_v=unserialize($this->value);
	if (!isset($_v["canonical"])) {$_v["canonical"]="";}
	if (!isset($_v["robots"])) {$_v["robots"]="";}

	$input = new Element\Text("canonical_".$this->name[0]);
	$input->setValue($_v["canonical"]);
	

	$select = new Element\Select("robots_".$this->name[0]);
	$select->setValueOptions([""=>"Нет","noindex"=>"Да"]);
	$select->setValue($_v["robots"]);

	return "<label>Запретить индексацию: ".
		$this->view->FormSelect($select)."</label><br/>\n".
	"<label>Адрес канонической страницы (пусто - нет), относительный:".
		$this->view->FormElement($input)."</label>";
}

public function save()
{
	$this->infa=serialize(["robots"=>$_POST["robots_".$this->col_name][$this->id], "canonical"=>$_POST["canonical_".$this->col_name][$this->id] ]);
	return $this->infa;
}

}
