<?php
/*
вывод фото и загрузка новых
*/

namespace Admin\Lib\Fhelper;

use Admin\Lib\Simba;
use Zend\Form\Element;
use Images\Service\ImagesLib;

//use Images\Filter\ImgResize;
//use Images\Filter\Watermark;
//use Images\Filter\ImgOptimize;

class F32 extends Fupload 
{
	protected $hname="закачка фото в хранилище + предосмотр уже существующего фото";
	protected $category=3;
	protected $properties_keys=["config_section",
								"admin_img_name",
								"img_array",
								];
	
	protected $properties_text=["config_section"=>"Имя подсекции из конфига приложения из секции 'images_storage' (Функции интерфейса могут менять это значение):",
								"admin_img_name"=>"Имя элемента выводимого из хранилища, если пусто, то 'admin_img'",
								"img_array"=>"НЕ РАБОТАЕТ!!!!! Максимум фото в одном поле (массив), если 0 или пусто, тогда классически одно фото:",
							   ];
	
	protected $properties_item_type=["config_section"=>1,
									 "admin_img_name"=>1,
								"img_array"=>0,
								];

	protected $itemcount=1;
	protected $constcount=0;

	protected $properties_listid=[];

protected $properties_listtext=[];

	//protected $root_file_system;		//абсолютный путь к корню приложения
	protected $data_folder;				//абсолютный путь к временной папке
	protected $public_folder;			//абсолютный путь к папке публикации веб

						
public function __construct($item_id)
{
		parent::__construct($item_id);
		
}
	
	
	
public function render()
{
	$this->init();

	//извлечем из имени ID строки таблицы, КОСТЫЛИ
	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
	
	if (empty($this->properties['admin_img_name'])) 
	{
		$this->properties['admin_img_name']="admin_img";
	}
	
	
	$img_array=($this->properties['img_array']) ? $this->properties['img_array']:1;
	$out='<table border="1" cellspacing="0" cellpadding="0">';
	$vv=explode(',',$this->value);
	$out1="";
	for ($i=0;$i<$img_array;$i++)
		{
		
			$out1.="<img src='".$this->view->imagestorage($this->properties["config_section"],$id,$this->properties['admin_img_name'],$this->default_value)."' />";

			$nnn=str_replace('[',$i.'[',$this->name[0]);//корректировать имя, что бы сделать псевдомассив внутри ячейки
		//добавить крыжики удаления
		
				$checkbox = new Element\Checkbox("delete_".$nnn);
				$checkbox->setUseHiddenElement(true);
				$checkbox->setCheckedValue(1);
				$checkbox->setUncheckedValue(0);
				$out1.='<br><label>'.$this->view->FormCheckbox($checkbox).'Удалить</label>';

	
	$out.="<tr>";
	if ($img_array>1)  {$out.=" <th width=1>$i:</th>";}
	
	
	//создаем элемент ФАЙЛ
	$out.="	<td>".$this->view->FormElement(new Element\File($nnn))."</td>
		<td>$out1</td>
		  </tr>";
		}

	$h1 = new Element\Hidden("img_array_".$this->name[0]);
	$h1->setValue($img_array);
	$out.= $this->view->FormElement($h1);

	$h2 = new Element\Hidden("value_array_".$this->name[0]);
	$h2->setValue($this->value);
	$out.= $this->view->FormElement($h2);


	return $out.'</table>';


}


public function save()
{
	$this->init();

	
	$item_key_config_name=$this->properties["config_section"];
	
	$img_array=$_POST["img_array_".$this->col_name][$this->id];// кол-во подэлементов внутри элемента
	$infa_old=explode (',',$_POST["value_array_".$this->col_name][$this->id]);
	$infa_=array();

	
	for($iq=0;$iq<$img_array;$iq++)
	{
	//проверим флажки удаления, если они установлены, тогда обнуляем элемент
	if (!empty($_POST['delete_'.$this->col_name.$iq][$this->id])  )	
		{
			$this->del();
		}
		
	
	
	$rez=$this->file_upload(
						array($this->id=>$this->col_name.$iq),
						$this->data_folder,
						[],
						0,//максимальный размер файла
						0666,
						"",
						""//$this->properties['names']
						);

	if ($rez['error']==0 && $rez['name']>'')
		{
			//проверим, изменилось ли имя файла, если да, тогда старый стереть!
			 if ($this->public_folder) {@unlink ($this->public_folder.$infa_old[$iq]);}
			//ошибки нет, записываем
			$infa_[$iq]=$rez['name'];

			$newfile=$infa_[$iq];
			
			$item_from_config=$this->config['images_storage']['items'][$item_key_config_name];
			
			$ImagesLib=Simba::$container->get(ImagesLib::class);
			$ImagesLib->setMediaInfo($item_from_config);
			$ImagesLib->saveImages($newfile,$item_key_config_name,$this->id);
		}
		else  $infa_[$iq]=$infa_old[$iq];
	}
	
	$infa=implode(',',$infa_);//упаковать
	$this->infa=$infa;
	return $this->infa;
}


public function del()
{

	$ImagesLib=Simba::$container->get(ImagesLib::class);
	$ImagesLib->deleteImage($this->properties["config_section"],$this->id);

}

/*
инициализирует пути данного помощника
*/
public function init()
{
	//настройки из конфига приложения
	$image_storage=$this->config['images_storage'];
	
	
	$this->data_folder=getcwd().DIRECTORY_SEPARATOR.$image_storage["images_data_folder"].DIRECTORY_SEPARATOR;
	
	if (!is_readable($this->data_folder)) {echo "<br>Папка <b>{$this->data_folder}</b> не существует! Создана!<br>";mkdir($this->data_folder,0777,true);}
}
}
