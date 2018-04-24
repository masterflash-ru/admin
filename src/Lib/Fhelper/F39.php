<?php
/*
редактор HTML в тек окне с записью в базу
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Zend\Session\Container;

class F39 extends Fhelperabstract 
{
	protected $hname="Окно-HTML редактор с записью в базу";
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
		$fck_connector_config = new Container('fck_connector_config');
		$fck_connector_config->Enabled=true;//разрешить загрузку файлов
		$fck_connector_config->FileTypesPath_File=$this->const[0];//путь к файлам и др. 
		$fck_connector_config->FileTypesPath_Image=$this->const[0];//путь к файлам с картинками и др. 
		
		$js="";
		if (!defined("_F36_")) 
			{
				define ("_F36_",1);
				$js='<script src="/htmledit/ckeditor.js"></script>';
			}
		$input1 = new Element\Textarea($this->name[0]);
		$input1->setValue($this->value);
		$input1->setAttribute("class","ckeditor");

	return $this->view->FormElement($input1).$js;
}




}
