<?php
/*
вывод фото и загрузка новых
*/

namespace Admin\Lib\Fhelper;
//use Filter_Imgresize;
use Admin\Lib\Simba;
use Zend\Form\Element;


class F30 extends Fupload 
{
	protected $hname="закачка фото + предосмотр уже существующего фото";
	protected $category=3;
	protected $properties_keys=["names",
								"help",
								"img_array",
								"img_size",
								"file_enable_extension",
								"img_resize_type",
								"img_new_size",
								"file_max_size",
								"watermark",
								"sql_for_delete_foto",
								'wh_kostil'
								];
	
	protected $properties_text=["names"=>"Изменение имени загруженного файла:",
								"help"=>"Вывод справочной информации",
								"img_array"=>"Максимум фото в одном поле (массив), если 0 или пусто, тогда классически одно фото:",
								"img_size"=>"При выводе изображения, преобразовать его к размеру (ширина в точках), 0(пусто) значение по умолчанию, -1 вывод без масштабирования",
								"file_enable_extension"=>"Список допустимых типов файлов для загрузки (разделитель символ |):",
								"img_resize_type"=>"Изменение размеров изображений:",
								"img_new_size"=>"Новый размер (см. параметр выше!):",
								"file_max_size"=>"Максимальный размер файла в байтах, если 0 или пусто, только ограничения PHP:",
								"watermark"=>"Имя файла (с путем) для наложения в виде водяного знака",
								"sql_for_delete_foto"=>"Выбирать SQL имя удаляемого файла:",
								'wh_kostil'=>"Костыльная опция для варианта /Преобразовать точно к размерам (пример, 200x300), вырезается!/"
								];
	
	protected $properties_item_type=["names"=>1,
								"help"=>1,
								"img_array"=>0,
								"img_size"=>0,
								"file_enable_extension"=>0,
								"img_resize_type"=>1,
								"img_new_size"=>0,
								"file_max_size"=>0,
								"watermark"=>0,
								"sql_for_delete_foto"=>1,
								'wh_kostil'=>1
								];

