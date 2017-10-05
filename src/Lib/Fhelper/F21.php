<?php
/*
однострочное поле-пароль
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Zend\Crypt\Password\Bcrypt;

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
	$input = new Element\Password("n_".$this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	$input2 = new Element\Hidden($this->name[0]);
	$input2->setValue($this->value);

	return $this->view->FormElement($input).
			$this->view->FormElement($input2);
}


/*обработчик записи, возвращает обработанное*/
public function save()
{
	if (!empty($_POST['n_'.$this->col_name][$this->id]))
		{
		    $bcrypt = new Bcrypt();
            $this->infa = $bcrypt->create($_POST['n_'.$this->col_name][$this->id]);        
		}
	return $this->infa;
}


}
