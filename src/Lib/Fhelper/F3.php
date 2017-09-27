<?php

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F3 extends Fhelperabstract 
{
	protected $itemcount=1;
	
	protected $hname="Многострочное поле";
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$input = new Element\Textarea($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);

	//return $this->view->formTextArea($this->name[0],$this->value,$this->zatr);
}



}
