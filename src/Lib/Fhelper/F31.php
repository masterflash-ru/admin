<?php
/*
вывод фото и загрузка новых
*/

namespace Admin\Lib\Fhelper;

use Admin\Lib\Simba;
use Laminas\Form\Element;
use Mf\Storage\Service\FilesLib;

class F31 extends Fupload 
{
	protected $hname="закачка файлов в хранилище + просмотр имени загруженного файла";
	protected $category=3;
	protected $properties_keys=["config_section",
								];
	
	protected $properties_text=["config_section"=>"Имя подсекции из конфига приложения из секции 'storage' (Функции интерфейса могут менять это значение): <br>",
							   ];
	
	protected $properties_item_type=["config_section"=>1,
								];

	protected $itemcount=1;
	protected $constcount=0;

						
public function __construct($item_id)
{
    parent::__construct($item_id);
}
	
	
	
public function render()
{
	//извлечем из имени ID строки таблицы, КОСТЫЛИ
	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];

	$img_array=1;
    
	$out='<table border="1" cellspacing="0" cellpadding="0">';
	$vv=explode(',',$this->value);
	$out1="";
	for ($i=0;$i<$img_array;$i++)
		{
		
			$out1.=implode('<br/>',$this->view->FilesStorage($this->properties["config_section"],$id));

			$nnn=str_replace('[',$i.'[',$this->name[0]);//корректировать имя, что бы сделать псевдомассив внутри ячейки
		  //добавить крыжики удаления
		
            $checkbox = new Element\Checkbox("delete_".$nnn);
            $checkbox->setUseHiddenElement(true);
            $checkbox->setCheckedValue(1);
            $checkbox->setUncheckedValue(0);
            $out1.='<br><label>'.$this->view->FormCheckbox($checkbox).'Удалить</label>';


            //создаем элемент ФАЙЛ
            $out.="<tr><td>".$this->view->FormElement(new Element\File($nnn))."</td><td>$out1</td></tr>";
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
	//настройки из конфига приложения
	$image_storage=$this->config['storage'];

	$data_folder=getcwd().DIRECTORY_SEPARATOR.$image_storage["data_folder"].DIRECTORY_SEPARATOR;
	
	if (!is_readable($data_folder)) {
        echo "<br>Папка <b>{$data_folder}</b> не существует! Создана!<br>";
        if (!mkdir($data_folder,0777,true)) {
            echo "Ошибка создания папки ".$data_folder;exit;
        };
    }

	
	$item_key_config_name=$this->properties["config_section"];
	
	$img_array=$_POST["img_array_".$this->col_name][$this->id];// кол-во подэлементов внутри элемента
	$infa_old=explode (',',$_POST["value_array_".$this->col_name][$this->id]);
	$infa_=array();

	
	for($iq=0;$iq<$img_array;$iq++)
	{
        //проверим флажки удаления, если они установлены, тогда обнуляем элемент
        if (!empty($_POST['delete_'.$this->col_name.$iq][$this->id])){
            $this->del();
            $infa_old[$iq]="";
        }

        $rez=$this->file_upload(
                            array($this->id=>$this->col_name.$iq),
                            $data_folder,
                            [],
                            0,//максимальный размер файла
                            0666,
                            "",
                            ""
                            );

        if ($rez['error']==0 && $rez['name']>''){
                $FilesLib=Simba::$container->get(FilesLib::class);
                $FilesLib->deleteFile($item_key_config_name,$this->id);

                //имя нового файла
                $infa_[$iq]=$rez['name'];
                //вариант, когда добавляе новую запись, смотрим следующий ID таблицы, если таблица указана
                if ($this->tab_name && empty($this->id)) {
                    $connection=Simba::$container->get('DefaultSystemDb');
                    $rs=$connection->Execute("SELECT AUTO_INCREMENT
                                                FROM information_schema.tables
                                                WHERE
                                                  table_name = '{$this->tab_name}'
                                                  AND table_schema = DATABASE()");
                    if (!$rs->EOF){
                        $this->id=$rs->Fields->Item['AUTO_INCREMENT']->Value;
                    }
                }

                $FilesLib->selectStorageItem($item_key_config_name);
                $infa_[$iq]=serialize($FilesLib->saveFiles($infa_[$iq],$item_key_config_name,$this->id));
            
        } else {$infa_[$iq]=$infa_old[$iq];}
        }

	$infa=implode(',',$infa_);//упаковать
	$this->infa=$infa;
	return $this->infa;
}


public function del()
{

	$FilesLib=Simba::$container->get(FilesLib::class);
	$FilesLib->deleteFile($this->properties["config_section"],$this->id);

}

/*перегруженный метод
*/
public function Getproperties_listid()
{
    $config=$this->config;
    if (isset($config["storage"]["items"])){
        return ['config_section'=>array_merge([""],array_keys($config["storage"]["items"]))];
    }
	return [];
}

public function Getproperties_listtext()
{
    $config=$this->config;
    $rez=[];
    $rez[]="";
    if (isset($config["storage"]["items"])){
        foreach ($config["storage"]["items"] as $key=>$d){
            if (!empty($d["description"])) {$rez[]=$d["description"];
            } else {$rez[]=$key;}
        }
        return ['config_section'=>$rez];
    }
	return [];
}

}
