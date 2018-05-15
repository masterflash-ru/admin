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
	public $container;	//контейнер приложения

public function __construct ($view,$config)
{//конструктор
	$this->config=$config;
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
	'category_list'=>array(1,2,3,4,5,6,7,100),
	'category_list_name'=>["простые поля ввода","выбор вариантов","файлы","HTML редактор","Дата время","Кнопки","Изображения","прочее"]
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


//выборка их XML описателя           1        2      3      4   5     6       7            8       9                 10                  11                   12             13
public function create_form_item ($item_id,$name_,$value,$atr_,$sp,$sp_id,$sp_group_array,$c,$default_value='',$properties_=[],$line_row_type='',$default_text='',$any_values=[])
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
			foreach ($infa as &$i)
				{
					$i=htmlspecialchars_decode($i,ENT_COMPAT);
				}
		}
	else
	{$infa=htmlspecialchars_decode($infa,ENT_COMPAT);}

$row_item=$id;
		$f="\\Admin\\Lib\\Fhelper\\F".$item_id;
		$f=new $f($item_id);
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

define ('__get_js_special__',1);
$out="\n<script language=\"JavaScript\" type=\"text/JavaScript\">";


$out.="if (typeof(dataitem)!='object') var dataitem=[];
if (typeof(timeitem)!='object') var timeitem=[];
if (typeof(fulldataitem)!='object') var fulldataitem=[];\n
function snd(obj,item_obj)
{//подтверждение удаления
	if (window.confirm('Подтвердите операцию')) 
		{
			d=document.createElement(\"input\");
			d.setAttribute('value',item_obj.value);
			d.setAttribute('name',obj);
			d.setAttribute('id',obj);
			d.setAttribute('type','hidden');
			item_obj.form.appendChild(d);
			item_obj.form.submit();
		 } 
		 	else return false;
}


function pole_id47(str,hidden_name)
{//для поля 47 (алфавит)
document.getElementById(hidden_name).value=str;//то что передается на сервер
document.getElementById(hidden_name).form.submit();//подписать форму, т.е. отправть на сервер
}

function nl_create_now_date(maska)
{//генерация текущей даты по маске как в пхп, например 
d=new Date();
var out='';
for (i=0;i<maska.length;i++)
	{s=maska.substr(i,1)
	if (s=='%')
		{i++;s=maska.substr(i,1)
		switch (s)
			{case 'H':{s=d.getHours();break;}
			case 'M':{s=d.getMinutes();break;}
			case 'S':{s=d.getSeconds();break;}
			case 'd':{s=d.getDate();break;}
			case 'D':{s=d.getDate();break;}
			case 'I':{s=d.getHours();if (s>12) s=s-12;break;}
			case 'm':{s=d.getMonth();s++;break;}
			case 'w':{s=d.getDay();break;}
			case 'Y':{s=d.getFullYear();break;}
			case 'y':{s=d.getFullYear();s=s.toString();s=s.substr(2,2);break;}
			case 'T':{s=d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();break;}
			}
		}
	out+=s
	}
return out
}



//*************************************************для поля 48
function htmlspecialchars_decode_for_item48(text)
{//обратное преобразование специальных символов
   var chars = Array(\"&amp;\", \"&lt;\", \"&gt;\", \"&quot;\", \"'\");
   var replacements = Array(\"&\", \"<\", \">\", '\"', \"'\");
   for (var i=0; i<chars.length; i++)
   {
       var re = new RegExp(chars[i], \"gi\");
       if(re.test(text))
       {
           text = text.replace(re, replacements[i]);
       }
   }
  return text;
}

function db_record_item48(array_key,array_value,selected_flag)
{//массив данных для поля 48

	this.array_key = array_key
	this.array_value =htmlspecialchars_decode_for_item48(array_value)
	this.selected_flag=selected_flag
	return this
}

win_names_array=[];//иемна открытых окон (ссылки на объект)

var win_name;
function create_window(win_name)
{
columns=db_item48[win_name][\"columns\"]//кол-во колонок в окне
if (columns==0 || columns=='') columns=2;//по умоляанию 2
col=0;row=0;//текущее состояние
out='<html><body><form><table width=\"100%\" border=0 style=\"font-size:12px; font-family:Verdana, Arial, Helvetica, sans-serif\">';
i=0;
row_no_end=true;
while (row_no_end)
	{//цикл по строкам
	out+='<tr>';
	for (c=0;c<columns;c++)
		{//цикл по колонкам
		if (db_item48[win_name].length>i) 
				{selected='';
				if (db_item48[win_name][i].selected_flag>0) selected='checked';
				out+='<td>' + '<label><input name=\"checkbox['+i+']\" type=\"checkbox\" value=\"'+db_item48[win_name][i].array_key+'\"' + selected +' /><span id=\"text__'+i+'\">'+db_item48[win_name][i].array_value+'</span></label></td>'
				}
			else {row_no_end=false;out+='<td>&nbsp;</td>';}
		i++;
		}
	out+='</tr>'
	}

out+='</table><div align=\"center\"><input name=\"save48\" type=\"button\" value=\"'+db_item48[win_name][\"button_caption\"]+'\" onClick=\"save()\" /></div></form>';
out+='<scr'+'ipt type=\"text/javascript\">'
out+='function save()\\n'
out+='{text_=[];value=[];kk=0;'
out+='for (i=0;i<document.forms[0].elements.length-1;i++)\\n'
out+='{'
out+='if (document.forms[0].elements[i].checked) {value[kk]=document.forms[0].elements[i].value;text_[kk]=document.getElementById(\"text__\"+i).innerHTML;kk++;}'
out+='}'

out+='opener.document.getElementById(\"'+db_item48[win_name][\"io_item\"]+'\").value=value.join(\",\");\\n'
out+='opener.document.getElementById(\"'+db_item48[win_name][\"io_item\"]+'_text\").innerHTML=text_.join(\",\");\\n';
out+='window.close()}\\n'
out+='window.moveTo(300,300);'
out+='</sc'+'ript>'

out+='</body></html>';
ww=db_item48[win_name][\"window\"][0];//ширина окна
hh=db_item48[win_name][\"window\"][1];//высота экрана
if (ww==0 || ww=='') ww=400;
if (hh==0 || hh=='') hh=400;
p='width='+ww+',height='+hh+'toolbar=no,menubar=no,scrollbars=yes';
win_names_array[win_name]=window.open('','',p);
//генерирум там скрипт и все остальное
win_names_array[win_name].document.write(out)
}

//установка поля 48 в начальное состояние
//цикл по всем полям типа 48
if (typeof(db_item48)==\"object\")
for (win_name in db_item48) 
	{
		ff=db_item48[win_name][\"function\"];//заполнить данными
	ff();
	value_=[];
	text_=[];
	for (i=0;i<db_item48[win_name].length;i++)
		{
		if (db_item48[win_name][i].selected_flag>0) 
				{value_[value_.length]=db_item48[win_name][i].array_key;
				text_[text_.length]=db_item48[win_name][i].array_value;
				}
		
		}
	document.getElementById(db_item48[win_name][\"io_item\"]).value=value_.join(\",\");
	document.getElementById(db_item48[win_name][\"io_item\"]+\"_text\").innerHTML=text_.join(\",\");
	}



//***********************************************конец для поля 48
function create_window55(win_name)
{
columns=db_item48[win_name][\"columns\"]//кол-во колонок в окне
if (columns==0 || columns=='') columns=2;//по умоляанию 2
col=0;row=0;//текущее состояние
out='<table width=\"100%\" border=\"0\" class=\"win55\">';
i=0;
row_no_end=true;
while (row_no_end)
	{//цикл по строкам
	out+='<tr>';
	for (c=0;c<columns;c++)
		{//цикл по колонкам
		if (db_item48[win_name].length>i) 
				{selected='';
				if (db_item48[win_name][i].selected_flag>0) selected='checked';
				out+='<td><label>';
                out+='<input name=\"checkbox['+i+']\" type=\"checkbox\" value=\"'+db_item48[win_name][i].array_key+'\"' + selected +' />';
                out+='<span id=\"text__'+i+'\">'+db_item48[win_name][i].array_value+'</span>';
                out+='</label></td>';
				}
			else {row_no_end=false;out+='<td>&nbsp;</td>';}
		i++;
		}
	out+='</tr>'
	}

out+='</table><div align=\"center\"><input name=\"save55[]\" type=\"button\" value=\"'+db_item48[win_name][\"button_caption\"]+'\" onClick=\"save55(\''+win_name+'\')\" /></div>';
ww=db_item48[win_name][\"window\"][0];//ширина окна
hh=db_item48[win_name][\"window\"][1];//высота экрана
if (ww==0 || ww=='') ww=400;
if (hh==0 || hh=='') hh=\"auto\";
$( \"#f55_dialog\" ).html(out);
$( \"#f55_dialog\" ).dialog({
      resizable: true,
      height: hh,
      width: ww,
      modal: true,

});
}

function save55(win_name)
{
var value=[],text_=[];
$( \"#f55_dialog\" ).dialog(\"close\");
$( \"#f55_dialog input:checked\" ).each(
    function (index){value[index]=$(this).val();text_[index]=$(this).next().text();}
);
document.getElementById(db_item48[win_name][\"io_item\"]).value=value.join(\",\");
document.getElementById(db_item48[win_name][\"io_item\"]+\"_text\").innerHTML=text_.join(\",\");
}

function f56(url,w,h)
{

$( \"#f56_dialog\" ).dialog({
      resizable: true,
      height: h+65,
      width: w+30,
      modal: true,
      open: function(ev, ui){
             $('#iframe56').attr({'src':url,'width':w,'height':h});
          }

});


}


function data___clock()
{
var full_data_now=nl_create_now_date('".$this->date_time_locale_format['date_time_format']."')
var data_now=nl_create_now_date('".$this->date_time_locale_format['date_format']."');
var time_now=nl_create_now_date('".$this->date_time_locale_format['time_format']."')
//заполнение кнопок

 for (i=0;i<fulldataitem.length;i++) 
 	{
		if (document.getElementById(fulldataitem[i])) 
			{
				document.getElementById(fulldataitem[i]).value=full_data_now;
				document.getElementById(fulldataitem[i]).innerHTML=full_data_now;
			}
	}
 for (i=0;i<dataitem.length;i++) 
 	{
		if (document.getElementById(dataitem[i])) 
			{
				document.getElementById(dataitem[i]).value=data_now;
				document.getElementById(dataitem[i]).innerHTML=data_now;
			}
	}
 for (i=0;i<timeitem.length;i++) 
 	{
		if (document.getElementById(timeitem[i])) 
			{
				document.getElementById(timeitem[i]).value=time_now;
				document.getElementById(timeitem[i]).innerHTML=time_now;
			}
	}

setTimeout('data___clock()',3000)
}
data___clock();

\$( function() {\$('.dtpicker' ).datetimepicker();});
";
$out.="</script>";
return $out;
}


}
?>