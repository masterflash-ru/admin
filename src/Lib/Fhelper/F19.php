<?php
/*
кнопка подписи формы
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;

class F19 extends Fhelperabstract 
{
	protected $hname="Кнопка подписывает форму";
	protected $category=6;
	protected $properties_keys=["button_delete_flag","button_caption"];
	protected $properties_text=["button_delete_flag"=>"Для кнопки удаления требовать подтверждение операции",
								"button_caption"=>"Надпись на кнопке"
								];
	
	protected $properties_item_type=["button_delete_flag"=>1,
								"button_caption"=>0
								];
protected $itemcount=1;
	protected $properties_listid=[
					            'button_delete_flag' => [0,1]
								];

	protected $properties_listtext=[
							'button_delete_flag' => ["Да","Нет"],
						];

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	//надпись
	$barr=["class"=>"ui-button ui-widget ui-corner-all"];
	//preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	$caption=$this->properties['button_caption'];
	if ($this->properties['button_delete_flag']==0) {
        $barr["onClick"]="snd(\"{$this->name[0]}\",this)";
        $input = new Element\Button($this->name[0]);
        $input->setValue($caption);
        $input->setAttributes($barr);
        return $this->view->formButton($this->name[0],$caption,$barr);
    } else {
        $input = new Element\Submit($this->name[0]);
        $input->setValue($caption);
        $input->setAttributes($barr);
        $input->setAttributes($this->zatr);
        return $this->view->FormElement($input);
    }
}




}
