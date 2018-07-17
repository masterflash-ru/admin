<?php

/*
*/
namespace Admin\Lib\Fhelper;
use Zend\Form\Element;


class F103 extends Fhelperabstract 
{
	protected $hname="Массив радиокнопок для каталога товара";
    protected $category=2;
    protected $itemtype=1;
    protected $itemcount=1;
	

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
    $input = new Element\Radio($this->name[0]);
    $input->setValueOptions($this->zselect);
    $input->setValue(explode("~",$this->value));
    $element=$this->view->FormRadio()->setSeparator("<br>");
    return $element->render($input)."<br/>" ;

}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	if ($this->properties['item_list'])
		{
			return implode(",",$this->infa);
		}
	return $this->infa;
}


}
