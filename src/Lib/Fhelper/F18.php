<?php

/*
радиокнопка в разрезе колонки, группа радиокнопка
группа в ограниченном пространстве
*/
namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F18 extends Fhelperabstract 
{
	protected $hname="радиокнопки (радиогруппа)";
	protected $properties_keys=["item_list","sql_delete"];
	protected $properties_text=["item_list"=>"SQL выборка, например, select id,name from table",
								"sql_delete"=>"Дополнительный параметр, для очистки флажков в SQL (например, where language=@language), тогда будут сбрасываться флажки по указанному условию)"
								];
	protected $properties_item_type=["item_list"=>2,
								"sql_delete"=>0
								];

	protected $itemcount=1;
	protected $properties_listid=[
					            'item_list' => ""
								];
	protected $properties_listtext=[
							'item_list' =>""
							];

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	if ($this->properties['item_list'])
		{
			//в разрезе элемента, просто группа
			$arr=Fhelperabstract::load_text_for_htmlitem($this->properties['item_list'],true);//считаем параметры и заодно кешируем их
			foreach ($arr["id"] as $k=>$id)
				{
					if (!isset($value[$k])) {$value[$k]=0;}
					$r[$id]=$arr["name"][$k];
				}
			$item_html=$this->view->formRadio($this->name[0],$this->value,NULL,$r);
			return $item_html.$this->view->formHidden("flag_".$this->name[0],1);
		}
		else
			{//в разрезе колонки, одиночный
				preg_match ("/([^\[]+)+\[?\[([0-9]+)\]/",$this->name[0],$ar_name);
				return $this->view->formRadio($ar_name[1],($this->value)?$ar_name[2]:"",NULL,[$ar_name[2]=>""]).
					$this->view->formHidden("flag_".$this->name[0],0);
			}
}

/*обработчик записи, возвращает обработанное*/
public function save()
{
	
	if ((int)$_POST['flag_'.$this->col_name][$this->id]==0)
		{
			$this->infa=0;
			if ($_POST[$this->col_name]==$this->id || $_POST[$this->col_name]=='')
				 {
					 if (!isset($this->properties['sql_delete'])) {$this->properties['sql_delete']='';}
					\simba_kernel::$ado_connect->Execute("update ".$this->tab_name." set ".$this->col_name."=0 ".$this->properties['sql_delete'],$RecordsAffected,\ADO::adExecuteNoRecords);
					//если выбранный флаг совпадает, тогда это 1 иначе нет, просто очистить
					 $this->infa=1;
				 }
		}
	return $this->infa;
}


}
