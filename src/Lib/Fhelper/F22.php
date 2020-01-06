<?php

/*
выпадающий список с мультивыбором
*/
namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

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
	foreach ($zs as $k=>$v) {
        $this->zselect[$k]=$v;
    }
	//костыли для списка с опциями
	foreach ($zs as $k=>$v) {
        if (is_array($v)) {
            $this->zselect[$k]=["options"=>$v,"label"=>$k];
        } else {
            $this->zselect[$k]=$v;
        }
    }

	$select = new Element\Select($this->name[0]);
	$select->setValueOptions($this->zselect);
	$select->setValue(explode(",",$this->value));
	$select->setAttributes($this->zatr);
	$select->setAttribute('multiple','multiple');
	return  $this->view->FormSelect($select);
	
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
    if (is_array($this->infa)){
        return implode(",",$this->infa);
    } else {
        return $this->infa;
    }
}


}
