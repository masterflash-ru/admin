<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;
use Admin\Lib\Simba;

class F45 extends Fhelperabstract 
{
	protected $hname="Специальное для zrkuban.ru опции ";
    protected $category=101;
	protected $itemcount=1;

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	

    
/*обработчик записи, возвращает обработанное*/
public function save()
{//\Laminas\Debug\Debug::dump(implode(",",$this->infa));
	return implode(",",$this->infa);
}

    
    
/*рендер элемента в админке*/	
public function render()
{//
 	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
    $name=$this->name[0];
    $value=explode(',',$this->value);
    
    $not_1200_600='';
    if (in_array("not_1200_600",$value)) {
        $not_1200_600='not_1200_600';
    }

    
    $input = new Element\Checkbox($this->name[0]."[]");
	$input->setUseHiddenElement(true);
	$input->setUncheckedValue("");
	$input->setCheckedValue("not_1200_600");
	$input->setValue($not_1200_600);
	$out='<label>Не выводить фото 1200x600 '.$this->view->formCheckbox($input).'</label><br>';

    $select = new Element\Select($this->name[0]."[]");
	$select->setValueOptions(["3D-slider"=>"3D слайдер"]);
	$select->setValue("3D-slider");
	$select->setAttributes($this->zatr);
	$out.='<label>Применить слайдер внутри статьи '. $this->view->FormSelect($select).'</label>';

    
    
    return $out;
    
}



}
