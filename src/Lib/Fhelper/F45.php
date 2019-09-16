<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Admin\Lib\Simba;

class F45 extends Fhelperabstract 
{
	protected $hname="Специальное для zrkuban.ru опции ";
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
    $name=$this->name[0];
    
$out=<<<EOT
<label>Не выводить фото 1200x600 <input name="{NAME0}[]" type="checkbox" id="{NAME0}[]" value="not_1200_600" { NOT_1200_600}></label><br/>
<label>Применить слайдер внутри статьи <select name="{NAME0}[]" id="{NAME0}[]">{INNER_SLIDER}</select></label><br/>
<label>--------------------<input name="{NAME0}[]" type="checkbox" id="{NAME0}[]" value="public_slider" {  PUBLIC_SLIDER}></label>

$value=explode(",",$value);
    if (in_array("not_1200_600",$value)) {
        $item_html=str_replace('{#NOT_1200_600}','checked',$item_html);
    }
    if (in_array("public_slider",$value)) {
        $item_html=str_replace('{#PUBLIC_SLIDER}','checked',$item_html);
    }

$s=["3D-slider"=>"3D слайдер"];
$rez=[];
foreach ($s as $k=>$v){
    if (in_array($k,$value)) {
        $selected='selected';
    } else {
        $selected='';
    }
    $rez= "<option $selected value=\"$k\">$v</option>";
}
$item_html=str_replace('{#INNER_SLIDER}',implode("",$rez),$item_html);

EOT;

    
    return $out;
}



}
