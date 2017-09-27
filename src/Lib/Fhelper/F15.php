<?php
/*
2 кнопки подписи формы УСТАРЕЛО!!! использует SQL
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F15 extends Fhelperabstract 
{
	protected $hname="---- Устарела 2 кнопки с произвольным текстом подписывают форму";
	protected $category=6;
	protected $properties_keys=["button_type1","button_delete_flag1","button_type2","button_delete_flag2","button_caption","button_image_file","button_out_type"];
	protected $properties_text=["button_type1"=>"СТАРЕЛО Вид кнопки 1",
								"button_delete_flag1"=>"Для 1-ой кнопки удаления требовать подтверждение операции",
								"button_type2"=>"УСТАРЕЛО Вид кнопки 2",
								"button_delete_flag2"=>"Для 2-ой кнопки удаления требовать подтверждение операции",
								"button_caption"=>"Надпись кнопок (SQL), например, select name from table where ..... (результат должен быть вида Сохранить,Удалить)или сам текст строки (зависит от параметра см.ниже)",
								"button_image_file"=>"УСТАРЕЛО  Имена файлов изображений для графических кнопок (через запятую)",
								"button_out_type"=>"Расположение кнопок"
								];
	
	protected $properties_item_type=["button_type1"=>1,
								"button_delete_flag1"=>1,
								"button_type2"=>1,
								"button_delete_flag2"=>1,
								"button_caption"=>2,
								"button_image_file"=>0,
								"button_out_type"=>1
								];
protected $itemcount=2;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений для графической кнопки:"];
	protected $properties_listid=[
							'button_type1' => ["submit","image"],
					        'button_delete_flag1' =>[0,1],
							'button_type2' => ["submit","image"],
					        'button_delete_flag2' =>[0,1],
				            'button_caption' =>"",
							'button_out_type'=>[0,1]
							];
	protected $properties_listtext=[
							'button_type1' => ["Стандартная","Графическая"],
					        'button_delete_flag1' =>["Да","Нет"],
							'button_type2' => ["Стандартная","Графическая"],
					        'button_delete_flag2' =>["Да","Нет"],
							"button_out_type" => ["В строку","Друг под другом"],
				            'button_caption' =>""
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
	$caption='';
	if ($this->properties['button_caption'])
		{
			$a=$this->load_text_for_htmlitem($this->properties['button_caption'],false);
			$caption=explode(',',$a['name']);
			if (!isset($caption[1])) $caption[1]=NULL;
		}
	
	if ($this->properties['button_delete_flag1']==0) 
		{
			$barr["onClick"]="snd(\"{$this->name[0]}\",this)"; 
			
			$html.= $this->view->formButton($this->name[0],$caption[0],$barr);
		}
	else
		{
			$html.= $this->view->formSubmit($this->name[0],$caption[0]);
		}
	
	if (!empty($this->properties['button_out_type'])) {$html.="<br/>";}
	
	//надпись
	$barr=[];
	//preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	if ($this->properties['button_delete_flag2']==0) 
		{
			$barr["onClick"]="snd(\"{$this->name[1]}\",this)"; 
			$html.= $this->view->formButton($this->name[1],$caption[1],$barr);
		}
	else
		{
			$html.= $this->view->formSubmit($this->name[1],$caption[1]);
		}
return $html;

}




}
