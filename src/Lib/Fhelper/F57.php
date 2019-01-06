<?php
/*
вызов другого интерфейса (кнопка/ссылка)
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F57 extends Fhelperabstract 
{
	protected $hname="Редактор владельцев и доступов";
	protected $category=100;
	protected $itemcount=1;


public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
			$input1 = new Element\Button($this->name[0]);
			$input1->setLabel("--------");
			$input1->setAttributes($this->zatr);
			$html=$this->view->FormElement($input1);
return $html;
}



}
