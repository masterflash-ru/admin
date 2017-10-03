<?php
/*

*/

namespace Admin\Lib\Fhelper;
//use Filter_Imgresize;
use ZipArchive;
use Admin\Lib\Simba;
use Zend\Form\Element;


class F43 extends Fupload 
{
	protected $hname="НЕ РАБОТАЕТ! Закачка баннеров HTML5 и просмотр существующих";
	protected $category=100;

	protected $itemcount=2;
	protected $constcount=1;

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$v=unserialize($this->value);

	$out="<label>ZIP файл с банером HTML5: ".$this->view->formFile("zip_".$this->name[0])."</label><br>\n";
	$out.=$this->view->formHidden($this->name[0],htmlspecialchars($this->value,ENT_QUOTES));

	if (is_array($v) && count($v)>1)
		{//генерируем фрейм для вывода
			$out.='<iframe frameborder="0" scrolling="no" width="'.$v["width"].'" height="'.$v["height"].'" src="/pic/banner/'.$v["folder"].'/'.$v["html"].'"></iframe>';
		}
		
	return $out;


}


public function save()
{
$v=unserialize($this->infa);

if (!empty($_FILES["zip_".$this->col_name]["name"][$this->id]))
{
	if (is_array($v)  && isset($v['folder']) && $v['folder'] && is_dir(ROOT_FILE_SYSTEM."pic/banner/".$v['folder']))
		{
			$path=ROOT_FILE_SYSTEM."pic/banner/".$v['folder'];
			//удалим все в папке
			$this->delFolder($path);
			$path=ROOT_FILE_SYSTEM."pic/banner/".$v['folder'];
			mkdir($path,0777);
		}
		else
			{//создаем новую папку
				$folder=md5(microtime());
				$path=ROOT_FILE_SYSTEM."pic/banner/".$folder;
				mkdir($path,0777);
				$v["folder"]=$folder;
			}
	if (!is_dir(ROOT_FILE_SYSTEM."pic/banner/".$v['folder'])) {mkdir(ROOT_FILE_SYSTEM."pic/banner/".$v['folder'],0777);}
	if ($_FILES["zip_".$this->col_name]["error"][$this->id] == UPLOAD_ERR_OK) 
		   		{
					$zip_file=md5(1000+microtime(true));
       				if (!move_uploaded_file( $_FILES["zip_".$this->col_name]["tmp_name"][$this->id],$path."/".$zip_file.".zip")) {echo "Ошибка загрузки файла";}
			  		//разворачиваем zip
					$zip_file=$path."/".$zip_file.".zip";
					$zip = new ZipArchive();
					if ($zip->open($zip_file) === TRUE) 
						{
							$zip->extractTo($path);
							$zip->close();
							$this->delFolder($path."/__MACOSX");
							unlink($zip_file);
							$d=glob($path."/*.htm*");
							if (count($d)>0)
								{
									$v["html"]=basename($d[0]);
									$meta=get_meta_tags($d[0]);
									if (isset($meta["ad_size"]))
										{
											
											//извлекаем размеры
											foreach (explode(",",$meta["ad_size"]) as $m)
												{
													$mm=explode("=",$m);
													if ($mm[1]) {$v[$mm[0]]=$mm[1];}
												}
										}
										else {echo "Нет метатега ad_size ";}
									
								}
								else {echo "в архиве нет файла html";}
						} 
						else {echo 'ошибка разархивации, возможно файл не является ZIP архивом';}					
				  }
$this->infa=serialize($v);

}
return $this->infa;

}


public function del()
{
	if ($this->col_name  ) 
		{
			$n=simba::queryOneRecord('select '.$this->col_name.' from '.$this->tab_name.' where id='.$this->id);//получить имя файла (может быть список)
			$v=unserialize($n[$this->col_name]);
			if (isset($v['folder']) && $v['folder'])
				{
					$path=ROOT_FILE_SYSTEM."pic/banner/".$v['folder'];
					$this->delFolder($path);
				}
		simba::queryOneRecord("update {$this->tab_name} set {$this->col_name}='".serialize([])."' where id={$this->id}");
		}

}



/**
* recursive delete folder
* @param $dir
* @return bool
*/
protected function delFolder($dir)
{
	if (!is_readable($dir)) {return;}
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) 
		{
			(is_dir("$dir/$file")) ? $this->delFolder("$dir/$file") : unlink("$dir/$file");
		}
	return rmdir($dir);
}

}
