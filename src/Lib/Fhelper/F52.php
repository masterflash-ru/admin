<?php
/*
однострочное поле
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element\Text;

class F52 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $hname="52 - Специализированый для selena-travel.ru";
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
    $r=[];
	if (empty($this->value)){$this->value=',,';}
    $value=explode(',',$this->value);

	$input = new Text($this->name[0].'[]');
	$input->setValue($value[0]);
	$input->setAttributes($this->zatr);
	$r[]='<label>0-2года:'.$this->view->FormElement($input).'</label>';

	$input1 = new Text($this->name[0].'[]');
	$input1->setValue($value[1]);
	$input1->setAttributes($this->zatr);
	$r[]='<label>2-7 лет:'.$this->view->FormElement($input1).'</label>';
	
    $input2 = new Text($this->name[0].'[]');
	$input2->setValue($value[2]);
	$input2->setAttributes($this->zatr);
	$r[]='<label>7-14 лет:'.$this->view->FormElement($input2).'</label>';

    return implode("<br/>\n",$r);
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	return implode(",",$this->infa);
}


}
