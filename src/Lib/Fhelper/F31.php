<?php
/*
вывод файлов и загрузка новых
*/

namespace Admin\Lib\Fhelper;
//use Filter_Imgresize;
use Admin\Lib\Simba;
use Zend\Form\Element;

class F31 extends Fupload 
{
	protected $hname="Закачка файлов + предосмотр уже имен существующих";
	protected $category=3;
	protected $properties_keys=["names",
								"help",
								"file_array",
								"file_enable_extension",
								"file_max_size",
								"disable_delete",
								"multiupload"
								];
	protected $properties_text=["names"=>"Изменение имени загруженного файла:",
								"help"=>"Вывод справочной информации",
								"file_array"=>"Максимум файлов в одном поле (массив), если 0 или пусто, тогда классически один:",
								"file_enable_extension"=>"Список допустимых типов файлов для загрузки (разделитель символ |):",
								"file_max_size"=>"Максимальный размер файла в байтах, если 0 или пусто, только ограничения PHP:",
								"disable_delete"=>"Не выводить флажок удаления файла",
								"multiupload"=>"Загружать много файлов в виде массива (КРОМЕ IE)"
								];

	protected $properties_item_type=["names"=>1,
								"help"=>1,
								"file_array"=>0,
								"file_enable_extension"=>0,
								"file_max_size"=>0,
								"disable_delete"=>1,
								"multiupload"=>1
								];

	protected $itemcount=2;
	protected $constcount=2;
	protected $const_count_msg=["Внутренняя папка обработки, ключи из конфига в виде массива от корня, например, [\"images\"][\"images_data_folder\"]:",
								"PUBLIC Папка вебсервера, ключи из конфига в виде массива от корня, например, [\"images\"][\"public_folder_url\"]:"];
	protected $properties_listid=[
					            'names' => [0,1,2,3],
								'help' => [0,1,2],
								'disable_delete' => [0,1],
								'multiupload' => [0,1],
								];
protected $properties_listtext=['names'=>[
                    "Не изменять",
                    "Добавить случайные числа в имя",
                    "Все имя файла случайное число",
                    "Все имя уникальная строка"],
            'help'=>[
                    "всю",
                    "нет",
                    "Только имя файла"],
            'disable_delete'=>[
                    "нет",
                    "да"],
            'multiupload'=>[
                    "нет",
                    "да"]					
			];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$this->init();
	$out1="";
	$file_array=($this->properties['file_array']) ? $this->properties['file_array']:1;
	$out='<table border="1" cellspacing="0" cellpadding="0">';
	$vv=explode(',',$this->value);

	for ($i=0;$i<$file_array;$i++)
		{
			$vv[$i]=trim($vv[$i]);
		$nnn=str_replace('[',$i.'[',$this->name[0]);//корректировать имя, что бы сделать псевдомассив внутри ячейки
		
		//добавить крыжики удаления
		if (!empty($vv[$i]))
			{
				$checkbox = new Element\Checkbox("delete_".$nnn);
				$checkbox->setUseHiddenElement(true);
				$checkbox->setCheckedValue(1);
				$checkbox->setUncheckedValue(0);
				$out1.='<br><label>'.$this->view->FormCheckbox($checkbox).'Удалить</label>';
				}
	
	
		//справочно
		$out2='';
		if ($this->properties['help']==0) $out2='<br><span '.$this->atr[1].'>Запись в:'.$this->data_folder.'<br></span><b '.$this->atr[1].'>Файл: '.$vv[$i].'</b>';
		if ($this->properties['help']==2) $out2='<b>Файл: '.$vv[$i].'</b>';
	
	$out.="<tr>";
	if ($file_array>1)  {$out.=" <th width=1>$i:</th>";}
	
	$out.="	<td>".$this->view->FormElement(new Element\File($nnn))."<br>$out2</td>
		<td>$out1</td>
		  </tr>";
		}
	
	$h1 = new Element\Hidden("file_array_".$this->name[0]);
	$h1->setValue($file_array);
	$out.= $this->view->FormElement($h1);

	$h2 = new Element\Hidden("value_array_".$this->name[0]);
	$h2->setValue($this->value);
	$out.= $this->view->FormElement($h2);
	
	return $out.'</table>';


}


public function save()
{
	$this->init();
	if ($this->properties['names']>0) $prefix=rand(); else $prefix='';
	$file_array=$_POST["file_array_".$this->col_name][$this->id];// кол-во подэлементов внутри элемента
	$infa_old=explode (',',$_POST["value_array_".$this->col_name][$this->id]);
	$infa_=array();
	//$img_resize=explode('|',$this->properties['img_resize']);//получить размеры для каждой картинки
	//$this->set_error($row_item,0);
	if ($this->properties['file_enable_extension']>'') {$file_enable_extension=explode('|',$this->properties['file_enable_extension']);}
		else {$file_enable_extension=[];}
	
	for($iq=0;$iq<$file_array;$iq++)
	{
	//проверим флажки удаления, если они установлены, тогда обнуляем элемент
	if (!empty($_POST['delete_'.$this->col_name.$iq][$this->id]))	
		{
			$infa_[$iq]='';
			@unlink ($this->public_folder.$infa_old[$iq]);
			$infa_old[$iq]='';
		}
	
	
	$rez=$this->file_upload(
						array($this->id=>$this->col_name.$iq),
						$this->data_folder,
						$file_enable_extension,
						(int) $this->properties['file_max_size'],//максимальный размер файла
						0666,
						$prefix,
						$this->properties['names']
						);

	if ($rez['error']==0 && $rez['name']>'')
		{
			//проверим, изменилось ли имя файла, если да, тогда старый стереть!
			 @unlink ($this->public_folder.$infa_old[$iq]);
			//ошибки нет, записываем
			$infa_[$iq]=$rez['name'];
	//переносим в PUBLIC папку
		foreach ($infa_ as $img_item)
			{
				if (!rename($this->data_folder.$img_item,$this->public_folder.$img_item)) {echo "<br>Ошибка переноса файла в PUBLIC папку!<br>";}
			}
		}
	
		else  $infa_[$iq]=$infa_old[$iq];
		
	
	}
	$infa=implode(',',$infa_);//упаковать
	$this->infa=$infa;
	return $this->infa;
}


public function del()
{
	$this->init();
	if ($this->col_name  ) 
		{
			$n=simba::queryOneRecord('select '.$this->col_name.' from '.$this->tab_name.' where id='.$this->id);//получить имя файла (может быть список)
			$infa=explode(',',$n[$this->col_name]);
			for ($qi=0;$qi<count($infa);$qi++)		
				{
					@unlink ($this->public_folder.$infa[$qi]);
				}
		}

}

/*
инициализирует пути данного помощника
*/
public function init()
{
	$this->root_file_system=getcwd().DIRECTORY_SEPARATOR;
	$this->public_folder=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR. $this->const[1];
	$this->data_folder=getcwd().DIRECTORY_SEPARATOR.$this->const[0];
	
	if (!is_readable($this->public_folder)) {echo "<br>Папка <b>{$this->public_folder}</b> не существует! Создана!<br>";mkdir($this->public_folder,0777,true);}
	if (!is_readable($this->data_folder)) {echo "<br>Папка <b>{$this->data_folder}</b> не существует! Создана!<br>";mkdir($this->data_folder,0777,true);}

}

}
