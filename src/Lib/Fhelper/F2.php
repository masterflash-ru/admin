<?php
/*
однострочное поле
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F2 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $hname="Однострочный ввод";
	protected $properties_keys=["type","empty_out"];
	protected $properties_text=[
        "type"=>"Тип поля HTML5:",
        "empty_out"=>"Если пустая строка передавать:"
    ];
	protected $properties_item_type=["type"=>1,"empty_out"=>1];
	protected $properties_listid=[
        'type'=>[
            "Text",
            "Number",
            "Tel",
            "Email",
            "Date",
            "DateTime",
            "DateTimeLocal",
            "Range",
            "Search",
            "Color",
        ],
        "empty_out" =>[0,1,2]
    ];

	protected $properties_listtext=[
        'type' =>[
            "Text - стандартное",
            "Number",
            "Tel",
            "Email",
            "Date",
            "DateTimeLocal",
            "DateTime",
            "Range",
            "Search",
            "Color",
        ],
        "empty_out" =>["Пустая строка","0","null"]
    ];
	
public function __construct($item_id)
{
    parent::__construct($item_id);
}
	
	
	
public function render()
{
	$element='\Zend\Form\Element\Text';
	if (!empty($this->properties['type'])) {
        $element='\Zend\Form\Element\\'.$this->properties['type'];
    }
	
	$input = new $element($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);
}



}
