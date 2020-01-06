<?php

/*
*/
namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;


class F104 extends Fhelperabstract 
{
	protected $hname="Массив строк ввода для каталога товара";
    protected $category=2;
    protected $itemtype=1;
    protected $itemcount=1;
	

public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
    
    	$input = new Element\Text($this->name[0]);
	$input->setValue($this->value);
	$input->setAttributes($this->zatr);
	return $this->view->FormElement($input);

    
    
    
     	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];


    $out='<button type="button" class="f101_add" data-id="'.$this->name[0]."[{$id}][]".'">+</button>';
    $list=explode("~",$this->value);
    foreach ($list as $kk=>$l){
        $i=md5(microtime());
        $input = new Element\Text($this->name[0]."[{$id}][]");
        $input->setAttribute("id",$i);
        $input->setValue($l); 
        $out.= "<br/>".$this->view->FormText($input).'<button type="button" class="f101_del" data-id="'.$i.'">-</button>';
    }

    return 'НЕ РАБОТАЕТ!<div id="f100-container-'.$i.'">'.$out.'</div>' ;

}

/*обработчик записи, возвращает обработанное*/
public function save11()
{
	if ($this->properties['item_list'])
		{
			return implode(",",$this->infa);
		}
	return $this->infa;
}


}
