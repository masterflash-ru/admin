<?php
/*
вызов другого интерфейса (кнопка/ссылка)
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F49 extends Fhelperabstract 
{
	protected $hname="Ссылка для открытия другого интерфейса";
	protected $category=100;
	protected $properties_keys=["interface_type","interface_name","window_properties","window_close_type","link_type"];
	protected $properties_text=["interface_type"=>"Типы интерфейсов (0-Линейный,1-Древовидный,2-Форма,3-модуль), ч/з)",
								"interface_name"=>"Имена интерфейсов/имен файлов модулей, ч/з ",
								"window_properties"=>"Параметры для метода window.open (через ,)",
								"window_close_type"=>"Закрыть окно после сохранения",
								"link_type"=>"Тип ссылки"
								];
	
	protected $properties_item_type=["interface_type"=>0,
								"interface_name"=>0,
								"window_properties"=>0,
								"window_close_type"=>1,
								"link_type"=>1
								];
	
	protected $itemcount=1;
		protected $properties_listid=[
								'window_close_type'=>[0,1],
					            'link_type'=>["link","button","menu"]
								];

	protected $properties_listtext=[
							'window_close_type' =>["Нет","Да"],
							'link_type' => ["Обычная","Кнопка","Выпадающее меню"]
                ];

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
	for ($i=0;$i<count($interface_type);$i++){
        if ($interface_type[$i]==0) $_url="line/";
		if ($interface_type[$i]==1) $_url="tree/";
		if ($interface_type[$i]==3) $_url="";
		$jmp[$i]='onclick=\'window.open("/adm/'. $_url. $interface_name[$i].
		
		'?get_interface_input='.base64_encode(serialize($this->value)).
		'&window_close_type='.$this->properties['window_close_type'].
		'","","'.str_replace(' ',',',$this->properties['window_properties']).'");return false;\'';
        
        $m="/adm/". $_url. $interface_name[$i].'?get_interface_input='.base64_encode(serialize($this->value)).
		'&window_close_type='.$this->properties['window_close_type'];

        switch ($this->properties['link_type']){
            case 'button':{
                $jmp_html[]= '<input name="__" '.$this->atr[0].' type="button" class="ui-button ui-widget ui-corner-all" value="'.$default_value[$i].'" '.$jmp[$i].' />';
                break;
            }
            case "menu":{
                $jmp_html[]= '<option value="'.$this->view->escapeHtmlAttr ($m).'" />'.$default_value[$i].'</option>';
                break;
            }
            default:{
                $jmp_html[]='<a '.$this->atr[0].' href=# '.$jmp[$i].'>'.$default_text[$i].'</a>';
            }
        }
    }
	
	$input = new Element\Hidden($this->name[0]);
	$input->setValue($this->value);
    if ($this->properties['link_type']=="menu"){
        return "<select class=\"controlgroup49\"><option value=''>Выберите действие</option>".implode("",$jmp_html).
                "</select>".
            $this->view->FormElement($input);
    } else {
        return implode("",$jmp_html).$this->view->FormElement($input);
    }
}



}
