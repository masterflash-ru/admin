<?php

/*
выпадающий список выбор в нем копирует значение в строковое поле рядом
*/
namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

class F9 extends Fhelperabstract 
{
	protected $hname="Строка ввода + выпадающий список для выбора URL";
	protected $category=2;
	protected $properties_keys=["FlagNull"];
	protected $properties_text=["FlagNull"=>"Пустой первый элемент"];
	protected $properties_item_type=["FlagNull"=>1];
	protected $properties_listid=[
									'FlagNull' => [0,1],
								];
	protected $properties_listtext=[
								'FlagNull'=>["Нет","Да"],
								];
	protected $itemtype=1;
	protected $itemcount=1;
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$zs=$this->zselect;
	if ($this->properties['FlagNull']) 
		{
			$this->zselect=[""=>""];
			foreach ($zs as $k=>$v) {$this->zselect[$k]=$v;}
		}
	foreach ($zs as $k=>$v) 
		{
			if (is_array($v))
				{
					$this->zselect[$k]=["options"=>$v,"label"=>$k];
				}
			else
				{
					$this->zselect[$k]=$v;
				}
			
		}
		
		
		$this->zatr['onChange']="document.getElementById('". str_replace("[","-",rtrim($this->name[0],"]")). "').value=this.value";
		
		$input = new Element\Text($this->name[0]);
		$input->setValue($this->value);
		$input->setAttribute("id",str_replace("[","-",rtrim($this->name[0],"]")));
	
		$select = new Element\Select("_".$this->name[0]);
		$select->setValueOptions($this->zselect);
		$select->setValue($this->value);
		$select->setAttributes($this->zatr);

		return $this->view->FormElement($input).$this->view->FormElement($select);
}



}
