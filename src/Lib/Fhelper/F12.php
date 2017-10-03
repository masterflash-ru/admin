<?php
/*
вывод картинки в масштабе
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F12 extends Fhelperabstract 
{
	protected $hname="масштабированное изображениe";
	protected $category=7;
	protected $itemcount=1;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений:"];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$item_html="<img alt='' src=\"/".$this->const[0].$this->value."\" style='max-width:".$this->pic_size."px'>";
	$input = new Element\Hidden($this->name[0]);
	$input->setValue($this->value);
	return $this->view->FormElement($input).$item_html;
}



}
