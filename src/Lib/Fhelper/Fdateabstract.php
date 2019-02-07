<?php
/**
* работает только в локали ru_RU !!!!
*
*/
namespace Admin\Lib\Fhelper;


class Fdateabstract extends Fhelperabstract 
{
	protected $category=5;
	protected $properties_keys=["out_date_time_format","in_date_time_format","empty_in","empty_out"];
	protected $properties_text=["out_date_time_format"=>"Формат даты-времени на входе:",
								"in_date_time_format"=>"Формат даты-времени на выходе:",
								"empty_in"=>"Если на входе 0 или пусто, тогда",
								"empty_out"=>"Если на выходе 0 или пусто, тогда"
								];
	
	protected $properties_item_type=["out_date_time_format"=>1,
								"in_date_time_format"=>1,
								"empty_in"=>1,
								"empty_out"=>1
								];
	protected $itemcount=2;
	protected $properties_listid=[
					            'out_date_time_format' => [0,1,2],
								'in_date_time_format' => [0,1,2],
								'empty_in' => [0,1,2],
								'empty_out' => [0,1,2],
								];

	protected $properties_listtext=[
								'out_date_time_format' =>["default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
								'in_date_time_format' => [
													"default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
								'empty_in' => [
													"Присвоить полю NULL",
													"Установить нулевую дату",
													"Установить текущую дату"],
								
								'empty_out' => [
													"Записать NULL",
													"Установить нулевую дату",
													"Установить текущую дату"],
						];

	protected $date_format='%d.%m.%Y';
	protected $time_format='%H:%M:%S';
	protected $date_time_format='%d.%m.%Y %H:%M:%S';

	protected $date_db_format='%Y-%m-%d';
	protected $time_db_format='%H:%i:%s';
	protected $date_time_db_format='%Y-%m-%d %H:%i:%s';

public function __construct($item_id)
{
    parent::__construct($item_id);
    setlocale(LC_ALL,array('ru_RU.utf8','ru_RU.UTF-8'));
}
	
public function save()
{
    $this->infa=trim($this->infa);
    
    /*если на входе пусто, тогда реагируем по установкам*/
    if (empty($this->infa)) {
        $this->infa=null;
        if ($this->properties['empty_out']==1) {$this->infa='0000-00-00 00:00:00';}//установим нулевую дату
        if ($this->properties['empty_out']==2) {$this->infa=date('Y-m-d H:i:s');}//установим нулевую дату
        return $this->infa;
    }
    
	switch ($this->properties['in_date_time_format']){
        case 1:{//default (YYYY-MM-DD HH-MM-SS, ISO 9075)
            break;
        }
        case 2:{//превратим в целое
            $this->infa=$this->date_to_integer ($this->infa,$this->properties['empty_out']); 
            break;
        }
        case 0:{//превратим из локального типа в формат базы
            $this->infa=$this->localdate_to_dbformat($this->infa,2,$this->properties['empty_out']);
        }
    }
    
return $this->infa;
	
}

    
/*
* форматирует входное значение
*/   
protected function _format()
{
    switch ($this->properties['out_date_time_format']){
        case 1: {
            if (empty($this->value)){
                $this->value=null;
                if ($this->properties['empty_in']==1) {$this->value='0000-00-00 00:00:00';}//установим нулевую дату
                if ($this->properties['empty_in']==2) {$this->value=date('Y-m-d H:i:s');}//установим нулевую дату
                }
             break;
            }
        case 2:{//преобразуем в целое число
            $this->value=$this->intdate_to_localformat($this->value,2,$this->properties['empty_in']);	
            break;
        }
        case 0:{//обработка формата ISO YYYY-MM-DD
            $this->value=$this->dbformat_to_localdate($this->value,2,$this->properties['empty_in']);
            break;
        }
    }
}
    

//------------------------Функции обработки даты и времни в соответсвии с текущей локалью и форматом в базе данных

protected function intdate_to_dbformat($int,$format_type=0,$behavior_empty=0)
	{//преобразовать целую дату в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	if (empty($int)) {
        //что делать если дата - пустая строка или 0
		if ($behavior_empty==0) return null;//оставить как есть
		if ($behavior_empty==1) $int=0;//атсновим нулевую дату
		if ($behavior_empty==2) $int=date('U');//атсновим текущую дату
    } 
	switch ($format_type){
			case 0:return strftime($this->format_date_db_to_php($this->date_db_format),$int);
			case 1:return strftime($this->format_date_db_to_php($this->time_db_format),$int);
			case 2:return strftime($this->format_date_db_to_php($this->date_time_db_format),$int);
		}
	}


protected function intdate_to_localformat($int,$format_type=0,$behavior_empty=0)
	{//преобразовать целую дату в формат БД, тип формата: 0-дата, 1-время, 2-дата-время

	if (empty($int)){//что делать если дата - пустая строка или 0
        if ($behavior_empty==0) return null;//оставить как есть
		if ($behavior_empty==1) $int=0;//атсновим нулевую дату
		if ($behavior_empty==2) $int=date('U');//атсновим текущую дату
		} 
	switch ($format_type){
		case 0:return strftime($this->date_format,$int);
		case 1:return strftime($this->time_format,$int);
		case 2:return strftime($this->date_time_format,$int);
		}
	}

protected function localdate_to_dbformat($inp,$format_type=0,$behavior_empty=0)
	{//преобразовать дату в текущей локали в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	return $this->intdate_to_dbformat($this->date_to_integer ($inp),$format_type,$behavior_empty);
	}

protected function dbformat_to_localdate($inp,$format_type=0,$behavior_empty=0)
	{//преобразовать дату в текущей локали в формат БД, тип формата: 0-дата, 1-время, 2-дата-время
	return $this->intdate_to_localformat($this->date_to_integer ($inp,$behavior_empty),$format_type,$behavior_empty);
	}

//преобразовать дату, дату-время, время в формат целого числа, исходная дата и время в формате текущей локали
protected function date_to_integer ($inp,$behavior_empty=0)
	{
	$inp=preg_replace('/ {2,}/','',$inp);//убрать все лишние пробелы
	if (empty($inp)) {//что делать если дата - пустая строка или 0
		if ($behavior_empty==0) return $inp;//оставить как есть
		if ($behavior_empty==1) return 0;//атсновим нулевую дату
		if ($behavior_empty==2) return date('U');//атсновим текущую дату
		}
	//преобразовать согласно установленой локали
	return strtotime ($inp);
	}

protected function format_date_db_to_php($str)
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
