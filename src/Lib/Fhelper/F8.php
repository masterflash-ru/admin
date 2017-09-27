<?php
/*
скрытое поле
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F8 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $hname="Скрытое поле";
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	return $this->view->formHidden($this->name[0],$this->value,$this->zatr);
}



}
