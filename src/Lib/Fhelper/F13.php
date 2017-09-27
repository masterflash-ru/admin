<?php
/*
однострочное поле-пароль с подтверждением
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;

class F13 extends Fhelperabstract 
{
	protected $itemcount=2;
	protected $hname="поле ввода пароля с подтверждением";
	
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
	$atr=$this->zatr;
	return "Новый пароль<br/>".$this->view->formPassword("n_".$this->name[0],$this->value,$atr)."<br/>Повторите пароль<br/>".
			$this->view->formPassword("r_".$this->name[0],$this->value,$atr).
			$this->view->formHidden($this->name[0],$this->value);
}


/*обработчик записи, возвращает обработанное*/
public function save()
{
	if (!empty($_POST['n_'.$this->col_name][$this->id]))
		{
			if (!empty($_POST['r_'.$this->col_name][$this->id]))
				{
					if($_POST['n_'.$this->col_name][$this->id]==$_POST['r_'.$this->col_name][$this->id])
						{
							$this->infa=$_POST['n_'.$this->col_name][$this->id];
							$s=substr(uniqid (),7,6);	//соль
							$this->infa=hash(HASH_PASSWORD_ALGO,$s.$this->infa.$s);//генерируем хеш
							$this->infa=HASH_PASSWORD_ALGO.'$'.$s.'$'.$this->infa;
						}
						else {echo "<h2>Пароли не совпадают</h2>";}
				}
			else {echo "<h2>Пароли не совпадают</h2>";}
		}
	return $this->infa;
}


}