	protected $itemcount=2;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений:"];
	protected $properties_listid=[
					            'names' => [0,1,2,3],
								'help' => [0,1,2],
								'img_resize_type' => ["n","w","h","wh"],
								'sql_for_delete_foto' => [0,1],
								'wh_kostil' => [0,1],
								];

protected $properties_listtext=['names'=>
				[
                    "Не изменять",
                    "Добавить случайные числа в имя",
                    "Все имя файла случайное число",
                    "Все имя уникальная строка"],

            'help'=>[
                    "всю",
                    "нет",
                    "Только имя файла"],

            'img_resize_type'=>[
                    "Ничего не делать",
                    "Преобразовать пропорционально, новый размер (px) ширина",
                    "Преобразовать пропорционально, новый размер (px) высота",
                    "Преобразовать точно к размерам (пример, 200x300), вырезается!"],

            'sql_for_delete_foto'=>[
                    "ДА",
                    "НЕТ"],

            'wh_kostil'=>[
                    //Filter_ImgResize::METHOD_SCALE_FIT_W,
                   // Filter_ImgResize::METHOD_SCALE_FIT_H
				   ]
			];
							
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$img_array=($this->properties['img_array']) ? $this->properties['img_array']:1;
	$out='<table border="1" cellspacing="0" cellpadding="0">';
	$vv=explode(',',$this->value);
	for ($i=0;$i<$img_array;$i++)
		{
			if (empty($vv[$i]) && $this->default_value>'') {$vv[$i]=$this->default_value;}//если пусто, установить значение по умолчанию
			$ss=@getimagesize(ROOT_FILE_SYSTEM.$this->const[0].$vv[$i]);
			$sss=150;
			if (isset($this->properties['img_size']) && !$this->properties['img_size']=='')  {$sss=(int)$this->properties['img_size'];}
			if (isset($this->properties['img_size']) && $this->properties['img_size']==-1)
				{//вывод без масштабирования			
					if (preg_match("/\.swf$/i",$vv[$i])) 
						{
							$out1='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'.$ss[0].'" height="'.$ss[1].'"><param name="allowScriptAccess" value="sameDomain"><param name="PLAY" value="false" /><param name="movie" value="'.ROOT_URL.$this->const[0].$vv[$i].'"><param name="quality" value="high"><embed src="'.ROOT_URL.$this->const[0].$vv[$i].'" quality="high" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'.$ss[0].'" height="'.$ss[1].'" play="false"></embed></object>';
						}
						else 
							{
								if ($vv[$i]) 
									{
										$out1="<img src=\"".ROOT_URL.$this->const[0].$vv[$i]."\">";
									}
								else {$out="";}
								
							}
				}
				else 
					{//масштабируем
						if (preg_match("/\.swf$/i",$vv[$i])) 
							{
								if (empty($ss))
									{
										$ss[0]=300;
										$ss[1]=150;
									}
									else
										{
											if ($ss[0]>0) $ss[1]=ceil($ss[1]*$sss/$ss[0]); else $ss[1]=0;
										}
			
								$out1='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'.$ss[0].'" height="'.$ss[1].'"><param name="allowScriptAccess" value="sameDomain"><param name="PLAY" value="false" /><param name="movie" value="'.ROOT_URL.$this->const[0].$vv[$i].'"><param name="quality" value="high"><embed src="'.ROOT_URL.$this->const[0].$vv[$i].'" quality="high" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'.$ss[0].'" height="'.$ss[1].'" play="false"></embed></object>';
							}
							else 
								{
									if (!empty($vv[$i])) {$out1="<img alt=\"\" src=\"".ROOT_URL.$this->const[0].$vv[$i]."\" style='max-width:".$sss."px'>";}
										else $out1="";
								}
					}
		$nnn=str_replace('[',$i.'[',$this->name[0]);//корректировать имя, что бы сделать псевдомассив внутри ячейки
		//добавить крыжики удаления
		if (!empty($vv[$i]))
			{
				$out1.='<br><label>'.$this->view->formCheckbox("delete_".$nnn,1,NULL,['uncheckedValue'=>0]).'Удалить</label>';}
		//добавить крыжик отмены авто ресайза фото, если конечно авторесайз включен
		//if ($this->properties['img_resize_type']>'') $out1.='<br><span '.$this->atr[1].'>Не менять размер</span><input name="non_resize_'.$nnn.'" type="checkbox" value="1">';
	
	
		//справочно
		$out2='';
		if ($this->properties['help']==0) $out2='<br><span '.$this->atr[1].'>Запись в:'.ROOT_FILE_SYSTEM.$this->const[0].'<br></span><span '.$this->atr[1].'>Файл: '.$vv[$i].', <b>'.$ss[0].'x'.$ss[1].' px</b></span>';
		if ($this->properties['help']==2) $out2='<span '.$this->atr[1].'>Файл: '.$vv[$i].', <b>'.$ss[0].'x'.$ss[1].' px</b></span>';
	
	$out.="<tr>";
	if ($img_array>1)  {$out.=" <th width=1>$i:</th>";}
	
	$out.="	<td>".$this->view->formFile($nnn)."<br>$out2</td>
		<td>$out1</td>
		  </tr>";
		}
	$out.=$this->view->formHidden("img_array_".$this->name[0],$img_array);
	$out.=$this->view->formHidden("value_array_".$this->name[0],$this->value);
	
	//$out.='<input name="-img_array_'.$this->name[0].'" type="hidden" value="'.$img_array.'"><input name="-value_array_'.$this->name[0].'" type="hidden" value="'.$this->value.'">';
	
	return $out.'</table>';


}


