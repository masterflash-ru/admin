<?php
/*
однострочное поле-пароль с подтверждением
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;
use Laminas\Crypt\Password\Bcrypt;

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
	$input = new Element\Password("n_".$this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);

	$input1 = new Element\Password("r_".$this->name[0]);
	$input1->setValue($this->value);
	$input1->setAttributes($this->zatr);

	$input2 = new Element\Hidden($this->name[0]);
	$input2->setValue($this->value);

	return "Новый пароль<br/>".$this->view->FormElement($input)."<br/>Повторите пароль<br/>".
			$this->view->FormElement($input1).
			$this->view->FormElement($input2);
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
							$bcrypt = new Bcrypt();
							$this->infa = $bcrypt->create($_POST['n_'.$this->col_name][$this->id]);        
						}
						else {echo "<h2>Пароли не совпадают</h2>";}
				}
			else {echo "<h2>Пароли не совпадают</h2>";}
		}
	return $this->infa;
}


}
