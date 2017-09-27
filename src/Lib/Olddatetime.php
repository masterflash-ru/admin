<?php
/*
Внимание! работает по новой схеме! 


*/


namespace Admin\Lib;
class Olddatetime
{
/*
version=00
build=09.12.2009
Более не поддерживается

*/

//тонкости в формате даты
	public $date_format;//тоже только для даты- результат nl_langinfo(D_FMT)
	public $date_format_array;//последовательность символов в формате даты, для 1251 : d,m,y. в общем комбинации
	public $date_separator;//разделитель между элиментами в дате
//тонкости в формате времени
	public $time_format;//тоже для времени- результат nl_langinfo(T_FMT)
	public $time_format_array;////последовательность символов в формате времени, для 1251 : H(i),M,S. в общем комбинации
	public $time_separator;//разделитель между элиментами во времени
	public $time_flag_am_pm=array();//текст "до или после " полдня, результат nl_langinfo(AM_STR/PM_STR) массив
//тонкости в формате даты-времени
	public $date_time_format;//формат даты-времени - результат nl_langinfo(D_T_FMT)
//формат даты времени в базе, ВСЕ ТРАНСФОРМИРУЕТСЯ В ФОРМАТ PHP!!!
	public $date_db_format;//формат даты в базе (%Y-%m-%d )
	public $time_db_format;//время
	public $date_time_db_format;//дата-время



public function  __construct ()
	{//параметры формата даты и времени во всех вариантах
	
	//формат даты и врменеи в базе данных, трансформировать дату и время в формат PHP
	list ($date_time_locale_format['date_db_format'],$date_time_locale_format['time_db_format'],$date_time_locale_format['date_time_db_format'])=array('%Y-%m-%d','%H:%i:%s','%Y-%m-%d %H:%i:%s');
	
	//настройки даты и времени по выбранной локали
	$date_time_locale_format['date_format']=nl_langinfo(D_FMT);
	
	
	
	preg_match('/\.|\-|:|,|\//',strftime($date_time_locale_format['date_format']),$a); $date_time_locale_format['date_separator']=$a[0];//разделитель в дате
	
	preg_match_all('/%(.)/',$date_time_locale_format['date_format'],$a);
	$date_time_locale_format['date_format_array']=$a[1];//формат даты по частям в виде массива
	$date_time_locale_format['time_format']=nl_langinfo(T_FMT);//формат времени
	//рахзделитель в строке времени
	preg_match('/\.|\-|:|,/',strftime(nl_langinfo(T_FMT)),$a);
	$date_time_locale_format['time_separator']=$a[0];
	preg_match_all('/%(.)/',$date_time_locale_format['time_format'],$a);
	$date_time_locale_format['time_format_array']=$a[1];//формат даты по частям в виде массива
	$a=nl_langinfo(AM_STR);
	if ($a>'') {$date_time_locale_format['time_flag_am_pm'][0]=$a;}
	$a=nl_langinfo(PM_STR);
	if ($a>'') $date_time_locale_format['time_flag_am_pm'][1]=$a;
	$date_time_locale_format['date_time_format']=nl_langinfo(D_FMT).' '.nl_langinfo(T_FMT);//формат полной даты-времени

	
	$this->date_format=$date_time_locale_format['date_format'];
	$this->date_format_array=$date_time_locale_format['date_format_array'];
	$this->date_separator=$date_time_locale_format['date_separator'];
	$this->time_format=$date_time_locale_format['time_format'];
	$this->time_format_array=$date_time_locale_format['time_format_array'];
	$this->time_separator=$date_time_locale_format['time_separator'];
	if (isset($date_time_locale_format['time_flag_am_pm'])) $this->time_flag_am_pm=$date_time_locale_format['time_flag_am_pm'];
	$this->date_time_format=$date_time_locale_format['date_time_format'];
	$this->date_db_format=$date_time_locale_format['date_db_format'];
	$this->time_db_format=$date_time_locale_format['time_db_format'];
	$this->date_time_db_format=$date_time_locale_format['date_time_db_format'];
	}





//------------------------Функции обработки даты и времни в соответсвии с текущей локалью и форматом в базе данных

public function intdate_to_dbformat($int,$format_type=0,$behavior_empty=0)
	{//преобразовать целую дату в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	if ($int==0 || $int=='')
		{//что делать если дата - пустая строка или 0
		if ($behavior_empty==0) return;//оставить как есть
		if ($behavior_empty==1) $int=0;//атсновим нулевую дату
		if ($behavior_empty==2) $int=date('U');//атсновим текущую дату
		} 
	switch ($format_type)
		{
			case 0:return strftime($this->format_date_db_to_php($this->date_db_format),$int);
			case 1:return strftime($this->format_date_db_to_php($this->time_db_format),$int);
			case 2:return strftime($this->format_date_db_to_php($this->date_time_db_format),$int);
		}
	}

public function simba_strftime($maska)
	{//аналог функии strftime, но маска преобразована к формату БД!!!
	return strftime($this->format_date_db_to_php($maska));
	}


public function intdate_to_localformat($int,$format_type=0,$behavior_empty=0)
	{//преобразовать целую дату в формат БД, тип формата: 0-дата, 1-время, 2-дата-время

	if ($int==0 || $int=='')
		{//что делать если дата - пустая строка или 0
		if ($behavior_empty==0) return;//оставить как есть
		if ($behavior_empty==1) $int=0;//атсновим нулевую дату
		if ($behavior_empty==2) $int=date('U');//атсновим текущую дату
		} 
	switch ($format_type)
		{
		case 0:return strftime($this->date_format,$int);
		case 1:return strftime($this->time_format,$int);
		case 2:return strftime($this->date_time_format,$int);
		}
	}

public function localdate_to_dbformat($inp,$format_type=0,$behavior_empty=0)
	{//преобразовать дату в текущей локали в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	
	return $this->intdate_to_dbformat($this->date_to_integer ($inp),$format_type,$behavior_empty);
	}

public function dbformat_to_localdate($inp,$format_type=0,$behavior_empty=0)
	{//преобразовать дату в текущей локали в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	return $this->intdate_to_localformat($this->date_to_integer ($inp,$behavior_empty),$format_type,$behavior_empty);
	}

//преобразовать дату, дату-время, время в формат целого числа, исходная дата и время в формате текущей локали
public function date_to_integer ($inp,$behavior_empty=0)
	{
	$inp=preg_replace('/ {2,}/','',$inp);//убрать все лишние пробелы
	if ($inp==0 || $inp=='')
		{//что делать если дата - пустая строка или 0
		if ($behavior_empty==0) return $inp;//оставить как есть
		if ($behavior_empty==1) return 0;//атсновим нулевую дату
		if ($behavior_empty==2) return date('U');//атсновим текущую дату
		} 
	//преобразовать согласно установленой локали
	//echo strftime(nl_langinfo(D_T_FMT),strtotime ($inp));
	return strtotime ($inp);
	}

private function format_date_db_to_php($str)
	{//внутренняя, для преобразования маски типа даты формата базы данных в формат принятый в PHP
	$str=str_replace('A','W',$str);
	$str=str_replace('M','B',$str);
	$str=str_replace('i','M',$str);
	$str=str_replace('T','R',$str);
	$str=str_replace('w','u',$str);
	$str=str_replace('s','S',$str);
	return $str;
	}



}
?>