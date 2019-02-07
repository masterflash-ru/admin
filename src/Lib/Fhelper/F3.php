<?php

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F3 extends Fhelperabstract 
{
	protected $itemcount=1;
	
	protected $hname="Многострочное поле";
	protected $properties_keys=["empty_out"];
	protected $properties_text=[
        "empty_out"=>"Если пустая строка передавать:"
    ];
	protected $properties_item_type=["empty_out"=>1];
	protected $properties_listid=[
        "empty_out" =>[0,1,2]
    ];

	protected $properties_listtext=[
        "empty_out" =>["Пустая строка","0","null"]
    ];

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$input = new Element\Textarea($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);

	//return $this->view->formTextArea($this->name[0],$this->value,$this->zatr);
}



}
