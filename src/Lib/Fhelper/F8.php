<?php
/*
скрытое поле
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

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
	$input = new Element\Hidden($this->name[0]);
	$input->setValue($this->value);
	return $this->view->FormElement($input);
}



}
