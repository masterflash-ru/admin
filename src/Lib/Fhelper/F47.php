<?php

/*
вывод алфавита для фильтрации вывода на первую букву
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F47 extends Fhelperabstract 
{
	protected $hname="генерирует строку-алфавит для выбора параметров по начальной букве алфавита";
	protected $category=100;
	protected $properties_keys=["number_str",
									"latin_str",
									"national_str",
									"national_encode",
									"national_str_code_start",
									"national_str_code_end",
									"array_links"];
	protected $properties_text=["number_str"=>"Отключить цифры",
									"latin_str"=>"Отключить латинские символы",
									"national_str"=>"Отключить национальные символы",
									"national_encode"=>"Имя кодировки (по умолчанию windows-1251)",
									"national_str_code_start"=>"Код начала национального алфавита",
									"national_str_code_end"=>"Код конца национального алфавита",
									"array_links"=>"Строка символов которые будут ссылками, пусто - все ссылки"
									];

	protected $properties_item_type=["number_str"=>1,
									"latin_str"=>1,
									"national_str"=>1,
									"national_encode"=>0,
									"national_str_code_start"=>0,
									"national_str_code_end"=>0,
									"array_links"=>0
									];
	
	protected $itemcount=1;
		protected $properties_listid=[
								'number_str'=>[0,1],
					            'latin_str'=>[0,1],
								'national_str'=>[0,1],
								];
	protected $properties_listtext=[
									'number_str'=>["нет","да"],
									'latin_str'=>["нет","да"],
									'national_str'=>["нет","да"]
									];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	//на входе код символа соответсв. списку ниже и  выделяет соответсвующую ссылку жирным и увеличенным шрифтом
	//генерируем цифры
	$str_='';
	$atr_='style="font-weight:bold; font-size:130%"';//стиль выделения символа
	$national_str_code_start=192;
	$national_str_code_end=223;
	if (!$this->properties['latin_str'])
		{//установим диапозон кодов символов национального
		if (isset($this->properties['national_str_code_start']) && $this->properties['national_str_code_start']>0) $national_str_code_start=$this->properties['national_str_code_start'];
		if (isset($this->properties['national_str_code_end']) && $this->properties['national_str_code_end']>0) $national_str_code_end=$this->properties['national_str_code_end'];
	if (!$this->properties['number_str'])
		for ($i=48;$i<58;$i++) 
			{if (stristr($this->properties['array_links'],chr($i))>'' || $this->properties['array_links']=='') 
					{if ($this->value==$i) $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')" '.$atr_.'>'.chr($i).'</a> '; 
						else $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')">'.chr($i).'</a> ';
					}
				else {
				if ($this->value==$i)
					$str_.='<span '.$atr_.'>'.chr($i).' </span> '; 
						else $str_.=chr($i).' ';
				}
			}
		//латинские буквы
		if (!$this->properties['latin_str'])
		 for ($i=65;$i<91;$i++) 
			{if (stristr($this->properties['array_links'],chr($i))>'' || $this->properties['array_links']=='')  
				{if ($this->value==$i) $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')" '.$atr_.'>'.chr($i).'</a> '; 
					else $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')">'.chr($i).'</a> ';
				}
				else
				{
				if ($this->value==$i)
					$str_.='<span '.$atr_.'>'.chr($i).'</span> '; 
						else $str_.=chr($i).' ';
				}
			}
		}
	
	//русские буквы
	if (!$this->properties['national_str'])
		{if (!isset($this->properties['national_encode']) || $this->properties['national_encode']=='') $this->properties['national_encode']='Windows-1251';
		for ($i=$national_str_code_start;$i<=$national_str_code_end;$i++)  
			{$chr=iconv($this->properties['national_encode'],$this->xmsg__->xmlEncoding, chr($i));
			if (stristr($this->properties['array_links'],$chr)>'' || $this->properties['array_links']=='')  
				{if ($this->value==$i) $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')" '.$atr_.'>'.$this->_encoding ($chr,$this->xml_parser_encoding,$this->xmsg__->xmlEncoding).'</a> '; 
					else $str_.='<a href=# onclick="pole_id47(\''.$i.'\',\''.$this->name[0].'\')" >'.$this->_encoding ($chr,$this->xml_parser_encoding,$this->xmsg__->xmlEncoding).'</a> ';
				}
				else
					{
					if ($this->value==$i) $str_.='<span '.$atr_.'>'.$this->_encoding ($chr,$this->xml_parser_encoding,$this->xmsg__->xmlEncoding).'</span>  '; 
						else $str_.=$this->_encoding ($chr,$this->xml_parser_encoding,$this->xmsg__->xmlEncoding).' ';
					}
			}
		
		}



	return $this->view->formHidden($this->name[0],$this->value)."<a href=# onclick=\"pole_id47(0,'{$this->name[0]}')\"  title=\"отменить фильтр\">_</a>".$str_;
}



}