public function save()
{
	if ($this->properties['names']>0) $prefix=rand(); else $prefix='';
	$img_array=$_POST["img_array_".$this->col_name][$this->id];// кол-во подэлементов внутри элемента
	$infa_old=explode (',',$_POST["value_array_".$this->col_name][$this->id]);
	$infa_=array();
	//$img_resize=explode('|',$this->properties['img_resize']);//получить размеры для каждой картинки
	//$this->set_error($row_item,0);
	if ($this->properties['file_enable_extension']>'') {$file_enable_extension=explode('|',$this->properties['file_enable_extension']);}
		else {$file_enable_extension=[];}
	
	for($iq=0;$iq<$img_array;$iq++)
	{
	//проверим флажки удаления, если они установлены, тогда обнуляем элемент
	if (!empty($_POST['delete_'.$this->col_name.$iq][$this->id]))	
		{
			$infa_[$iq]='';
			@unlink (ROOT_FILE_SYSTEM.$this->const[0].$infa_old[$iq]);
			$infa_old[$iq]='';
		}
	
	
	$rez=$this->file_upload(
						array($this->id=>$this->col_name.$iq),
						ROOT_FILE_SYSTEM.$this->const[0],
						$file_enable_extension,
						(int) $this->properties['file_max_size'],//максимальный размер файла
						0666,
						$prefix,
						$this->properties['names']
						);

	if ($rez['error']==0 && $rez['name']>'')
		{
		//проверим, изменилось ли имя файла, если да, тогда старый стереть!
		 @unlink (ROOT_FILE_SYSTEM.$this->const[0].$infa_old[$iq]);
		//ошибки нет, записываем
		$infa_[$iq]=$rez['name'];
		
		
			switch ((string)$this->properties['img_resize_type'])
				{
				case 'w':
						{//масштабно по ширине
						$new_wh=preg_split ("/x/i", $this->properties['img_new_size']);//если указана и высота, тогда к урезанию изображения выполнить вырезку краев
						$f=new \Filter_ImgResize(
													array
														(
															'adapter'=>FILTER_IMG_RESIZE_ADAPTER,
															'width' => $new_wh[0],	
															'height' =>(isset($new_wh[1])) ? $new_wh[1] : 1,
															'method' => \Filter_ImgResize::METHOD_SCALE_FIT_W
														)
												);

						$f->filter(ROOT_FILE_SYSTEM.$this->const[0].$infa_[$iq]); //применить фильтр
						break;
						}
				case 'h':
						{//масштабно по высоте
						$new_wh=preg_split ("/x/i", $this->properties['img_new_size']);//если указана и высота, тогда к урезанию изображения выполнить вырезку краев
						$f=new \Filter_ImgResize(
												array
													(
														'adapter'=>FILTER_IMG_RESIZE_ADAPTER,
														'height' => $new_wh[0],	
														'width' =>(isset($new_wh[1])) ? $new_wh[1] : 1,
														'method' => \Filter_ImgResize::METHOD_SCALE_FIT_H
													)
												);

						$f->filter(ROOT_FILE_SYSTEM.$this->const[0].$infa_[$iq]); //применить фильтр
						break;
						}
				case 'wh':
						{
						$new_wh=explode('x',$this->properties['img_new_size']);//получить новые размеры
						  
						 // $wh_method=Filter_ImgResize::METHOD_SCALE_FIT_W;
						 // if (!empty($this->properties['wh_kostil']) )$wh_method=Filter_ImgResize::METHOD_SCALE_FIT_H;
						$f=new \Filter_ImgResize(
													array
															(
																'adapter'=>FILTER_IMG_RESIZE_ADAPTER,
																'height' => $new_wh[1],	
																'width' => $new_wh[0] ,
																'method' =>Filter_ImgResize:: METHOD_SCALE_WH_CROP
															)
												);

						$f->filter(ROOT_FILE_SYSTEM.$this->const[0].$infa_[$iq]); //применить фильтр

						
						break;
						}
				
				
			}
		//проверим надо ли накладывать водяной знак
		if (!empty($this->properties['watermark'])) 
			{
					$f=new \Filter_Watermark(['waterimage'=>ROOT_FILE_SYSTEM.$this->properties['watermark']]);
					$f->filter(ROOT_FILE_SYSTEM.$this->const[0].$infa_[$iq]);

			}
		
		}
	
		else  $infa_[$iq]=$infa_old[$iq];
		
		//if ($rez['error']>0) $this->set_error($row_item,$rez['error']);
	
	}
	$infa=implode(',',$infa_);//упаковать
	$this->infa=$infa;
	return $this->infa;
}


public function del()
{
	if ($this->col_name  && empty($this->properties['sql_for_delete_foto']) ) 
		{
			$n=simba::queryOneRecord('select '.$this->col_name.' from '.$this->tab_name.' where id='.$this->id);//получить имя файла (может быть список)
			$infa=explode(',',$n[$this->col_name]);
			for ($qi=0;$qi<count($infa);$qi++)		
				{
					@unlink (ROOT_FILE_SYSTEM.$this->const[0].$infa[$qi]);
				}
		}

}


}
