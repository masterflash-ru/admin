<?php

/*
выпадающий список с мультивыбором
*/
namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F22 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $itemtype=1;
	protected $hname="Выпадающий список с мультивыбором";
	protected $category=2;
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$zs=$this->zselect;
	$this->zselect=[""=>""];
	foreach ($zs as $k=>$v) {$this->zselect[$k]=$v;}
	return $this->view->formSelect($this->name[0].'[]',$this->value,$this->zatr,$this->zselect);
	
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	return implode(",",$this->infa);
	
}


}
