<?php

/*
вывод просто текста
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F1 extends Fhelperabstract 
{
	protected $hname="Просто текст";
	protected $properties_keys=["value_type","text_type"];
	protected $properties_text=["value_type"=>"Тип значения","text_type"=>"Тип вывода на экран"];
	protected $properties_item_type=["value_type"=>1,"text_type"=>1];
	protected $itemcount=1;
	protected $properties_listid=[
									'value_type' => [0,1],
									'text_type' => [0,1]
								];
	protected $properties_listtext=[
									'value_type' => ["результат выборки","Из настроек Значение (умолчание)"],
									'text_type' => ["результат выборки","Из настроек Значение (умолчание)"]
								];

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$value=$this->value;
	$valuep=$this->value;
	if ($this->properties['value_type']>0) {$value=$this->default_value;}
	if ($this->properties['text_type']>0) {$valuep=$this->default_text;}
	return $this->view->formHidden($this->name[0],$value)."<span {ATR0}>{$valuep}</span>";
}



}
