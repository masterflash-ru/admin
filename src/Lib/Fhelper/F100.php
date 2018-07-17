<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Admin\Lib\Simba;

class F100 extends Fhelperabstract 
{
	protected $hname="Общие параметры товара";
    protected $category=101;
	protected $itemcount=1;

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	

    
/*обработка записи

$this->id - ID записи основной таблицы (товара)
*/
public function save()
{
    
//    \Zend\Debug\Debug::dump($_POST[$this->col_name][$this->id]);
    if (!is_array($_POST[$this->col_name][$this->id])){
        return;
    }
    //удалим старое
    simba::query("delete from tovar_parameters_value where tovar_catalog=".(int)$this->id);
    foreach ($_POST[$this->col_name][$this->id] as $tovar_parameters_id=>$value){
        //заносим вновь новые
        if (is_array($value)){
            //запись массива
            foreach ($value as $v){
                if (!$v){continue;}
                Simba::ReplaceRecord([
                    "tovar_catalog"=>(int)$this->id,
                    "tovar_parameters"=>(int)$tovar_parameters_id,
                    "value"=>$v
                    ],"tovar_parameters_value");
            }
        } else {
            if (!$value){continue;}
            Simba::ReplaceRecord([
                "tovar_catalog"=>(int)$this->id,
                "tovar_parameters"=>(int)$tovar_parameters_id,
                "value"=>$value
                ],"tovar_parameters_value");
        }
    }
    
    
}
    
    
    
/*рендер элемента в админке*/	
public function render()
{//
 	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
    
    $name=$this->name[0];   //имя в виде tovar_parameters[234] - внутри ID строки таблицы tovar_catalog
    
    $parameters=Simba::queryAllRecords("select 
        p.*,
        
        (select GROUP_CONCAT(`values` separator '~') from `tovar_parameters_list` as l where l.tovar_parameters=p.id) as list
            from tovar_parameters as p
                group by p.id
        ");
	
    //\Zend\Debug\Debug::dump($parameters);
    $out="<div class=\"f100-container\">";
    if (Simba::numRows()){
        foreach ($parameters["id"] as $k=>$v){
            //читаем значения
            $vv=Simba::QueryOneRecord("select value from tovar_parameters_value 
                where tovar_catalog={$id} and tovar_parameters={$v}");
            
            switch ($parameters["field_type"][$k]){
                case 1:{
                    //тип выпадающий список
                    $out.="<br/><b>".$parameters["name"][$k].': </b>';
                    $input = new Element\Select($name."[{$v}]");
                    $list=explode("~",$parameters["list"][$k]);
                    $input->setEmptyOption("");
                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue($vv["value"]);
                    $out.= $this->view->FormElement($input).'<br/>';
                    break;
                }
                case 2:{
                    //тип радиокнопки список
                    $out.="<br/><b>".$parameters["name"][$k].': </b>';
                    $input = new Element\Radio($name."[{$v}]");
                    $list=explode("~",$parameters["list"][$k]);
                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue($vv["value"]);
                    $element=$this->view->FormRadio()->setSeparator("<br>");
                    $out.="<br/>".$element->render($input) ;
                    break;
                }
                case 3:{
                    //тип флажки список
                    $out.="<br/><b>".$parameters["name"][$k].':</b>';
                    $input = new Element\MultiCheckbox($name."[{$v}]");
                    $list=explode("~",$parameters["list"][$k]);
                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue(explode("~",$vv["value"]));
                    $element=$this->view->FormMultiCheckbox()->setSeparator("<br>");
                    $out.="<br/>".$element->render($input) ;
                    break;
                }
                default:{
                    //по умолчанию поле ввода
                    $out.="<br/><br/><b>".$parameters["name"][$k].":</b> ".'<button type="button" class="f100_add" data-id="'.$name."[{$v}][]".'">+</button>';
                    $list=explode("~",$vv["value"]);
                    foreach ($list as $kk=>$l){
                        $i=$k*20+$kk;
                        $input = new Element\Text($name."[{$v}][]");
                        $input->setAttribute("id","f100_".$i);
                        $input->setValue($l);
                        $out.= "<br/>".$this->view->FormText($input).'<button type="button" class="f100_del" data-id="f100_'.$i.'">-</button>';
                    
                    }
                }
            }
        }
        
    } 

    
    return $out.'</div>';
}



}
