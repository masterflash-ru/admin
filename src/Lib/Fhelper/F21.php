<?php
/*
однострочное поле-пароль
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F21 extends Fhelperabstract 
{
	protected $itemcount=1;
	protected $hname="поле ввода пароля без подтверждения";
	
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$atr=$this->zatr;
	return $this->view->formPassword("n_".$this->name[0],$this->value,$atr).$this->view->formHidden($this->name[0],$this->value);
}


/*обработчик записи, возвращает обработанное*/
public function save()
{
	if (!empty($_POST['n_'.$this->col_name][$this->id]))
		{
			$this->infa=$_POST['n_'.$this->col_name][$this->id];
			$s=substr(uniqid (),7,6);	//соль
			$this->infa=hash(HASH_PASSWORD_ALGO,$s.$this->infa.$s);//генерируем хеш
			$this->infa=HASH_PASSWORD_ALGO.'$'.$s.'$'.$this->infa;
		}
	return $this->infa;
}


}
