<?php
/*
однострочное поле + кнопка дата-время
*/

namespace Admin\Lib\Fhelper;

use Zend\Form\Element;

class F26 extends Fdateabstract 
{
	protected $hname="Генерация даты последней модификации";
	protected $category=5;
	protected $properties_keys=["out_date_time_format","in_date_time_format"];
	protected $properties_text=["out_date_time_format"=>"Формат даты-времени на входе:",
								"in_date_time_format"=>"Формат даты-времени на выходе:",
								];
	
	protected $properties_item_type=["out_date_time_format"=>1,
								"in_date_time_format"=>1,
								];
	protected $Olddatetime;
	protected $itemcount=2;
	protected $properties_listid=[
					            'out_date_time_format' => [0,1,2],
								'in_date_time_format' => [0,1,2],
								];

	protected $properties_listtext=[
								'out_date_time_format' =>["default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
								'in_date_time_format' => [
													"default (YYYY-MM-DD HH-MM-SS, ISO 9075)",
													"Настройки локали",
													"Целое число (UNIXTIME)"],
								
						];

	
public function __construct($item_id)
{
    parent::__construct($item_id);
}
	
	
	
public function render()
{
    $this->value=date('Y-m-d H:i:s');//установим нулевую дату
	switch ($this->properties['out_date_time_format'])
			{
				/*case 1:{if ($this->value=='' || $this->value==0)
							{
								if ($this->properties['empty_in']==1) $this->value='0000-00-00 00:00:00';//установим нулевую дату
								if ($this->properties['empty_in']==2) $this->value=date('Y-m-d H:i:s');//установим нулевую дату
							}
						break;
						}*/
				case 2:{$this->value=$this->intdate_to_localformat($this->value,2, 2);	break;}//из целого
				case 0:{//обработка формата ISO YYYY-MM-DD
						$this->value=$this->dbformat_to_localdate($this->value,2, 2);break;}
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
	$input = new Element\Text($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	$input->setAttribute("id", $name);
	
	$button = new Element\Button($this->name[0]."_");
	$button->setValue($this->value);
	$button->setLabel("_");
	$button->setAttributes(["onClick"=>'document.getElementById("'.$name.'").value=this.value',"id"=>$name."_"]);


	return $this->view->FormElement($input).
		$this->view->FormElement($button).
		'<script>
			if (typeof(fulldataitem)!="object") {var fulldataitem=[];}
			fulldataitem[fulldataitem.length]="'.$name.'_";
		</script>';
}


public function save()
{
	switch ($this->properties['in_date_time_format']){
        case 1:{
            if (empty($this->infa)){
                $this->infa=date('Y-m-d H:i:s');//установим нулевую дату
            }
            break;
        }
		case 2:{
            $this->infa=$this->date_to_integer ($this->infa, 2);
            break;
        }//из целого
		case 0:{
            $this->infa=$this->localdate_to_dbformat($this->infa,2, 2);
        }//обработка формата ISO YYYY-MM-DD
    }
return $this->infa;
	
}



}
