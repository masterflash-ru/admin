<?php
/*
Cпециальное поле настройки банерных показов
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

class F25 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $category=100;
	protected $hname="Cпециальное поле настройки банерных показов";
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	//preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	//$spec_name=$ar_name[1].'-'.$ar_name[2];
	$_v=unserialize($this->value);
	if (!isset($_v["hops"])) {$_v["hops"]="";}
	if (!isset($_v["out"])) {$_v["out"]="";}

	$input = new Element\Text("hops_".$this->name[0]);
	$input->setValue($_v["hops"]);
	$input->setAttributes(["size"=>5]);

	$select = new Element\Select("out_".$this->name[0]);
	$select->setValueOptions(["rnd"=>"Случано","ord"=>"Чередование"]);
	$select->setValue($_v["out"]);

	return "<label>Вариант вывода: ".
		$this->view->FormSelect($select)."</label><br/>\n".
	"<label>Количество показов(страниц) одного банера, страниц:".
		$this->view->FormElement($input)."</label>";
}

public function save()
{
	$this->infa=serialize(["out"=>$_POST["out_options"][$this->id], "hops"=>$_POST["hops_options"][$this->id] ]);
	return $this->infa;
}

}
