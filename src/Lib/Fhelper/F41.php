<?php
/*

*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F41 extends Fhelperabstract 
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
	protected $const_count_msg=["Относительный путь к библиотеке изображений:"];
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
		$_SESSION['fck_connector_config']['Enabled']=true;//разрешить загрузку файлов
		$_SESSION['fck_connector_config']['UserFilesPath']=ROOT_URL;//корневой путь к папкам
		$_SESSION['fck_connector_config']['UserFilesAbsolutePath']=ROOT_FILE_SYSTEM;//абсолютный корневой путь
		$_SESSION['fck_connector_config']['FileTypesPath_File']=$this->const[0];//путь к файлам и др. 
		$_SESSION['fck_connector_config']['FileTypesPath_Image']=$this->const[0];//путь к файлам с картинками и др. 
		$_SESSION['fck_connector_config']['FileTypesPath_Flash']=$this->const[0];//путь к файлам с флешем
		$_SESSION['fck_connector_config']['FileTypesPath_Media']=$this->const[0];//путь к файлам с флешем
		preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
		if (count($ar_name))
			{
				$name=$ar_name[1].'-'.$ar_name[2];
			}
			else
				{
					$name=$this->name[0];
				}

		$js="";
		if (!defined("_F36_")) 
			{
				define ("_F36_",1);
				$js='<script src="'.ROOT_URL.ADMIN_FOLDER.'App/View/htmledit/ckeditor.js"></script>';
			}
		$js.='<br><input type="button" name="'.$this->name[0].'_but" value="Редактор HTML" onClick="window.open(\'/admin/App/View/htmledit/index.php?field='.$name.'\',\'\',\'width=1200,height=780\')">';
	return $this->view->formTextArea($this->name[0],$this->value,["style"=>"width:10rem;height:5rem"]).$js;
}




}
