<?php
/*
строка + календарь
*/

namespace Admin\Lib\Fhelper;
use Admin\Lib\Olddatetime;
use Zend\Form\Element;

class F34 extends Fhelperabstract 
{
	protected $hname="Календарь в окне";
	protected $category=5;
	protected $properties_keys=["style","out_date_time_format","in_date_time_format","empty_in","empty_out"];
	protected $properties_text=["style"=>"Устарело",
								"out_date_time_format"=>"Формат даты на входе:",
								"in_date_time_format"=>"Формат даты на выходе:",
								"empty_in"=>"Если на входе 0 или пусто, тогда",
								"empty_out"=>"Если на выходе 0 или пусто (юзер не выбрал ничего), тогда"
								];
	protected $properties_item_type=["style"=>1,
								"out_date_time_format"=>1,
								"in_date_time_format"=>1,
								"empty_in"=>1,
								"empty_out"=>1
								];

	protected $Olddatetime;
	
	protected $itemcount=2;
	protected $properties_listid=[
								'style'=>"",
					            'out_date_time_format' => [0,1,2],
								'in_date_time_format' => [0,1,2],
								'empty_in' => [0,1,2],
								'empty_out' => [0,1,2],
								];
	protected $properties_listtext=[
								'style'=>"Устарело",
								'out_date_time_format' =>["default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
								'in_date_time_format' => [
													"default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
								'empty_in' => [
													"Оставить как есть",
													"Установить нулевую дату",
													"Установить текущую дату"],
								
								'empty_out' => [
													"Оставить как есть",
													"Установить нулевую дату",
													"Установить текущую дату"],
						];

	
public function __construct($item_id)
{
		parent::__construct($item_id);
		$this->Olddatetime=new Olddatetime();
}
	
	
	
public function render()
{
	switch ($this->properties['out_date_time_format'])
			{
				case 1:{if ($this->value=='' || $this->value==0)
							{
								if ($this->properties['empty_in']==1) $this->value='0000-00-00';//установим нулевую дату
								if ($this->properties['empty_in']==2) $this->value=date('Y-m-d');//установим нулевую дату
							}
						break;
						}
				case 2:{$this->value=$this->Olddatetime->intdate_to_localformat($this->value,0,$this->properties['empty_in']);	break;}//из целого
				case 0:{//обработка формата ISO YYYY-MM-DD
						$this->value=$this->Olddatetime->dbformat_to_localdate($this->value,0,$this->properties['empty_in']);break;}
			}

	preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
	if (count($ar_name))
		{
			$name=$ar_name[1].'-'.$ar_name[2];
		}
		else
			{
				$name=$this->name[0];
			}
	$this->zatr["class"]="dtpicker";
	return $this->view->formText($this->name[0],$this->value,$this->zatr);
}


public function save()
{
	switch ($this->properties['in_date_time_format'])
		{
			case 1:{if ($this->infa=='' || $this->infa=0)
							{if ($this->properties['empty_out']==1) $this->infa='0000-00-00';//установим нулевую дату
							if ($this->properties['empty_out']==2) $this->infa=date('Y-m-d');//установим нулевую дату
							}
						break;}
			case 2:{$this->infa=$this->Olddatetime->date_to_integer ($this->infa,$this->properties['empty_out']); break;}//из целого
			case 0:{$this->infa=$this->Olddatetime->localdate_to_dbformat($this->infa,0,$this->properties['empty_out']);}//обработка формата ISO YYYY-MM-DD
		}

return $this->infa;
	
}



}
