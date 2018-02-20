<?php

namespace Admin\Lib;
use Admin\Lib\Formitem as form_item;





class tabadmin
{
	//21.4.17 - добавлен параметр view из ZEND, для генерации полей этим фреймворком

/*
Генерирует линейный интерфейс на низком уровне (вид таблицы или формы)
отдельно создается дополнительные поля сверху, первая строка, основное содержимое таблицы и последняя строка
содержит набор JS функций для массовых операций по выбранным строкам
*/
/*

Наведен порядок с ошибками типа Notice

*/

//генерация таблиц ввода информации в базу данных
public $sort_cols_flag=[];//массив номера слева направо по колонкам, если содержимое true, тогда сортировка колонки разрешена иначе нет
public $form_name="form_1";//имя формы
public $form_action;//поле action формы
public $tab_atribute='border="1" cellpadding="5" cellspacing="0"';//атрибуты таблицы по умолчанию
public $form_atribute;//дополнительные атрибуты формы
public $caption;//заголовок таблицы
public $row_value_select_group;
public $caption_dop=[];//заголовок-предварительный текст перед выводом дополнительного поля перед таблицей
public $caption_dop_style=[];//стиль
public $caption_dop_end=[];//заголовок-предварительный текст перед выводом дополнительного поля В КОНЦЕ таблицей
public $caption_dop_style_end=[];//стиль Конечного поля в конце таблицы (аналогично как дополнительное вначале

public $row_bgcolor=[];//чередующиеся цвета строк, если пусто, тогда никаких чередований

public $flag_out_form=true;//флаг вывода тега формы
public $flag_out_js=true;//флаг вывода сопроводительных JS скриптов
public $class_header ;//класс для вывода заголовков используется тег span
public $code_start='';//коды HTML до вывода таблицы (но внутри формы вывод!)
public $code_end='';//коды HTML поле вывода таблицы (но внутри формы вывод!)

public $col_name=[]; //имена колонок (заголовки)
public $row_type_item=[];//структура строки, ее вид, в виде кода

public $type_item_cols=[];//массив структуры каждого поля, если пусто, тогда берется все по умолчанию
public $item_atr=[];//дополнительные атрибуты поля для индивидуального

public $row_page_flag=0;// 0 вывод всей таблицы, иначе по страницный вывод
public $row_page=0;// хранит номер текущей страницы, которая отображается нга экране
public $row_page_text='';//просто текст который бкдет выводиться вместе с кнопками листания 
public $row_page_count=0;//общее кол-во страниц при тавьбличном выводле

public $row_atr_item=[];//дополнительные атрибуты поля
public $row_properties_item=[];//особые (не HTML) свойства поля
public $row_name_item=[];//имя поля, если это потребуется
public $row_value_item=[];//значение элемента
public $row_value_item_default=[];//значение элемента 
public $row_value_item_default_text=[];//текстовое значение из конструтора
public $row_value_item_any_values=[];//пролизвольные данные в поле (данный объект ничего с ним не делает, просто передает дальне на уровень ниже)
public $row_value_select_item=[];//список значений выпадающего меню
public $row_value_select_id_item=[];//список идентификаторов значений выпадающего меню
public $select_group=[];//список групп для сложного выпадающего списка
//индивидуальный список для каждой строчки, если пустые тогда работают выше стоящие!
public $row_value_select_item_item=[];//список значений выпадающего меню
public $row_value_select_id_item_item=[];//список идентификаторов значений выпадающего меню
public $row_value_select_group_item_item=[];

public $row_consts=[];//разные константы которые могут понадобиться, для поля 22 это путь куда сохраняются картинки из редактора
public $form_input_type=0;//тип формы ввода данных 0-таблица 1-форма с полям
public $form_input_array=[];//список имен записей для перехода по записям (только для ввода в виде формы) если пусто, тогда щаписи просто нумеруются
public $button_create_new_item_flag=1;//флаг вывода на форме кнопки "создать новую запись"
public $buttons_jmp_flag=1;//флаг вывода кнопок переходов между записями на форме 
public $create_new_zap_flag=false;//спец влаг, если равет истине, тогда не нужно нажимать кнопку "Создать запись" при создании новой записи, полезно при выводе в виде формы

public $global_action=[];//список кнопок которые выводятся 0-нет,1-да (четкое соответствие)
public $global_action_id_array;//список уникальных ключей, они заносятся в скрытое поле для того, что бы потом определить какие поля были выбраны для массовых операций

public $jmp_record=0;//принудительно перейти на запись с номером (актуалдьно для формы ввода)
public $out_record;//хранит порядковый номер или идентификатор записи (в зависимости от выбора), которая на экране (для форм)

public $cod_form;//уникальный код формы, можно использовать для зажиты от подделки данных

private $row_all_value_col_numb=0;//счетчик колонок для функции row_all_value
private $row_def_type_col_numb=0;//счетчик колонок для функции row_def_type
private $row_start_type_col_numb=0;//счетчик колонок для функции row_start_type
private $row_start_value_col_numb=0;//счетчик колонок для функции row_start_value
private $row_end_type_col_numb=0;//счетчик колонок для функции row_end_type
private $row_end_value_col_numb=0;//счетчик колонок для функции row_end_value

public $error_from_form_item=[];//ошибки из объекта form_item, если коды 0 тогда нет ошибок. Ключи массива это [$col_name(имя колонки)][$row_item(идентификатор строки)]


public $button_all_operation_names=[];//имена кнопок "Сохранить все", Удалить выбранное, очистить кеш и т.д.

public  $view;
public $form_item;
public $config;
	public $container;

public function __toString()
{return array('version'=>$this->version,'build'=>build);
}

public function version()
{return $this->version;
}

public function __construct ($view,$config)
{//конструктор
//
$this->view=$view;
//установить имена кнопок массовых операций по умолчанию
$this->button_all_operation_names[0]='delete_selected_';
$this->button_all_operation_names[1]='_save_all_';
$this->button_all_operation_names[2]='_optimize_table_';
$this->button_all_operation_names[3]='_clear_cache_';
$this->form_item=new form_item($view,$config);
$this->config=$config;

}


public function get_status($id=NULL,$col_name=NULL)
{//получить код для элементов полей, если все пусто на входе, возвращаем все что есть, массив [$col_name(имя колонки)][$id(идентификатор строки, ключ)] 
if ($id==NULL || $col_name==NULL) return $this->error_from_form_item;
return $this->error_from_form_item[$col_name][$id];
}


//для выполнения сохранения элемента
public function save_field($pole_type ,$id,$col_name,$const_=[],$properties=[],$tab_name='')
{
/*
save_form_item($id,$item_id,$tab_name,$col_name,$const,$infa,$properties)

$pole_type - номер-идентификатор поля
$id - уникальный идентификатор строки (ключ)
col_name - имя колонки таблицы
row_item=$id  номер-идентификатор строки по которой производится операция
$const - массив ИМЕН!!!! констант для поля
properties- МАССИВ, РАНЬШЕ БЫЛА СТРОКА!!!!! (структура свойств элемента (список в виде свойство1,свойсто2,.....)
$tab_name - имя редактируемой таблицы, актуально для поля 18 (радиокнопки)
возвращает $infa - занчение поля, освобожденное от экранирующих символов (при этом если загружаются файлы то они записываются согласно исходным данным)
*/

$const=[];
foreach ($const_ as $c) $const[]=simba::get_const($c);
//print_r($const);
//$row_item=$id;//номер-идентификатор строки по которой производится операция
if (isset($_POST[$col_name][$id])) $v=$_POST[$col_name][$id]; else $v=NULL;
$infa=$this->form_item->save_form_item($id,$pole_type,$tab_name,$col_name,$const,$v,$properties);

$this->error_from_form_item[$col_name][$id]=$this->form_item->get_status($id);//получить ошибки для данной строки и данной колонки


return $infa;
}




//для выполнения удаления поля (это нужно если удаляем файлы
public function delete_field($item_id,$id,$col_name,$const=[],$properties='',$tab_name='')
{
/*
$item_id - номер-идентификатор поля
col_name - имя колонки таблицы
row_item=$id  номер-идентификатор строки по которой производится операция
$const - массив (ИМЕН!!!) констант для поля
properties-структура свойств элемента (список в виде свойство1,свойсто2,.....
$tab_name - имя таблицы мускула в которой хранится данное поле
ничего не возвращает*/

$this->form_item->del_form_item($id,$item_id,$tab_name,$col_name,$const,$properties);

}



public function init()
{//инициализация объекта, - очистить все входные данные
	$this->row_value_item=[];//значение элемента
	$this->row_atr_item=[];//Допролнительные атрибуты соответствующего тэга
	$this->row_properties_item=[];//свойства соответствующего тэга
	$this->row_type_item=[];//тип поля по номерам колонок
	$this->row_name_item=[];//имя поля, если требуется
	$this->row_value_select_item=[];//список вариантов выпабающенго списка, для радио - наименование которое будет на экране (не массив!)
	$this->row_value_select_id_item=[];//список значений выпабающенго списка,для радио - значение которое получит эта кнопка(не массив!)
	$this->row_value_select_group=[];//группы для сложного списка
	$this->row_consts=[];;//различные константы, для поля 22 это путь куда записывать картинки из html редактора
	$this->row_value_item_default=[];//значение по умолчанию если есть (из конструктора обычно)
	$this->row_all_value_col_numb=0;//счетчик колонок
	$this->row_def_type_col_numb=0;
	$this->row_start_type_col_numb=0;
	$this->row_start_value_col_numb=0;
	$this->row_end_type_col_numb=0;
	$this->row_end_value_col_numb=0;
}


//дополнитеольное поле (в начале таблицы 
public function caption ($c_dop,$c_dop_style,$poz=0)
{//устанавливают параметры заголовка в доп поле (казываются тест, стиль и номер позиции начиная с 0)
$this->caption_dop[$poz]=$c_dop;
$this->caption_dop_style[$poz]=$c_dop_style;
}

//дополнитеольное поле (в КОНЦЕ таблицы 
public function caption_end ($c_dop,$c_dop_style,$poz=0)
{//устанавливают параметры заголовка в доп поле (казываются тест, стиль и номер позиции начиная с 0)
$this->caption_dop_end[$poz]=$c_dop;
$this->caption_dop_style_end[$poz]=$c_dop_style;
}



//=====================значения полей
public function row_all_value ($col_numb,$value=[],$td_atr=[])
{//заполнение значениями для всех значений таблицы

if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_all_value_col_numb;
$this->row_value_item['all'][$col_numb]=$value;
$this->row_value_item['all_td_atr'][$col_numb]=$td_atr;
$this->row_all_value_col_numb++;
}

public function row_start_value ($col_numb,$value=[],$td_atr=[])
{//заполнение значениями для первой строки!
if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_start_value_col_numb;
$this->row_value_item['start'][$col_numb]=$value;
$this->row_value_item['start_td_atr'][$col_numb]=$td_atr;
$this->row_start_value_col_numb++;
}

public function row_dop_value ($value='',$numb=0)
{//заполнение значениями для первой строки!
$this->row_value_item['dop'][$numb]=array($value);
}
public function row_dop_end_value ($value='',$numb=0)
{//заполнение значениями для первой строки!
$this->row_value_item['dop_end'][$numb]=array($value);
}


public function row_end_value ($col_numb,$value='',$td_atr=[])
{//заполнение значениями для последней строки
if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_end_value_col_numb;
$this->row_value_item['end'][$col_numb]=array($value);
$this->row_value_item['end_td_atr'][$col_numb]=$td_atr;
$this->row_end_value_col_numb++;
}
//====================================типы полей===

public function row_end_type($col_numb,$type,$name='',$atr='',$select='',$select_id='',$c='',$select_group='',$default_value='',$row_properties_item='',$default_text='',$any_values=[])
{if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_end_type_col_numb;
$this->row_type('end',$col_numb,$type,$atr,array($name),$select,$select_id,$c,$select_group,$default_value,$row_properties_item,$default_text,$any_values);
$this->row_end_type_col_numb++;
}


public function row_start_type($col_numb,$type,$name='',$atr='',$select='',$select_id='',$c='',$select_group='',$default_value='',$row_properties_item='',$default_text='',$any_values=[])
{if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_start_type_col_numb;
$this->row_type('start',$col_numb,$type,$atr,array($name),$select,$select_id,$c,$select_group,$default_value,$row_properties_item,$default_text,$any_values);
$this->row_start_type_col_numb++;
}


//=======================================ИЗМЕНИЛСЯ СПОСОБ ОБРАЩЕНИЯ К ФУНКЦИИ !!!!!!!!!!!!!!!!!!!!!!!!!!!!
//                              1	  2	      3			4			5			6		7				8			9				10
public function row_dop_type($type,$name='',$atr='',$select='',$select_id='',$c='',$select_group='',$numb=0,$default_value='',$row_properties_item='',$default_text='',$any_values=[])
{$this->row_type('dop',$numb,$type,$atr,array($name),$select,$select_id,$c,$select_group,$default_value,$row_properties_item,$default_text,$any_values);}

public function row_dop_end_type($type,$name='',$atr='',$select='',$select_id='',$c='',$select_group='',$numb=0,$default_value='',$row_properties_item='',$default_text='',$any_values=[])
{$this->row_type('dop_end',$numb,$type,$atr,array($name),$select,$select_id,$c,$select_group,$default_value,$row_properties_item,$default_text,$any_values);}

public function row_def_type($col_numb,$type,$name=[],$atr='',$select='',$select_id='',$c='',$select_group='',$default_value='',$row_properties_item='',$default_text='',$any_values=[])
{
if ($col_numb<0 || $col_numb===false) $col_numb=$this->row_def_type_col_numb;
$this->row_type('all',$col_numb,$type,$atr,$name,$select,$select_id,$c,$select_group,$default_value,$row_properties_item,$default_text,$any_values);
$this->row_def_type_col_numb++;
}
//====================================


public function set_item_atr($col_numb,$row_numb,$atr)
{//установка атрибутов индивидуально для каждой из ячеек основной таблицы
/*$col_numb - номер столбца
$row_numb - номер строки
$atr - астрибуты для ячейки
*/
$this->item_atr['all'][$col_numb][$row_numb]=$atr;
}




public function change_row_type_item ($col_numb,$row_numb,$t=1,$atr='',$select='',$select_id='',$c='',$select_group='')
{//меняет параметры ОДНОГО ЭЛЕМЕНТА колонки (т.е.ячейку)
//необязательный параметр, определяется тип каждого элемента в колонке, если пусто, то действует умолчание
//работает только в основной таблице, на начало и конц не действует!
//$row_numb - нумерация с 0, т.е. первая строка это 0
//$this->type_item_cols['all'][$col_numb][$row_numb]=$t;//Допролнительные атрибуты соответствующего тэга
//$this->item_atr['all'][$col_numb][$row_numb]=$atr;//дополнительные атрибуты поля для индивидуального
$this->row_value_select_item_item['all'][$col_numb][$row_numb]=$select;//список вариантов выпабающенго списка
$this->row_value_select_id_item_item['all'][$col_numb][$row_numb]=$select_id;//список вариантов выпабающенго списка
$this->row_value_select_group_item_item['all'][$col_numb][$row_numb]=$select_group;//группы для сложного списка
$this->row_consts['all'][$col_numb][$row_numb]=$c;//различные константы, для поля 22 это путь куда записывать картинки из html редактора
}




public function type_item ($col_numb,$t=[],$atr=[],$select=[],$select_id=[],$c='',$select_group='')
{//меняет параметры сразу всей колонки, нужно передать массив !!! каждый элмент массива это строка
//необязательный параметр, определяется тип каждого элемента в колонке, если пусто, то действует умолчание
//работает только в основной таблице, на начало и конц не действует!
$this->type_item_cols['all'][$col_numb]=$t;//Допролнительные атрибуты соответствующего тэга
$this->item_atr['all'][$col_numb]=$atr;//дополнительные атрибуты поля для индивидуального
$this->row_value_select_item_item['all'][$col_numb]=$select;//список вариантов выпабающенго списка
$this->row_value_select_id_item_item['all'][$col_numb]=$select_id;//список вариантов выпабающенго списка
$this->row_value_select_group['all'][$col_numb]=$select_group;//группы для сложного списка
$this->row_consts['all'][$col_numb]=$c;//различные константы, для поля 22 это путь куда записывать картинки из html редактора

}


public function row_type($poz,$col_numb,$type,$atr='',$name=[],$select='',$select_id='',$cc='',$select_group='',$default_value='',$properties_item='',$default_text='',$any_values=[])
//параметры:**положение,колонка номер,код типа, атрибуты тэга,имя поля (массив),список вариантов меню, выбранный элемент меню, константы, список групп для выпад. списка
{
$this->row_atr_item[$poz][$col_numb]=$atr;//Допролнительные атрибуты соответствующего тэга
$this->row_properties_item[$poz][$col_numb]=$properties_item;//свойства соответствующего тэга
$this->row_type_item[$poz][$col_numb]=$type;//тип поля по номерам колонок
$this->row_name_item[$poz][$col_numb]=$name;//имя поля, если требуется
$this->row_value_select_item[$poz][$col_numb]=$select;//список вариантов выпабающенго списка, для радио - наименование которое будет на экране (не массив!)
$this->row_value_select_id_item[$poz][$col_numb]=$select_id;//список значений выпабающенго списка,для радио - значение которое получит эта кнопка(не массив!)
$this->row_value_select_group[$poz][$col_numb]=$select_group;//группы для сложного списка
$this->row_consts[$poz][$col_numb]=$cc;//различные константы, для поля 22 это путь куда записывать картинки из html редактора
$this->row_value_item_default[$poz][$col_numb]=$default_value;//значение по умолчанию если есть (из конструктора обычно)
$this->row_value_item_default_text[$poz][$col_numb]=$default_text;//значение  текста по умолчанию если есть (из конструктора обычно)
$this->row_value_item_any_values[$poz][$col_numb]=$any_values;//произвольные данные в поле
}

public function tab_print()
{//print_r($this->row_consts);
print ($this->tab_fetch ());}

public function tab_fetch ()
{//вывод содержимого
$out='';

if ($this->flag_out_form) 
	{$out="<form action=\"".$this->form_action."\"  ".$this->form_atribute." method=\"post\" enctype=\"multipart/form-data\" name=\"".$this->form_name."\">
<input name=\"fictive_name0\" type=\"hidden\" value=\"0\">";//приклеить фиктивное поля, для глюка пхп 5.1.1 (при закачке теряется первое поле всегда)
	//встаим код формы, вроде подписи
	if ($this->cod_form>'') $out.= '<input name="cod_form" type="hidden" value="'.$this->cod_form.'" />';
	}

$out.= '<table '.$this->tab_atribute.'>';
$out.="<caption><center><strong>".$this->caption."</strong></center></caption>";

switch ($this->form_input_type)
{
	case 0:{
	//========табличный ввод
	if (count($this->global_action)>0) 
			{$colspan=count($this->col_name)+1;
			array_unshift($this->col_name,'&nbsp;<input name="global_action_id_array" type="hidden" value="'.$this->global_action_id_array.'">');
			} 
			else $colspan=count($this->col_name);//поправка к колонкам, в зависимсоти от массовых операций
	if ($this->code_start) $out.='<tr><td colspan="'.$colspan.'">'.$this->code_start.'</td></tr>';//дополнительно вывести код html если он указан
	if (isset($this->row_type_item['dop'])) $out.=$this->create_html_tab('dop');//вначале доп поле
	//заголовки колонок

	//создадим ссылку для возмождности сортировки выбранного поля
	$_sort=0;
	if (!isset($_POST['sort_type'])) $_POST['sort_type']=NULL;
	if (!isset($_POST['sort_item'])) $_POST['sort_item']=NULL;
	$out.='<input name="sort_type" id="sort_type" type="hidden" value="'.$_POST['sort_type'].'"><input name="sort_item" id="sort_item" type="hidden" value="'.$_POST['sort_item'].'">';
	for ($iu=0;$iu<count($this->col_name);$iu++)
		{//установим значение для возмодно смены типа сортировки 0-нет,1по возрастанию,-1 по убыванию
		//обязательно учитываем спец поле для массовых операций, причем сохраним нумерацию колонок!!!!!!
		if (count($this->global_action)>0) $_iu=$iu-1; else $_iu=$iu;
		if (isset($this->sort_cols_flag[$_iu]) && $this->sort_cols_flag[$_iu])
			{//проверим, разрешена ли сортировка в этой колонке
			$img_ico='';
			if ($_POST['sort_item']==$_iu)
				{if ($_POST['sort_type']==0) $_sort=1;
					else {
						if ($_POST['sort_type']>0) $img_ico='<a href=# onClick=document.getElementById("sort_type").value=0;document.'.$this->form_name.'.submit()><img title="Отменить сортировку" src="/img/s_asc.png" border=0></a>';
								else $img_ico='<a href=# onClick=document.getElementById("sort_type").value=0;document.'.$this->form_name.'.submit()><img title="Отменить сортировку" src="/img/s_desc.png" border=0></a>';
						$_sort=-$_POST['sort_type'];
						}
				}
					else $_sort=1;//по умолчанию первый щелчек сортировать по возрастанию
			
			$out.='<th><a title="Сортировать" href=# onClick=document.getElementById("sort_item").value='.$_iu.';document.getElementById("sort_type").value='.$_sort.';document.'.$this->form_name.'.submit() '.$this->class_header.'>'.$this->col_name[$iu].'</a> '.$img_ico.'</th>';
			}
		else $out.='<th><span '.$this->class_header.'>'.$this->col_name[$iu].'</span></th>';
		}
	
	if (isset($this->row_type_item['start'])) $out.=$this->create_html_tab('start');//первая строка таблицы
	if (isset($this->row_type_item['all'])) $out.=$this->create_html_tab('all');//середина

	
	if (isset($this->row_type_item['end'])) $out.=$this->create_html_tab('end');//конец
	if ($this->row_page_flag) 
				{//определиться с дополнительными элементами листания страниц
					$spout='';$jjj=0;
					for ($iii=1;$iii<=$this->row_page_count;$iii++)
							{
							if ($this->row_page==$jjj) $spout.="<option value='$jjj' selected>$iii</option>";
												else $spout.="<option value='$jjj'>$iii</option>";
							$jjj+=$this->row_page_flag;
							}

				$out.='<tr> <th colspan="'.$colspan.'">'.$this->row_page_text;
				$out.='&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="page_start" type="submit" value="В начало" >
				<input name="pagep" type="submit" value="<<">
				<input name="pagen" type="submit" value=">>">
				<input name="page_end" type="submit" value="В конец" >
				<input name="row_page555" type="hidden" value="'.$this->row_page.'">';
				$out.='&nbsp;&nbsp;&nbsp;&nbsp;Страница:<select name="row_page"  onChange=this.form.submit()>'.$spout.'</select>';
				$out.='</th></tr>';
				}
	
	//массовые глобальные операции
if (count($this->global_action)>0)
	{//проверим кнопки какие выводить, какие нет
	$out.='<tr><td bgcolor="#CCCCCC"><input name="_select_item_all_" type="checkbox" value="" OnClick="select_all(this)"></td><td colspan="'.count($this->col_name).'" bgcolor="#CCCCCC">';
	if (isset($this->global_action[0]) && $this->global_action[0]>0)$out.='<input name="'.$this->button_all_operation_names[0].'" id="delete_selected_" type="button" onClick="snd(this.name,this)"  value="Удалить выбранное" style="font-size:10px; background-color:#FF0000;color:#FFFFFF;font-weight:bolder;">&nbsp;&nbsp;&nbsp; ';
	if (isset($this->global_action[1]) && $this->global_action[1]>0)$out.='<input name="'.$this->button_all_operation_names[1].'" type="submit" value="Сохранить все" style="font-size:10px; background-color:#00ff00;font-weight:bolder;"> ';
	if (isset($this->global_action[2]) && $this->global_action[2]>0)$out.='<input name="'.$this->button_all_operation_names[2].'" type="submit" value="Оптимизировать таблицу" style="font-size:10px;color:#ffffff; background-color:#0000ff;font-weight:bolder;"> ';
	if (isset($this->global_action[3]) && $this->global_action[3]>0)$out.='<input name="'.$this->button_all_operation_names[3].'" type="submit" value="Очистить кэш" style="font-size:10px;color:#ffffff; background-color:#ff00ff;font-weight:bolder;"> ';
	$out.='</td></tr>';
	}
	
	
	if (isset($this->row_type_item['dop_end']))$out.=$this->create_html_tab('dop_end');//вначале доп поле
	//=========конец табл. ввода
	break;}

	case 1:
	{//в виде формы
	if (isset($this->row_value_item['all'][0])) $count=count($this->row_value_item['all'][0]);//кол-во записей
			else $count=0;
	//создать структуру для выпадающего списка
	$spout='';
	if (isset($_POST['jmp_zap'])) $jmp_zap=$_POST['jmp_zap'];
			 else 
			 	{//если указан номер записи для перехода, перейти туда принудительно (если конечно не нажали кнопки листания записей)
				if ($this->jmp_record>0) $jmp_zap=$this->jmp_record;else $jmp_zap=0;
				}
	if (isset($_POST['pagen']) && $jmp_zap<$count-1)$jmp_zap++;//след запись
	if (isset($_POST['pagep']) && $jmp_zap>0)$jmp_zap--;//предыдущая
	if (isset($_POST['page_start']))$jmp_zap=0;//начальная
	if (isset($_POST['page_end'])) $jmp_zap=$count-1;//конечная
	
	for ($i=0;$i<$count;$i++)
		{if (isset($this->form_input_array[$i]) && $this->form_input_array[$i])  $iii=$this->form_input_array[$i]; else $iii=$i;
		if ($jmp_zap==$i) $spout.="<option value='$i' selected>$iii</option>";
					else $spout.="<option value='$i'>$iii</option>";
		}
	//проверим границы
	if ($jmp_zap<0 || $jmp_zap>$count-1)$jmp_zap=0;
	
	$this->out_record=$jmp_zap;
	$out.='<tr ><td>';
	//проверим надобность вывода кнопки "создать новую запись"
	if ($this->button_create_new_item_flag>0) $out.='<input name="create_new_zap" type="submit" value="Создать новую запись" style="font-family:Verdana ;font-size:10px;width: 128px;">'; else $out.='&nbsp;';
	$out.='</td>';
	//проверим необходимость вывода кнопок переходов между записями
	if ($this->buttons_jmp_flag>0)
	{$out.='<td>Переход к записи:<select name="jmp_zap" onChange="this.form.submit()">'.$spout.'</select></td></tr>
	<tr ><th colspan=2>
	<input name="page_start" type="submit" value="В начало" >&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="pagep" type="submit" value="<<" >
	<input name="pagen" type="submit" value=">>" >&nbsp;&nbsp;&nbsp;&nbsp;
	<input name="page_end" type="submit" value="В конец" >
	</td></tr>';
	$out.='<tr ><th colspan="2">Текущ.запись:'.$jmp_zap.',&nbsp;&nbsp;Всего:'.$count.'</th></tr>';
	} else $out.='<td>&nbsp;</td></tr>';
	
	
	if ($this->code_start) $out.='<tr><td colspan="2">'.$this->code_start.'</td></tr>';//дополнительно вывести код html если он указан
	if (isset($this->row_type_item['dop']))$out.=$this->create_html_tab('dop');//вначале доп поле
	
	if (isset($_POST['create_new_zap']) || $this->create_new_zap_flag) {if (count($this->row_type_item['start'])) $out.=$this->create_html_form('start');}//первая строка таблицы
		else {if (isset($this->row_type_item['all'])) $out.=$this->create_html_form('all',$jmp_zap);}//середина
	if (isset($this->row_type_item['end'])) $out.=$this->create_html_form('end');//конец
	if ($this->row_page_flag) 
	break;}




}
$out.="</table>";
//$out.='<input id="f" type="hidden" name="hgjhghj">';
if ($this->code_end) $out.='<tr> <td colspan="'.count($this->col_name).'">'.$this->code_end.'</td></tr>';//дополнительно вывести код html если он указан
if ($this->flag_out_form) $out.="</form>";


 if (!$this->flag_out_js) return $out;//сопроводительные крипты не прикреплять
//включить функцию подтверждения
 $out.=$this->form_item->get_js_special();
 $out.="\n<script language=\"JavaScript\" type=\"text/JavaScript\">
 //проверим соответсвие списка в поле global_action_id_array и чекбоксов, на всякий случай, вывести ошибку, если она есть
/*
if (document.getElementById('global_action_id_array')) 
{
var c_b=0;
 for (i = 0; i < document.forms[0].elements.length; i++)
     {
      var item = document.forms[0].elements[i];
    if (item.name.search(/^_select_item\[/)>-1)  c_b++
	}
var ar=document.getElementById('global_action_id_array').value.split(',')
if (ar.length!=c_b && c_b>0) {alert ('Внутренняя ошибка!\\nСписок идентификаторов не соответствует списку чекбоксов!\\nОбратитесь к разработчику\\nОбработка групповых операций невозможна')
document.getElementById('global_action_id_array').value=''
}
}
*/
function select_check(obj)
{
var flag=false;
document.getElementById('delete_selected_').disabled=true;
 for (i = 0; i < obj.form.elements.length; i++)
     {
         var item = obj.form.elements[i];
	     if (typeof(item.name)!='undefined')
		 if (item.name.search(/^_select_item\[/)>-1)  
		 {
		     if (item.checked)  flag=true;
		 };
	 }
if (document.getElementById('delete_selected_')!=null) 	
	{if (flag) document.getElementById('delete_selected_').disabled=false;
	}
}
function select_all(obj)
{
 for (i = 0; i < obj.form.elements.length; i++)
     {
         var item = obj.form.elements[i];
		 if (typeof(item.name)!='undefined')
	     if (item.name.search(/^_select_item\[/)>-1)  
		 {
		     item.checked = obj.checked;
		 };
	 }
if (document.getElementById('delete_selected_')!=null) 
	{if (obj.checked) document.getElementById('delete_selected_').disabled=false; else document.getElementById('delete_selected_').disabled=true;
	}
}
</script>";
return $out;
}

public function create_html_form($key,$ii=0)
{//создает структуру для вида формы
//$global_action_flag - флаг вывода чекбоксов в первой колонке, для массовых операций, по умолчанию запрещено
//выволд полей

if ($key=='all') $count=count($this->row_value_item[$key][0]); else $count=1;
$out='';//print_r($this->row_type_item[$key]);
for ($i=0;$i<count($this->row_type_item[$key]);$i++)
	{$out.='<tr><th><span '.$this->class_header.'>'.$this->col_name[$i].'</span></th>';
			$ttt=$this->row_type_item[$key][$i];//тип поля по умолчанию
			$atr=$this->row_atr_item[$key][$i];//атрибуты по умолчанию
			$row_properties_item=$this->row_properties_item[$key][$i];//совйства по умолчанию
			$row_value_item_default=$this->row_value_item_default[$key][$i];//значение по умолчанию
			$row_value_item_default_text=$this->row_value_item_default_text[$key][$i];//значение по умолчанию
			$any_values=$this->row_value_item_any_values[$key][$i];//произвольные значение по умолчанию
			$sp=$this->row_value_select_item[$key][$i];//список по умолчанию
			$sp_id=$this->row_value_select_id_item[$key][$i];//список идентификаторов для списка
			$sp_group=$this->row_value_select_group[$key][$i];//список групп сложного списка
			$const=$this->row_consts[$key][$i];//дополнительные константы, если требуется
			if (isset($this->row_value_select_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_item_item['all'][$i][$ii]!='') {$sp=$this->row_value_select_item_item['all'][$i][$ii];}
			if (isset($this->row_value_select_id_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_id_item_item['all'][$i][$ii]!='') $sp_id=$this->row_value_select_id_item_item['all'][$i][$ii];
			
			if (isset($this->row_value_select_group_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_group_item_item['all'][$i][$ii]!='') $sp_group=$this->row_value_select_group_item_item['all'][$i][$ii];
			
			
			if (isset($this->item_atr['all'][$i][$ii]) && $key=='all' && $this->item_atr['all'][$i][$ii]!='') $atr=$this->item_atr['all'][$i][$ii]; //атрибуты индивидуальны, установить
			if (isset($this->type_item_cols[$key][$i][$ii]) &&  is_integer($this->type_item_cols[$key][$i][$ii]) && $key=='all') {$ttt=$this->type_item_cols['all'][$i][$ii];}//если тип указан конкретный взять его иначе по умолчанию
			$name=$this->row_name_item[$key][$i][$ii];//имя поля
			//получить индивидуальные атрибуты ячеек для выжедения или еще что
			if (isset($this->row_value_item[$key.'_td_atr'][$i][$ii])) $td_atr =$this->row_value_item[$key.'_td_atr'][$i][$ii]; else $td_atr =NULL;

			$p=$this->form_item->create_form_item ($ttt,//1
													$name,//2
													@$this->row_value_item[$key][$i][$ii],//3
													$atr,//4
													$sp,//5
													$sp_id,//6
													$sp_group,//7
													$const,//8
													$row_value_item_default,//9
													$row_properties_item,//10
													$key,//11
													$row_value_item_default_text,//12
													$any_values
													);
				if ($key=='dop') {$pp=' colspan="2"';
									$p='<span '.$this->caption_dop_style[$i].'>'.$this->caption_dop[$i].'</span>'.$p;
									} else $pp=''; //объеденить строку в одну ячейку
					if ($p)	
						{//if ($i>0 && $key=='dop') 
	  					  $out.= "<td $pp $td_atr>$p</td>\n";
  					 //else $out.= "<td $pp $td_atr>$p</td>\n";
						}
						else	$out.= "<td $pp $td_atr>&nbsp;</td>\n";
			$out.= '</tr>';
			}

return $out;
}


public function create_html_tab($key)
{//формирует строку $key-тип строки all,dop,start.end
//выволд полей
$bg_color_all=count($this->row_bgcolor);
$bg_color_=0;//счетчик чередования цвета строк таблицы
	if ($this->global_action) $colspan=count($this->col_name); else $colspan=count($this->col_name);//поправка к колонкам, в зависимсоти от массовых операций
		if ($key=='all') $count=count($this->row_value_item[$key][0]); else $count=1;
		$out='';
		for ($ii=0;$ii<$count;$ii++)
	{//цикл по строкам
		if ($key=='all' && $bg_color_all>0) $bg_="bgcolor=".$this->row_bgcolor[$bg_color_]; else $bg_="";
		$out.='<tr>';
		$c__=count($this->row_type_item[$key]);
		for ($i=0;$i<$c__;$i++)
			{//по столбцам
			$ttt=$this->row_type_item[$key][$i];//тип поля по умолчанию
			$atr=$this->row_atr_item[$key][$i];//атрибуты по умолчанию
			$row_properties_item=$this->row_properties_item[$key][$i];//свойства по умолчанию
			$row_value_item_default=$this->row_value_item_default[$key][$i];//значение по умолчанию
			$row_value_item_default_text=$this->row_value_item_default_text[$key][$i];//значение по умолчанию
			$any_values=$this->row_value_item_any_values[$key][$i];//пролизвольные значение по умолчанию
			$sp=$this->row_value_select_item[$key][$i];//список по умолчанию
			$sp_id=$this->row_value_select_id_item[$key][$i];//список идентификаторов для списка
			$sp_group=$this->row_value_select_group[$key][$i];//список групп сложного списка
			$const=$this->row_consts[$key][$i];//дополнительные константы, если требуется
			
			if (isset($this->row_value_select_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_item_item['all'][$i][$ii]!='') {$sp=$this->row_value_select_item_item['all'][$i][$ii];}
			if (isset($this->row_value_select_id_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_id_item_item['all'][$i][$ii]!='') $sp_id=$this->row_value_select_id_item_item['all'][$i][$ii];
			if (isset($this->row_value_select_group_item_item['all'][$i][$ii]) && $key=='all' && $this->row_value_select_group_item_item['all'][$i][$ii]!='') $sp_group=$this->row_value_select_group_item_item['all'][$i][$ii];

	
			if (isset($this->item_atr['all'][$i][$ii]) && $key=='all' && $this->item_atr['all'][$i][$ii]!='') $atr=$this->item_atr['all'][$i][$ii]; //атрибуты индивидуальны, установить
			if (isset($this->type_item_cols[$key][$i][$ii]) && is_integer($this->type_item_cols[$key][$i][$ii]) && $key=='all') {$ttt=$this->type_item_cols['all'][$i][$ii];}//если тип указан конкретный взять его иначе по умолчанию
			$name=$this->row_name_item[$key][$i][$ii];//имя поля
//			$value=$this->row_value_item[$key][$i][$ii];//значение поля
			//получить индивидуальные атрибуты ячеек для выжедения или еще что
			if (isset($this->row_value_item[$key.'_td_atr'][$i][$ii])) $td_atr =$this->row_value_item[$key.'_td_atr'][$i][$ii];
									else $td_atr =NULL;
//вывести чекбоксы для массовых операций

if ($this->global_action && $i==0 && ($key=='all' || $key=='start' || $key=='end'))
					 {
					 if ($key=='all') $p=$this->form_item->create_form_item (20,
					 													'_select_item['.$ii.']',
																		0,
																		' onClick="select_check(this)"',
																		'',
																		'',
																		0,
																		'',
																		'',
																		[],
																		$key);
											else $p=$this->form_item->create_form_item (0,'','&nbsp;','','','','','','',[],$key);
					 $out.= "<td  bgcolor='#CCCCCC'>$p</td>\n";
					}
			//$td_atr='style="border: 3px solid #FF0000"';//атрибуты ячеек, для выделения например при ошибке
			if (!isset($this->row_value_item[$key][$i][$ii])) $this->row_value_item[$key][$i][$ii]=NULL; //на случай, если не установили значение
			$p=$this->form_item->create_form_item ($ttt,
														$name,
														$this->row_value_item[$key][$i][$ii],
														$atr,
														$sp,$sp_id,
														$sp_group,
														$const,
														$row_value_item_default,
														$row_properties_item,
														$key,
														$row_value_item_default_text,
														$any_values
														);//echo $name.' ';
				if ($key=='dop' || $key=='dop_end') 
								{$pp=' colspan="'.$colspan.'"';
									
									if ($key=='dop_end')
											$p='<span '.$this->caption_dop_style_end[$i].'>'.$this->caption_dop_end[$i].'</span>'.$p;
										else $p='<span '.$this->caption_dop_style[$i].'>'.$this->caption_dop[$i].'</span>'.$p;
								}
									else $pp=''; //объеденить строку в одну ячейку
				if ($p)	
					{if ($i>0 && ($key=='dop' || $key=='dop_end'))
							$out.= "<tr><td $pp $td_atr>$p</td></tr>\n"; 
								else 
									{//чередуем цвета строк, если они указаны (только для основн. части таблицы
									$out.= "<td $pp $td_atr $bg_>$p</td>\n";
									
									}
					}
					else $out.= "<td $pp $td_atr>&nbsp;</td>\n";
			}
	$out.= '</tr>';
	if ($bg_color_all-1>$bg_color_) $bg_color_++; else $bg_color_=0;//чередование цвета, т.е. по порядку
	}
return $out;
}


public function create_array_names_radio($count,$prefix)
{//создание массива имен для элементов РАДИОКНОПОК!!!!!!!!!!!!!!!!!
//вход количество элементов для которых надо создать массив имен  prefiix - имя в конструкции имя[идекс]
for ($i=0;$i<$count;$i++)
$n[$i]=$prefix;
return $n;
}

public function create_array_names($arr,$prefix,$prefix1='')
{//создание массива имен для элементов
//вход массив с индексами или уникальными номерами, prefiix - имя в конструкции имя[идекс]
//flag - если false то префикс слева и в [] записывается индекс, иначе в конце приклепляется еще дополнительно [] это для мультивыбора
$i=0;
$n=[];
//if (!$arr) echo '<script language="JavaScript" type="text/JavaScript">alert("Невозможно сгенерировать массив имен\nв методе create_array_names\nМассив идентификаторов (id) пуст");</ script>';
if (!is_array($arr)) $arr=[];
if (!$prefix1) 
		foreach ($arr as $key=>$value)
				{$n[$i]=$prefix."[".$value."]";$i++;}
		else 
		foreach ($arr as $key=>$value)
			{$n[$i]=$prefix."[".$value."],".$prefix1."[".$value."]";$i++;}
return $n;
}

public function create_array_names_multi($arr,$prefix)
{//создание массива имен для элементов
//вход массив с индексами или уникальными номерами, prefiix - имя в конструкции имя[идекс]
//flag - если false то префикс слева и в [] записывается индекс, иначе в конце приклепляется еще дополнительно [] это для мультивыбора
$i=0;
if (!is_array($arr)) $arr=[];
		foreach ($arr as $key=>$value)
				{$n[$i]=$prefix."[".$value."][]"; $i++;}
return $n;

}
private function _encoding ($str,$tocode,$fromcode)
    {//перекодировщик
		if ($tocode==$fromcode)  return  $str;
		//return mb_convert_encoding($str, $tocode, $fromcode);
		return iconv($fromcode, $tocode, $str);
    }

}
?>