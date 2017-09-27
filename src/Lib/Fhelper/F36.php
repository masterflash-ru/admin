<?php

/*
редактор HTML в тек окне с записью в файл
*/

namespace Admin\Lib\Fhelper;
use Admin\Lib\Simba;
use Zend\Form\Element;

class F36 extends Fhelperabstract 
{
	protected $hname="Окно-HTML редактор с записью в файл";
	protected $category=4;
	protected $properties_keys=["height","width","html_editor_default_theme","html_editor_default_toolbars","table_name","table_id"];
	protected $properties_text=["height"=>"Высота окна:",
								"width"=>"Ширина окна:",
								"html_editor_default_theme"=>"Скин редактора:",
								"html_editor_default_toolbars"=>"Вид панели:",
								"table_name"=>"КОСТЫЛИ! имя таблицы где хранится имя файла",
								"table_id"=>"КОСТЫЛИ! имя поля-ключа таблицы где хранится имя файла"
								];

	protected $properties_item_type=["height"=>0,
								"width"=>0,
								"html_editor_default_theme"=>1,
								"html_editor_default_toolbars"=>1,
								"table_name"=>0,
								"table_id"=>0
								];

	protected $itemcount=2;
	protected $constcount=2;
	protected $const_count_msg=["Относительный путь к библиотеке изображений:","Относительный путь записи файла:"];
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
	$data="";
	if ($this->value)
		 {//используется массив $properties - высота и ширина окна
			$data=file_get_contents(ROOT_FILE_SYSTEM.$this->const[1].$this->value);
			$data=stripslashes ($data);
		 }
		$_SESSION['fck_connector_config']['Enabled']=true;//разрешить загрузку файлов
		$_SESSION['fck_connector_config']['UserFilesPath']=ROOT_URL;//корневой путь к папкам
		$_SESSION['fck_connector_config']['UserFilesAbsolutePath']=ROOT_FILE_SYSTEM;//абсолютный корневой путь
		$_SESSION['fck_connector_config']['FileTypesPath_File']=$this->const[0];//путь к файлам и др. 
		$_SESSION['fck_connector_config']['FileTypesPath_Image']=$this->const[0];//путь к файлам с картинками и др. 
		$_SESSION['fck_connector_config']['FileTypesPath_Flash']=$this->const[0];//путь к файлам с флешем
		$_SESSION['fck_connector_config']['FileTypesPath_Media']=$this->const[0];//путь к файлам с флешем
		
		$js="";
		if (!defined("_F36_")) 
			{
				define ("_F36_",1);
				$js='<script src="'.ROOT_URL.ADMIN_FOLDER.'App/View/htmledit/ckeditor.js"></script>';
			}

	return "Имя файла:".$this->view->formText($this->name[0],$this->value,$this->zatr).
			$this->view->formTextArea("data_".$this->name[0],$data,["class"=>"ckeditor"]).$js;
}


/*обработчик записи, возвращает обработанное*/
public function save()
{
		if (!empty($this->properties['table_name'])) $ttt=$this->properties['table_name'];
				else $ttt=$this->tab_name;
		
		if (!empty($this->properties['table_id'])) $ttt_id=$this->properties['table_id'];
				else $ttt_id="id";
		if (empty($_SESSION["LOCALE_ADMIN_KOSTIL1"])) $_SESSION["LOCALE_ADMIN_KOSTIL1"]="ru_RU";
		
		
		$n=simba::queryOneRecord('select '.$this->col_name.' from '.$ttt.' where locale="'.$_SESSION["LOCALE_ADMIN_KOSTIL1"].'" and '.$ttt_id.'="'.$this->id.'"');//получить значение (имя файла) которое было до этой операции
		
		if ($this->infa && !empty($n[$this->col_name]))
				{//флаг установлен, проверяем что было в таблице, если не пусто, удаолим этот файл
					unlink(ROOT_FILE_SYSTEM.$this->const[1].$n[$this->col_name]);
				}
		
		if (!$this->infa) {$this->infa="rand_".rand().'.html'; }
		
		file_put_contents(ROOT_FILE_SYSTEM.$this->const[1].$this->infa,$_POST['data_'.$this->col_name][$this->id]);
		return $this->infa;
}

/*удаление*/
public function del()
{

	if (empty($_SESSION["LOCALE_ADMIN_KOSTIL1"])) {$_SESSION["LOCALE_ADMIN_KOSTIL1"]="ru_RU";}

	if ($this->col_name) 
	{
		if (!empty($this->properties['table_name'])) $ttt=$this->properties['table_name'];
				else $ttt=$this->tab_name;
		if (!empty($this->properties['table_id'])) $ttt_id=$this->properties['table_id'];
				else $ttt_id="id";

	$n=simba::queryOneRecord('select '.$this->col_name.' from '.$ttt.' where locale="'.$_SESSION["LOCALE_ADMIN_KOSTIL1"].'" and '.$ttt_id.'='.$this->id);//получить имя файла
	unlink(ROOT_FILE_SYSTEM.$this->const[1].$n[$this->col_name]);

	}
}


}
