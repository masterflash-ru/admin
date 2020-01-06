<?php

/*
флажок в разрезе колонки, группа флажков
группа в ограниченном пространстве
*/
namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;


class F102 extends Fhelperabstract 
{
	protected $hname="Массив флажков для каталога товара";
    protected $category=2;
    protected $itemtype=1;
    protected $itemcount=1;
	

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
    $input = new Element\MultiCheckbox($this->name[0]);
    $input->setValueOptions($this->zselect);
    $input->setValue(explode("~",$this->value));
    $element=$this->view->FormMultiCheckbox()->setSeparator("<br>");
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
