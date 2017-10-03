<?php
/*
однострочное поле
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F2 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $hname="Однострочный ввод";
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$input = new Element\Text($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);
}



}
