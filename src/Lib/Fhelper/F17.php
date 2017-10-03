<?php
/*
2 кнопки подписи формы
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F17 extends Fhelperabstract 
{
	protected $hname="2 кнопки  подписывают форму";
	protected $category=6;
	protected $properties_keys=["button_delete_flag1","button_delete_flag2","button_caption","button_out_type"];
	protected $properties_text=["button_delete_flag1"=>"Для 1-ой кнопки удаления требовать подтверждение операции",
								"button_delete_flag2"=>"Для 2-ой кнопки удаления требовать подтверждение операции",
								"button_caption"=>"Надписи через запятую",
								"button_out_type"=>"Расположение кнопок"
								];
	
	protected $properties_item_type=["button_delete_flag1"=>1,
								"button_delete_flag2"=>1,
								"button_caption"=>0,
								"button_out_type"=>1
								];
protected $itemcount=2;
	protected $properties_listid=[
								'button_delete_flag1' =>[0,1],
					            'button_delete_flag2'=>[0,1],
					            'button_out_type' => [0,1]
								];

	protected $properties_listtext=[
							'button_delete_flag1' => ["Да","Нет"],
					        'button_delete_flag2' =>["Да","Нет"],
							"button_out_type" => ["В строку","Друг под другом"]
							];
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$html='';
	//надпись
	$barr=[];
	//preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	$caption=explode(',',$this->properties['button_caption']);
	if ($this->properties['button_delete_flag1']==0 ) 
		{
			$barr["onClick"]="snd(\"{$this->name[0]}\",this)"; 
			$input = new Element\Button($this->name[0]);
			$input->setLabel($caption[1]);
			$input->setAttributes($barr);
			$html.=$this->view->FormButton($input);
			//$html.= $this->view->formButton($this->name[0],$caption[0],$barr);
		}
	else
		{
			$input = new Element\Submit($this->name[0]);
			$input->setValue($caption[0]);
			$html.= $this->view->FormElement($input);

			//$html.= $this->view->formSubmit($this->name[0],$caption[0]);
		}
	
	if (!empty($this->properties['button_out_type'])) {$html.="<br/>";}
	
	//надпись
	$barr=[];
	//preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	if ($this->properties['button_delete_flag2']==0) 
		{
			$barr["onClick"]="snd(\"{$this->name[1]}\",this)"; 
			//$html.= $this->view->formButton($this->name[1],$caption[1],$barr);
			$input1 = new Element\Button($this->name[1]);
			$input1->setLabel($caption[1]);
			$input1->setAttributes($barr);
			$html.=$this->view->FormElement($input1);

		}
	else
		{
			$input1 = new Element\Submit($this->name[1]);
			$input1->setValue($caption[1]);
			$html.= $this->view->FormElement($input1);

			//$html.= $this->view->formSubmit($this->name[1],$caption[1]);
		}
return $html;

}




}
