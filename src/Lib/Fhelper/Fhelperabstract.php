<?php

namespace Admin\Lib\Fhelper;
use Admin\Lib\Simba;


abstract class Fhelperabstract
{
	protected static $cache=[];
	
	public $view;
	public $name;
	public $item_id;
	public $value;
	public $atr;
	public $zatr;
	public $const;
	public $sp;
	public $sp_id;
	public $sp_group_array;
	public $properties;
	public $default_value;
	public $default_text;
	public $line_row_type;
	public $any_values;
	public $zselect;
	
	//для записи
	public $tab_name;
	public $col_name;
	public $infa=NULL;
	public $id;
	
	protected $properties_keys=[];
	public  $pic_size=100;		//масштаб изображения
	
	
	protected $itemcount=0;
	protected $constcount=0;
	protected $const_count_msg="";
	protected $properties_listid;
	protected $properties_listtext;
	protected $properties_text;
	protected $properties_item_type;
	protected $itemtype=0;
	protected $hname="";
	protected $category=1;
	
	
	
public function __construct($item_id)
{
	//$layout = \Layout::getMvcInstance();

	$this->item_id=$item_id;
}

/*генерирует вид*/
public function Render()
{
	return "";
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	return $this->infa;
}

/*обработчик удаления, заглушка*/
public function del()
{
}

public function SetView($view)
{
	$this->view=$view;
}


/*преобразует массив с числовыми индексами в ассоциативный, имена ключей=именам св-в поля из описателя*/
public function get_properties_array_item($properties)
{
	if (!is_array($properties)) {return;}
	foreach ($properties as $k=>$p)
		{
			if (isset($this->properties_keys[$k]))
				{
					$this->properties[$this->properties_keys[$k]]=$p;
				}
		}
	
	return $this->properties;
}


//кеширование результатов выборки
protected static function cache_save($itemid,$sql,$rezult)
{//запись в кеш
	if ($itemid<1) return false;
	self::$cache[$itemid]['result']=$rezult;
	self::$cache[$itemid]['sql']=$sql;
}

protected static function cache_test($itemid,$sql)
{//проверка наличия в кеше
	if ($itemid<1) return false;
	if (array_key_exists($itemid,self::$cache) && self::$cache[$itemid]['sql']==$sql) 
		{
			 return self::$cache[$itemid]['result'];
		}
	return false;//промах кеша, нет данных таких
}

protected static function load_text_for_htmlitem($sql,$flag_return_array=false,$flag_sql_query=true)
{//чтение надписей для элементов форм, например надписи на кнопках
//$sql - SQL запрос
//$flag_return_array - ложь-тогдла вывод только дной записи, иначе массив данных
//$flag_sql_query истинна-делаем запрос в базу данных, иначе взходная строка и есть данные которые нужно вернуть
	if (!$sql) return false;
	if (!$flag_sql_query) return $sql;
	$itemid=md5($sql);//уникальный ключ для SQL Запроса
	$cache=self::cache_test($itemid,$sql);//проверим, есть ли такой элемент
	if ($cache) return $cache;//да есть, возвращаем результат
	//выполняем запрос в базу и кешируем его
	if ($flag_return_array) $rezult=simba::queryAllRecords($sql); else $rezult=simba::queryOneRecord($sql);
	self::cache_save($itemid,$sql,$rezult);//записываем в кеш
	return $rezult;//данные в видк одной записи!!!
}



/* НАБОР для получения полного описания всех полей*/
public function Getitemcount()
{
	return $this->itemcount;
}

public function Getconstcount()
{
	return $this->constcount;
}

public function Getconst_count_msg()
{
	return $this->const_count_msg;
}

public function Getproperties_listid()
{
	return $this->properties_listid;
}

public function Getproperties_listtext()
{
	return $this->properties_listtext;
}

public function Getproperties_text()
{
	return $this->properties_text;
}

public function Getproperties_item_type()
{
	return $this->properties_item_type;
}

public function Getitemtype()
{
	return $this->itemtype;
}

public function Gethname()
{
	return $this->hname;
}
public function Getcategory()
{
	return (string)$this->category;
}


}
