<?php
/*
кнопка подписи формы (УСТАРЕЛО!!! использует SQL)
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F14 extends Fhelperabstract 
{
	protected $hname="----- Устарела Кнопка с произвольным текстом подписывает форму";
	protected $category=6;
	protected $properties_keys=["button_type","button_delete_flag","button_caption","button_image_file"];
	protected $properties_text=["button_type"=>"УСТАРЕЛО Вид кнопки",
								"button_delete_flag"=>"Для кнопки удаления требовать подтверждение операции",
								"button_caption"=>"Надпись кнопки (SQL), например, select name from table where .... или сам текст строки (зависит от параметра см.ниже).",
								"button_image_file"=>"УСТАРЕЛО  Имя файла изображения для графической кнопки"
								];
	
	protected $properties_item_type=["button_type"=>1,
								"button_delete_flag"=>1,
								"button_caption"=>2,
								"button_image_file"=>0
								];
	protected $itemcount=1;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений для графической кнопки:"];
	protected $properties_listid=[
							'button_type' => ["submit","image"],
					        'button_delete_flag' =>[0,1],
				            'button_caption' =>""
							];
	protected $properties_listtext=[
							'button_type' => ["Стандартная","Графическая"],
					        'button_delete_flag' =>["Да","Нет"],
				            'button_caption' =>""
							];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	//надпись
	$barr=[];
	$caption='';
	if ($this->properties['button_caption'])
		{
			$a=$this->load_text_for_htmlitem($this->properties['button_caption'],false);
			$caption=$a['name'];
		}
	if ($this->properties['button_delete_flag']==0) 
		{
			$barr["onClick"]="snd(\"{$this->name[0]}\",this)"; 
			return $this->view->formButton($this->name[0],$caption,$barr);
		}
	else
		{
			return $this->view->formSubmit($this->name[0],$caption);
		}
}




}
