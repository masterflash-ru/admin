<?php
/*
вывод фото и загрузка новых
*/

namespace Admin\Lib\Fhelper;

use Admin\Lib\Simba;
use Zend\Form\Element;
use Mf\Imglib\Filter\ImgResize;
use Mf\Imglib\Filter\Watermark;
use Mf\Imglib\Filter\ImgOptimize;

class F30 extends Fupload 
{
	protected $hname="РУЧНАЯ закачка фото + предосмотр уже существующего фото";
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
								'images_optimize',
								'public_to_public'
								];
	
	protected $properties_text=["names"=>"Изменение имени загруженного файла:",
								"help"=>"Вывод справочной информации",
								"img_array"=>"Максимум фото в одном поле (массив), если 0 или пусто, тогда классически одно фото:",
								"img_size"=>"При выводе изображения, преобразовать его к размеру (ширина в точках), 0(пусто) значение по умолчанию, -1 вывод без масштабирования",
								"file_enable_extension"=>"Список допустимых типов файлов для загрузки (разделитель символ |):",
								"img_resize_type"=>"Изменение размеров изображений:",
								"img_new_size"=>"Новый размер (см. параметр выше!):",
								"file_max_size"=>"Максимальный размер файла в байтах, если 0 или пусто, только ограничения PHP:",
								"watermark"=>"Имя файла (с путем Относительно папки указаной в первой константе!) для наложения в виде водяного знака ",
								"sql_for_delete_foto"=>"Выбирать SQL имя удаляемого файла:",
								'images_optimize'=>"Оптимизация изображения:",
								'public_to_public'=>'Загружать файл в публичную папку веба (влияет на удаление!)'
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
								'images_optimize'=>1,
								'public_to_public'=>1
								];

	protected $itemcount=2;
	protected $constcount=2;
	protected $const_count_msg=["Внутренняя папка обработки, ключи из конфига в виде массива от корня, например, [\"images\"][\"images_data_folder\"]:",
								"PUBLIC Папка вебсервера, ключи из конфига в виде массива от корня, например, [\"images\"][\"public_folder_url\"]<br>ЕСЛИ ПУСТО, то обработку дальше должна делать сторонняя функция:"];
	protected $properties_listid=[
					            'names' => [0,1,2,3],
								'help' => [0,1,2],
								'img_resize_type' => ["n","w","h","wh"],
								'sql_for_delete_foto' => [0,1],
								'images_optimize' => [0,1],
								'public_to_public'=>[0,1]
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

            'images_optimize'=>[
                    "Нет",
                   "Да"
				   ],
            'public_to_public'=>[
                    "Нет",
                   "Да"
				   ]
		
			];

	protected $root_file_system;		//абсолютный путь к корню приложения
	protected $data_folder;				//абсолютный путь к временной папке
	protected $public_folder;			//абсолютный путь к папке публикации веб
						
public function __construct($item_id)
{
		parent::__construct($item_id);
		
}
	
	
	
