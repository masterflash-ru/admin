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
	protected $properties_keys=["list_type","FlagNull","nullValue"];
	protected $properties_text=["list_type"=>"Тип:","FlagNull"=>"Пустой первый элемент","nullValue"=>"Значение пустого элемента"];
	protected $properties_item_type=["list_type"=>1,"FlagNull"=>1,"nullValue"=>1];
	protected $itemcount=1;
	protected $properties_listid=[
									'list_type' => [0,1],
									'FlagNull' => [0,1],
                                    "nullValue"=>[0,1,2]
								];
	protected $properties_listtext=[
								'list_type'=>["Стандарт","В виде значения-текста"],
					            'FlagNull' =>["Нет","Да"],
                                'nullValue'=>["Пустая строка","Цифра 0","Значение NULL"]
								];
	protected $itemtype=1;
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
    if (is_null($this->value)) {
        $this->value="null";
    }
	if ($this->properties['list_type']>0){
			//$item_html=$this->view->formHidden($this->name[0],$this->value,$this->zatr);
			$h= new Element\Hidden($this->name[0]);
			$h->setValue($this->value);
			$item_html=$this->view->FormElement($h);
			for ($kk=0;$kk<count($this->sp_id);$kk++){
                if ($this->sp_id[$kk]==$this->value){
                    $item_html.= '<span '.$this->atr[0].'>'.$this->sp[$kk].'</span>' ;
                    $kk=count($this->sp_id);
                }
            }
			return $item_html;
    } else {
        if (!isset($this->properties["nullValue"])){
            $this->properties["nullValue"]=0;
        }
		switch ($this->properties["nullValue"]){
            case 0:{
                $pusto="";
                break;
            }
            case 1:{
                $pusto=0;
                break;
            }
            case 2:{
                $pusto="null";
                break;
            }
        }
        $zs=$this->zselect;
        if ($this->properties['FlagNull']){
            $this->zselect=[$pusto=>"ПУСТО"];
        }
        foreach ($zs as $k=>$v) {
            $this->zselect[$k]=$v;
        }
			//костыли для списка с опциями
		foreach ($zs as $k=>$v) {
            if (is_array($v)){
                $this->zselect[$k]=["options"=>$v,"label"=>$k];
            } else {
                $this->zselect[$k]=$v;
            }
        }

        $select = new Element\Select($this->name[0]);
		$select->setValueOptions($this->zselect);
		$select->setValue($this->value);
		$select->setAttributes($this->zatr);
		return  $this->view->FormSelect($select);
    }
}



}
