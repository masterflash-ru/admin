<?php

/*
вывод просто текста
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F48 extends Fhelperabstract 
{
	protected $hname="огромный выбор в отдельном окне";
	protected $properties_keys=["window_width","window_height","columns"];
	protected $properties_text=["window_width"=>"Ширина окна","window_height"=>"Высота окна","columns"=>"Колонок"];
	protected $properties_item_type=["window_width"=>1,"window_height"=>1,"columns"=>1];
	protected $itemcount=1;
	protected $itemtype=1;
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	$name_id=$ar_name[1].'-'.$ar_name[2];
	$value=explode(',',$this->value);
	$names_=str_replace(']','',str_replace('[','',$this->name[0]));//убрать [ ] из имени, что бы в JS сделать уникальную функцию

	if (!is_array($value)) {$value=[];}
   $out='<script language="JavaScript" type="text/JavaScript">';//JS для заполенния массивом данных
   $out.='if (typeof(db_item48)=="undefined") {db_item48=[];}';
   $out.= 'db_item48["'.$names_.'"] =[];'."\n";
   $out.='db_item48["'.$names_.'"]["io_item"]="'.$name_id.'";'."\n";
   $out.='db_item48["'.$names_.'"]["button_caption"]="Сохранить";'."\n";
   $out.='db_item48["'.$names_.'"]["columns"]="'.$this->properties['columns'].'";'."\n";
   $out.='db_item48["'.$names_.'"]["window"]=Array("'.$this->properties['window_width'].'","'.$this->properties['window_height'].'");'."\n";
    $out.='db_item48["'.$names_.'"]["function"]=new  Function(\'';
	for ($j=0;$j<count($this->sp);$j++)
		{
			$selected_flag=0;
			$_text=htmlspecialchars($this->sp[$j],ENT_QUOTES);

			if (in_array($this->sp_id[$j],$value)) {$selected_flag=1;} else {$selected_flag=0;}
			$out.='db_item48["'.$names_.'"][db_item48["'.$names_.'"].length] = new db_record_item48("'.  htmlspecialchars($this->sp_id[$j],ENT_NOQUOTES).'","'.$_text.'","'.$selected_flag.'");';
	  }
	$out.='\');';
	$out.='</script>';
	
	$barr["onClick"]="create_window(\"{$names_}\")";
	
	$button = new Element\Button($this->name[0]."_");
	$button->setValue($this->value);
	$button->setLabel("Выбрать");
	$button->setAttributes($barr);

	$input = new Element\Hidden($this->name[0]);
	$input->setValue($this->value);
	$input->setAttribute("id",$name_id);

	return $out."<span id=\"{$name_id}_text\"></span>".$this->view->FormElement($input).
		$this->view->FormElement($button);
}



}
