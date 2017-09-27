<?php

/*
выпадающий список для семены языка 
*/
namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F38 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $itemtype=1;
	protected $hname="Выпадающий список для смены языка";
	protected $category=2;
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$zs=$this->zselect;
	if(!isset($_POST['ch_language'])) {$_POST['ch_language']=NULL;}
	foreach ($zs as $k=>$v) {$this->zselect[$k]=$v;}
	
	
		$input = new Element\Hidden("ch_language");
		$input->setValue($_POST['ch_language']);
	
		$select = new Element\Select($this->name[0]);
		$select->setValueOptions($this->zselect);
		$select->setValue(\Locale::canonicalize(\Locale::getDefault()));
		$select->setAttributes($this->zatr);
		$select->setAttribute("onChange",'ch_l(this.form)');

		return $this->view->FormElement($select).$this->view->FormElement($input);
	
	return $this->view->formSelect($this->name[0],\Locale::canonicalize(\Locale::getDefault()),$this->zatr,$this->zselect,["onChange"=>'ch_l(this.form)']).
			$this->view->formHidden("ch_language",$_POST['ch_language']);
	
}



}
