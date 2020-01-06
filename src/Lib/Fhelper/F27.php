<?php
/*
однострочное поле + кнопка дата-время
*/

namespace Admin\Lib\Fhelper;

use Laminas\Form\Element;

class F27 extends Fdateabstract 
{
	protected $hname="ввод даты и времени (строка ввода + кнопка)";

	
public function __construct($item_id)
{
    parent::__construct($item_id);
}
	
	
	
public function render()
{
    $this->_format();
	preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	if (count($ar_name)){
        $name=$ar_name[1].'-'.$ar_name[2];
    } else {
        $name=$this->name[0];
    }
	$input = new Element\Text($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	$input->setAttribute("id", $name);
	
	$button = new Element\Button($this->name[0]."_");
	$button->setValue($this->value);
	$button->setLabel("_");
	$button->setAttributes(["onClick"=>'document.getElementById("'.$name.'").value=this.value',"id"=>$name."_"]);


	return $this->view->FormElement($input).
		$this->view->FormElement($button).
		'<script>
			if (typeof(fulldataitem)!="object") {var fulldataitem=[];}
			fulldataitem[fulldataitem.length]="'.$name.'_";
		</script>';
}





}
