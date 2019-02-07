<?php
/*
строка + календарь
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F34 extends Fdateabstract
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
}
	
	
	
public function render()
{
    $this->_format();
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
	$input = new Element\Text($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);
}





}