public function render()
{
	$this->init();

	$img_array=($this->properties['img_array']) ? $this->properties['img_array']:1;
	$out='<table border="1" cellspacing="0" cellpadding="0">';
	$vv=explode(',',$this->value);
	for ($i=0;$i<$img_array;$i++){
        if (empty($vv[$i]) && $this->default_value>'') {
            //если пусто, установить значение по умолчанию
            $vv[$i]=$this->default_value;
        }
        $ss=@getimagesize($this->root_file_system.$this->const[1].$vv[$i]);
        $sss=150;
        if (isset($this->properties['img_size']) && !$this->properties['img_size']=='') {
            $sss=(int)$this->properties['img_size'];
        }
        if (isset($this->properties['img_size']) && $this->properties['img_size']==-1) {
            //вывод без масштабирования
            if ($vv[$i]) {
                $out1="<img src=\"/".$this->const[1].$vv[$i]."\">";
            } else {
                $out="";
            }
        } else {//масштабируем
            if (!empty($vv[$i])) {
                $out1="<img alt=\"\" src=\"/".$this->const[1].$vv[$i]."\" style='max-width:".$sss."px'>";
            } else {
                $out1="";
            }
        }
        $nnn=str_replace('[',$i.'[',$this->name[0]);//корректировать имя, что бы сделать псевдомассив внутри ячейки
		//добавить крыжики удаления
		
		if (!empty($vv[$i])) {
            $checkbox = new Element\Checkbox("delete_".$nnn);
            $checkbox->setUseHiddenElement(true);
            $checkbox->setCheckedValue(1);
            $checkbox->setUncheckedValue(0);
            $out1.='<br><label>'.$this->view->FormCheckbox($checkbox).'Удалить</label>';
        }
	
		//справочно
		$out2='';
		if ($this->properties['help']==0) {
            $out2='<br><b>Папка:'.$this->data_folder.'<br>Файл: '.$vv[$i].', '.$ss[0].'x'.$ss[1].' px</b>';
        }
		if ($this->properties['help']==2) {
            $out2='<b>Файл: '.$vv[$i].', '.$ss[0].'x'.$ss[1].' px</b>';
        }
        $out.="<tr>";
        if ($img_array>1)  {
            $out.=" <th width=1>$i:</th>";
        }
        //создаем элемент ФАЙЛ
        $out.="	<td>".$this->view->FormElement(new Element\File($nnn))."<br>$out2</td><td>$out1</td></tr>";
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
	if ($this->properties['names']>0) $prefix=rand(); else $prefix='';
	$img_array=$_POST["img_array_".$this->col_name][$this->id];// кол-во подэлементов внутри элемента
	$infa_old=explode (',',$_POST["value_array_".$this->col_name][$this->id]);
	$infa_=array();

	if ($this->properties['file_enable_extension']>'') {
        $file_enable_extension=explode('|',$this->properties['file_enable_extension']);
    } else {
        $file_enable_extension=[];
    }
	
	for($iq=0;$iq<$img_array;$iq++){
        //проверим флажки удаления, если они установлены, тогда обнуляем элемент
        if (!empty($_POST['delete_'.$this->col_name.$iq][$this->id]) && $this->public_folder ) {
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
        if ($rez['error']==0 && $rez['name']>''){
            //проверим, изменилось ли имя файла, если да, тогда старый стереть!
            if ($this->public_folder) {
                @unlink ($this->public_folder.$infa_old[$iq]);
            }
            //ошибки нет, записываем
            $infa_[$iq]=$rez['name'];
            
            $FILTER_IMG_RESIZE_ADAPTER="Gd";
            
            switch ((string)$this->properties['img_resize_type']){
                case 'w':{//масштабно по ширине
                    $new_wh=preg_split ("/x/i", $this->properties['img_new_size']);//если указана и высота, тогда к урезанию изображения выполнить вырезку краев
                    $f=new ImgResize(array
                                     ('adapter'=>$FILTER_IMG_RESIZE_ADAPTER,
                                      'width' => $new_wh[0],	
                                      'height' =>(isset($new_wh[1])) ? $new_wh[1] : 1,
                                      'method' => IMG_METHOD_SCALE_FIT_W
                                     )
                                    );
                    $f->filter([$this->data_folder.$infa_[$iq]]); //применить фильтр
                    break;
                }
				case 'h':{//масштабно по высоте
                    $new_wh=preg_split ("/x/i", $this->properties['img_new_size']);//если указана и высота, тогда к урезанию изображения выполнить вырезку краев
                    $f=new ImgResize(array
                                     (
                                         'adapter'=>$FILTER_IMG_RESIZE_ADAPTER,
                                         'height' => $new_wh[0],	
                                         'width' =>(isset($new_wh[1])) ? $new_wh[1] : 1,
                                         'method' => IMG_METHOD_SCALE_FIT_H
                                     )
                                    );
                    $f->filter([$this->data_folder.$infa_[$iq]]); //применить фильтр
                    break;
                }
				case 'wh':{
                    $new_wh=explode('x',$this->properties['img_new_size']);//получить новые размеры
                    $f=new ImgResize(array
                                     (
                                         'adapter'=>$FILTER_IMG_RESIZE_ADAPTER,
                                         'height' => $new_wh[1],	
                                         'width' => $new_wh[0] ,
                                         'method' =>IMG_METHOD_SCALE_WH_CROP
                                     )
                                    );
                    $f->filter([$this->data_folder.$infa_[$iq]]); //применить фильтр
                    break;
                }
            }
            //проверим надо ли накладывать водяной знак
            if (!empty($this->properties['watermark'])) {
                $f=new Watermark(['waterimage'=>$this->data_folder.$this->properties['watermark']]);
                $f->filter($this->data_folder.$infa_[$iq]);
            }
            //проверим надо ли оптимизировать изображение
            if (!empty($this->properties['images_optimize'])) {
                $f=new ImgOptimize($this->config["images"]["images_optimize"]);
                $f->filter($this->data_folder.$infa_[$iq]);
            }
            //переносим в PUBLIC папку если она указана
            if ($this->public_folder && !empty($this->properties['public_to_public'])){
                foreach ($infa_ as $img_item){
                    if (!rename($this->data_folder.$img_item,$this->public_folder.$img_item)) {
                        echo "<br>Ошибка переноса файла в PUBLIC папку!<br>";
                    }
                }
			}
        } else {
            $infa_[$iq]=$infa_old[$iq];
        }
    }
	$infa=implode(',',$infa_);//упаковать
	$this->infa=$infa;
	return $this->infa;
}


public function del()
{
	$this->init();

	if ($this->col_name  && empty($this->properties['sql_for_delete_foto']) && $this->public_folder  && !empty($this->properties['public_to_public'])){
        $n=simba::queryOneRecord('select '.$this->col_name.' from '.$this->tab_name.' where id='.$this->id);//получить имя файла (может быть список)
        $infa=explode(',',$n[$this->col_name]);
        for ($qi=0;$qi<count($infa);$qi++) {
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
	$this->data_folder=getcwd().DIRECTORY_SEPARATOR.$this->const[0];
	
	if (!is_readable($this->data_folder)) {
        echo "<br>Папка <b>{$this->data_folder}</b> не существует! Создана!<br>";mkdir($this->data_folder,0777,true);
    }
    $this->public_folder=null;
    if ($this->const[1]){
        $this->public_folder=$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR. $this->const[1];
        if (!is_readable($this->public_folder)) {
            echo "<br>Папка <b>{$this->public_folder}</b> не существует! Создана!<br>";mkdir($this->public_folder,0777,true);
        }
    }

}
}
