<?php

/*
редактор HTML в тек окне с записью в файл
*/

namespace Admin\Lib\Fhelper;
use Admin\Lib\Simba;
use Zend\Form\Element;
use Zend\Session\Container;

class F36 extends Fhelperabstract 
{
	protected $hname="Окно-HTML редактор с записью в файл";
	protected $category=4;
	protected $properties_keys=["height","width","html_editor_default_theme","html_editor_default_toolbars","table_name","table_id"];
	protected $properties_text=[
								"height"=>"Высота окна:",
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

	protected $itemcount=1;
	protected $constcount=2;
	protected $const_count_msg=["Относительный путь к библиотеке изображений:","Относительный путь записи файла HTML:"];
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
	
/*
$this->const[1] - константа относительно корня приложения!!!
$this->const[0] - константа относительно корня веб public папки!!!
*/
public function render()
{
	//создадим папку, если ее нет
	if (!is_readable($this->const[1]) )
		{
			//если папок нет, создаем
			mkdir($this->const[1],0777,true);
		}

	
	$data="";
	if ($this->value)
		 {//используется массив $properties - высота и ширина окна
			$data=file_get_contents($this->const[1].$this->value);
			$data=stripslashes ($data);
		 }
	
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
		
		$input = new Element\Text($this->name[0]);
		$input->setValue($this->value);
		$input->setAttributes($this->zatr);//\Zend\Debug\Debug::dump($input);exit;

		$input1 = new Element\Textarea("data_".$this->name[0]);
		$input1->setValue($data);
		$input1->setAttribute("class","ckeditor");

	return "Имя файла:".$this->view->FormElement($input).$this->view->FormElement($input1).$js;
			//$this->view->formTextArea("data_".$this->name[0],$data,["class"=>"ckeditor"]).$js;
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
					unlink($this->const[1].$n[$this->col_name]);
				}
		
		if (!$this->infa) {$this->infa="rand_".rand().'.html'; }
		
		file_put_contents($this->const[1].$this->infa,$_POST['data_'.$this->col_name][$this->id]);
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
	unlink($this->const[1].$n[$this->col_name]);

	}
}


}
