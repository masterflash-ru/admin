<?php
/*
вызов другого интерфейса (кнопка/ссылка)
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F56 extends Fhelperabstract 
{
	protected $hname="Ссылка для открытия другого интерфейса в МОДАЛЬНОМ окне";
	protected $category=100;
	protected $properties_keys=["interface_type","interface_name","link_type","window_width","window_height"];
	protected $properties_text=["interface_type"=>"Типы интерфейсов (0-Линейный,1-Древовидный,2-Форма,3-модуль), ч/з)",
								"interface_name"=>"Имена интерфейсов/имен файлов модулей, ч/з ",
								"link_type"=>"Тип ссылки",
                                "window_width"=>"Ширина окна",
                                "window_height"=>"Высота окна"
								];
	
	protected $properties_item_type=["interface_type"=>0,
								"interface_name"=>0,
								"link_type"=>1,
                                "window_width"=>1,
                                     "window_height"=>1
								];
	
	protected $itemcount=1;
		protected $properties_listid=[

					            'link_type'=>["link","button"]
								];

	protected $properties_listtext=[

							'link_type' => ["Обычная","Кнопка"]
                ];
    protected static $flag_dialog=true;

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$jmp=[];
	$jmp_html=[];
	
	$interface_type=explode(",",$this->properties['interface_type']);//список интерфейсов
	$interface_name=explode(",",$this->properties['interface_name']);
	$default_text=explode(",",$this->default_text);
	$default_value=explode(",",$this->default_value);
    
    if (!empty($this->properties['window_width'])){
        $width=(int)$this->properties['window_width'];
    } else {$width=800;}
    if (!empty($this->properties['window_height'])){
        $height=(int)$this->properties['window_height'];
    } else {$height=600;}

	for ($i=0;$i<count($interface_type);$i++)
		{
		if ($interface_type[$i]==0) $_url="line/";
		if ($interface_type[$i]==1) $_url="tree/";
		if ($interface_type[$i]==3) $_url="";
		$jmp[$i]='onclick=\'f56("/adm/'. $_url. $interface_name[$i].'?get_interface_input='.base64_encode(serialize($this->value)).'",'.$width.','.$height.');return false;\'';

		if ($this->properties['link_type']=='link' || $this->properties['link_type']=='')
			{
				$jmp_html[]='<a '.$this->atr[0].' href=# '.$jmp[$i].'>'.$default_text[$i].'</a>';
			}
		if ($this->properties['link_type']=='button')
			{
				$jmp_html[]= '<input name="__" '.$this->atr[0].' type="button" value="'.$default_value[$i].'" '.$jmp[$i].' />';
			}
		}
	
	$input = new Element\Hidden($this->name[0]);
	$input->setValue($this->value);
    

    if (F56::$flag_dialog){$out='<div id="f56_dialog" style="display:none"><iframe frameborder="0" id="iframe56" src=""></iframe></div>';F56::$flag_dialog=false;}
        else {$iut="";}
	return implode("",$jmp_html).$this->view->FormElement($input).$out;
}



}
