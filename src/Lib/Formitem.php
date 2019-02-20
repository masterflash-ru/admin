<?php
//23.4.17 - перешли на рельсы объектов, большая часть генерируется через ZEND, готовим все для ZF3

//21.4.17 - добавлен параметр view из ZEND, для генерации полей этим фреймворком

//поправлен регистр передаваемой строки имени кодировки в iconv, в PHP7 это вызывает крах
namespace Admin\Lib;
use Exception;

class Formitem 
{
	
	//переходная версия 
	//Более не поддерживается
	
	
	
/*

01.08.2013 - теперь при удалении передаются параметры properties как и при записи



убрано предупреждение при возврате кода ошибки если ошибки нет


убраны функции серии ereg - для перехода в PHP6
Наведен порядок с ошибками типа Notice


*/

public  $form_name="form_1";//имя формы
public $error_code=[];//код ошибки входных данных, ключи массива - имена полей (POST)
public $error_message=[];//текстовые сообщения об ошибках


public $errors=[];//хранит тексты ошибок обычно загружаемых данных, ключи массива - номера ошибок
public $date_time_locale_format=[];//массив с форматом даты-времени данной локали
public $view;
public $config;		//конфиг приложения
	public $connection;	//соединение с базой

public function __construct ($view,$config)
{//конструктор
	$this->config=$config;
    //$this->connection=$connection;
	$this->date_time_locale_format=[
									"date_time_format"=>"%d.%m.%Y %H:%M:%S",
									"date_format"=>"%d.%m.%Y",
									"time_format"=>"%H:%M:%S"
									];
	$this->view=$view;
}


public function get_status($item_name)
{//получить код ошибки и сообщение на входе имя элмента

if (!empty($this->error_code[$item_name]) && !empty($this->error_message[$item_name]))
		return array(
				'code'=>$this->error_code[$item_name],
				'message'=>$this->error_message[$item_name]
					);
return array(
				'code'=>0,
				'message'=>""
					);
}



public function get_items_name_id()
{//получить список идентификаторов и имен
/*
Категории элементов:
1 - простые поля ввода
2 - выбор вариантов
3 файлы
4 HTML редактор
5 Дата время
6 Кнопки 
7 Изображения
100 прочее
*/
	$helper_files=glob(__DIR__."/Fhelper/F[1234567890]*.php");
	sort($helper_files,SORT_NATURAL);
	$id=[];
	$name=[];
	$category=[];
	foreach ($helper_files as $item)
		{
			$item=array_reverse(explode(DIRECTORY_SEPARATOR,$item));
			$item=explode(".",$item[0]);
			$fn="\\Admin\\Lib\\Fhelper\\".$item[0];
			$f_id=(int)str_replace("F","",$item[0]);
			$f=new $fn($f_id);
			$f->SetView($this->view);
			
			$id[$f_id]=$f_id;
			$name[$f_id]=$f_id."-".$f->Gethname();
			$category[$f_id]=$f->Getcategory();
		}

return [
	'id'=>$id,
	'name'=>$name,
	'category'=>$category,
	'category_list'=>array(1,2,3,4,5,6,7,100,101),
	'category_list_name'=>["простые поля ввода","выбор вариантов","файлы","HTML редактор","Дата время","Кнопки","Изображения","прочее","Специальные для каталога/магазина"]
	];
}

/*получение списка разных параметров полей для конструкторов интерфейсов*/
public function get_pole_consts_styles()
{
	$helper_files=glob(__DIR__."/Fhelper/F[1234567890]*.php");

	sort($helper_files,SORT_NATURAL);
	$itemcount=[];
	$constcount=[];
	$const_count_msg=[];
	$properties_listid=[];
	$properties_listtext=[];
	$properties_text=[];
	$properties_item_type=[];
	$itemtype=[];
	foreach ($helper_files as $item)
		{
			$item=array_reverse(explode(DIRECTORY_SEPARATOR,$item));
			$item=explode(".",$item[0]);
			$fn="\\Admin\\Lib\\Fhelper\\".$item[0];
			$f_id=(int)str_replace("F","",$item[0]);
			$f=new $fn($f_id);
			$f->SetView($this->view);
			$f->setConfig($this->config);
			
			$itemcount[$f_id]=$f->Getitemcount();
			$constcount[$f_id]=$f->Getconstcount();
			$const_count_msg[$f_id]=$f->Getconst_count_msg();
			$properties_listid[$f_id]=$f->Getproperties_listid();
			$properties_listtext[$f_id]=$f->Getproperties_listtext();
			$properties_text[$f_id]=$f->Getproperties_text();
			$properties_item_type[$f_id]=$f->Getproperties_item_type();
			$itemtype[$f_id]=$f->Getitemtype();
		}

return
[
	'itemcount'=>$itemcount,//кол-во элементов HTML внутри
	'itemtype'=>$itemtype, 
	'constcount'=>$constcount, 												//кол-во констант
	'const_count_msg'=>$const_count_msg,								//кол-во текстовых сообщений для ввода констант
	'properties_listid'=>$properties_listid,
	'properties_listtext'=>$properties_listtext,
	'properties_text'=>$properties_text,
	'properties_item_type'=>$properties_item_type
]
;
	
	
}


//выборка их XML описателя  
public function create_form_item (
    $item_id,               //1
    $name_,                 //2
    $value,                 //3
    $atr_,                  //4
    $sp,                    //5
    $sp_id,                 //6
    $sp_group_array,        //7
    $c,                     //8
    $default_value='',      //9
    $properties_=[],        //10
    $line_row_type='',      //11
    $default_text='',       //12
    $any_values=[]          //13
)
{
	$name=@explode (",",$name_);
	$atr=@explode (",",$atr_);
	$const=@explode (",",$c);
//$any_value_in_row - произвольные данные для поля, данный объект это никак не трогает!
//т.к. парсер работает в UTF-8, то исходные данные конвертируем, т.к. полученные тэги будут именно в UTF-8!!!!! потом ВСЕ конвертируется обратно в исходную кодировку
$item_id=(int)$item_id;
$id=$item_id;
$row_item=$id;


	$f="\\Admin\\Lib\\Fhelper\\F".$item_id;
	$f=new $f($item_id);
	$f->SetView($this->view);
	
	$f->setConfig($this->config);
$item_count=$f->Getitemcount();

//применим атрибуты имя
$zatr=[];
$zselect=[];
for ($i=0;$i<$item_count;$i++)
	{
		if (!isset($atr[$i])) $atr[$i]='';

		//костыли для ZEND
		$zatr=[];
		foreach ($atr as $a)
			{
				foreach (explode(" ",$a) as $a1)
					{
						if ($a1)
							{
								$a2=explode("=",$a1);
								$zatr[$a2[0]]=trim($a2[1],'"\'');
							}
					}
			}
		$zselect=[];$zselecti=[];
		if (is_array($sp_id) && is_array($sp)){
            if (empty($sp_group_array))	{//простой список
                foreach ($sp_id as $k=>$v){
                    if (is_null($k)){
                        $k="null";
                    }
                    if (is_null($v)){
                        $v="null";
                    }

                    $zselect[$v]=$sp[$k];
                }
            } else {//с элементами options
                foreach ($sp_group_array as $zk=>$zgr) {
                    foreach ($sp_id[$zk] as $k=>$v)	{
                        if (is_null($k)){
                            $k="null";
                        }
                        if (is_null($v)){
                            $v="null";
                        }

                        $zselecti[$v]=$sp[$zk][$k];
                    }
                    $zselect[$zgr]=$zselecti;
                    $zselecti=[];
                }
            }
        }
		
	}

		$f->name=$name;
        $f->connection=$this->connection;
		$f->value=$value;
		$f->atr=$atr;
		$f->zatr=$zatr;
		$f->sp=$sp;
		$f->sp_id=$sp_id;
		$f->sp_group_array=$sp_group_array;
		$f->const=$const;
		$f->default_value=$default_value;
		$f->zselect=$zselect;
		$f->line_row_type=$line_row_type;
		$f->default_text=$default_text;
		$f->any_values=$any_values;
		$f->get_properties_array_item($properties_);
		return $f->Render();
}


public function del_form_item($del_record,$item_id,$tab_name,$col_name,$const,$properties=NULL)
{//удаление поля (выполняется код записанный в описателе phpDel)
/*
		$tab_name - имя таблицы базы данных в которое записывается данное
		$col_name - имя колонки таблицы
		$const - массив констант для поля
		properties - параметры для данного поля
		$del_record - идентификатор строки, в коиторой делаются измменения
		$item_id - идентификатор поля
*/
$id=$del_record;
		$f="\\Admin\\Lib\\Fhelper\\F".$item_id;
		$f=new $f($item_id);
    $f->connection=$this->connection;
		$f->SetView($this->view);
	
		$f->setConfig($this->config);
		$f->col_name=$col_name;
		$f->tab_name=$tab_name;
		$f->const=$const;
		$f->id=$id;	//ID строки
		$f->get_properties_array_item ($properties);
		return $f->del();
}



public function save_form_item($id,$item_id,$tab_name,$col_name,$const,$infa,$properties)
{//запись поля (выполняется код записанный в описателе phpIn)
/*
		$tab_name - имя таблицы базы данных в которое записывается данное
		$col_name - имя колонки таблицы (по сути это имя поля, истинное имя поле сладывается из этого поля и идентификатора строки)
		$row_item=$id  номер-идентификатор строки по которой производится операция
		$const - массив констант для поля (ЗНАЧЕНИЯ КОНСТАНТ)
		$infa - занчение поля, освобожденное от экранирующих символов, если нужно
		properties - параметры для данного поля МАССИВ!!!
		$id - идентификатор строки, в коиторой делаются измменения
		$item_id - идентификатор поля (код типа по файлу html_item.xml)
*/
if (is_array($infa)) 
		{
			foreach ($infa as &$i)	{
                if (!is_array($i)){
                    $i=htmlspecialchars_decode($i,ENT_COMPAT);
                }
            }
		}
	else
	{$infa=htmlspecialchars_decode($infa,ENT_COMPAT);}

$row_item=$id;
		$f="\\Admin\\Lib\\Fhelper\\F".$item_id;
		$f=new $f($item_id);
    $f->connection=$this->connection;
		$f->SetView($this->view);
		$f->setConfig($this->config);
	
		$f->col_name=$col_name;
		$f->tab_name=$tab_name;
		$f->const=$const;
		$f->infa=$infa;
		$f->id=$id;	//ID строки
		$f->get_properties_array_item ($properties);
		return $f->save();
}




public  function get_js_special($flag='')
{//flag - true - выдаются только данные к скриптам
$out="";
if (!defined("__get_js_special__")){
    define ('__get_js_special__',1);
    $out="\n<script language=\"JavaScript\" type=\"text/JavaScript\">";
    $out.="
    var full_data_now=nl_create_now_date('".$this->date_time_locale_format['date_time_format']."')
    var data_now=nl_create_now_date('".$this->date_time_locale_format['date_format']."');
    var time_now=nl_create_now_date('".$this->date_time_locale_format['time_format']."')
    ";
    $out.="</script>";
}
return $out;
}


}