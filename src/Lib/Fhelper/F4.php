<?php

/*
выпадающий список
*/
namespace Admin\Lib\Fhelper;
use Zend\Form\Element;


class F4 extends Fhelperabstract 
{
	protected $hname="Выпад. список";
	protected $category=2;
	protected $properties_keys=["list_type","FlagNull"];
	protected $properties_text=["list_type"=>"Тип:","FlagNull"=>"Пустой первый элемент"];
	protected $properties_item_type=["list_type"=>1,"FlagNull"=>1];
	protected $itemcount=1;
	protected $properties_listid=[
									'list_type' => [0,1],
									'FlagNull' => [0,1]
								];
	protected $properties_listtext=[
								'list_type'=>["Стандарт","В виде значения-текста"],
					            'FlagNull' =>["Нет","Да"]
								];
	protected $itemtype=1;
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	if ($this->properties['list_type']>0)
		{
			//$item_html=$this->view->formHidden($this->name[0],$this->value,$this->zatr);
			$item_html= new Element\Hidden($this->name[0]);
			$item_html->setValue($this->value);
			for ($kk=0;$kk<count($this->sp_id);$kk++)
				{
					if ($this->sp_id[$kk]==$this->value)
						{
							$item_html.= '<span '.$this->atr[0].'>'.$this->sp[$kk].'</span>' ;
							$kk=count($this->sp_id);
						}
				}
			return $item_html;
		}
		else
			{
				if ($this->properties['FlagNull'])
					{
						$zs=$this->zselect;
						$this->zselect=[""=>""];
						foreach ($zs as $k=>$v) {$this->zselect[$k]=$v;}
					}
				$select = new Element\Select($this->name[0]);
				$select->setValueOptions($this->zselect);
				$select->setValue($this->value);
				$select->setAttributes($this->zatr);
				return $this->view->FormElement($select);
				
				
				//return $this->view->formSelect($this->name[0],$this->value,$this->zatr,$this->zselect);
			}
}



}
