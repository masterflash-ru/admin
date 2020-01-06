<?php

/*
флажок в разрезе колонки, группа флажков
группа в ограниченном пространстве
*/
namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

class F20 extends Fhelperabstract 
{
	protected $hname="флажок (группа флажков)";
	
	protected $properties_keys=["item_list","window_width","window_height"];
	protected $properties_text=["item_list"=>"SQL выборка, например, select id,name from table",
								"window_width"=>"Ширина области вывода группы в px (пусто-без прокрутки)",
								"window_height"=>"Высота области вывода группы в px (пусто-без прокрутки)"
								];
	
	protected $properties_item_type=["item_list"=>2,
								"window_width"=>0,
								"window_height"=>0
								];
	protected $itemcount=1;
	protected $properties_listid=[
					            'item_list' => ""
								];

		protected $properties_listtext=[
							'item_list' =>""
							];

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	if (isset($this->properties['item_list']) && $this->properties['item_list']){
        //в разрезе элемента, просто группа
        $value=explode(',',$this->value);
        $arr=Fhelperabstract::load_text_for_htmlitem($this->properties['item_list'],true);//считаем параметры и заодно кешируем их
        foreach ($arr["id"] as $k=>$id)	{
            if (!isset($value[$k])) {$value[$k]=0;}
            $r[$id]=$arr["name"][$k];
        }
		$item_html=$this->view->formMultiCheckbox($this->name[0],$value,NULL,$r);
		if ($this->properties['window_width']>0 && $this->properties['window_height']>0) {
            $item_html='<div style="width:'.$this->properties['window_width'].'px; height:'.$this->properties['window_height'].'px; overflow:auto;">'.$item_html.'</div>';
        }
        return $item_html;
    } else {//в разрезе колонки, одиночный
        $input = new Element\Checkbox($this->name[0]);
        $input->setUseHiddenElement(true);
        $input->setUncheckedValue(0);
        $input->setCheckedValue(1);
        $input->setValue((int)$this->value);
        return $this->view->formCheckbox($input);
    }
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	if ($this->properties['item_list']) {
        return implode(",",$this->infa);
    }
	return $this->infa;
}


}
