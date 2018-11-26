<?php
/*

*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Zend\Session\Container;

class F41 extends Fhelperabstract 
{
	protected $hname="Многострочное поле с кнопкой вызова HTML редактора с записью в базу";
	protected $category=4;
	protected $properties_keys=["height","width","html_editor_default_theme","html_editor_default_toolbars","table_name","table_id"];
	protected $properties_text=["height"=>"Высота окна:",
								"width"=>"Ширина окна:",
								"html_editor_default_theme"=>"Скин редактора:",
								"html_editor_default_toolbars"=>"Вид панели:"
								];


	protected $properties_item_type=["height"=>0,
								"width"=>0,
								"html_editor_default_theme"=>1,
								"html_editor_default_toolbars"=>1
								];
	protected $itemcount=2;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений, ключи из конфига в виде массива от корня, например, ['public_media_folder']:"];
	protected $properties_listid=[
								'html_editor_default_theme'=>["default"],
					            'html_editor_default_toolbars' =>["default"]
								];

	protected $properties_listtext=[
								'html_editor_default_theme' =>["-"],
								'html_editor_default_toolbars' =>["-"],
						];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	//запишем в сессию конфиг для передачи в ckeditor
	preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	if (count($ar_name))
		{
			$name=$ar_name[1].'-'.$ar_name[2];
		}
		else
			{
				$name=$this->name[0];
			}

		$fck_connector_config = new Container('fck_connector_config');
		$fck_connector_config->Enabled=true;//разрешить загрузку файлов
		$fck_connector_config->FileTypesPath_File=$this->const[0];//путь к файлам и др. 
		$fck_connector_config->FileTypesPath_Image=$this->const[0];//путь к файлам с картинками и др. 
		$js='<br><input type="button" name="'.$this->name[0].'_but" value="Редактор HTML" onClick="window.open(\'/ckeditorf41/'.$name.'?get_interface_input=1\',\'\',\'width=1200,height=780\')">';
	
		$input1 = new Element\Textarea($this->name[0]);
		$input1->setValue($this->value);
		$input1->setAttributes($this->zatr);
		$input1->setAttribute("id",$name);
		return $this->view->FormElement($input1).$js;
}




}
