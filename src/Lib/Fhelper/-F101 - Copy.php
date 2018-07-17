<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Admin\Lib\Simba;

class F101 extends Fhelperabstract 
{
	protected $hname="Складская информация по товару";
    protected $category=101;
	protected $itemcount=1;
    protected $tree_ids=[];
    protected $_parameters;
    protected $_name;
    protected $_parameters_combination_number=0;    //одноименный параметр из таблицы значения параметров
    protected $_max_parameters_list=0;              //максимальное кол-во элементов в списках параметров

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	

    
/*обработка записи

$this->id - ID записи основной таблицы (товара)
*/
public function save()
{return;
    
    //\Zend\Debug\Debug::dump($_POST[$this->col_name][$this->id]);
    if (!is_array($_POST[$this->col_name][$this->id])){
        return;
    }
    //удалим старое
    simba::query("delete from tovar_category_parameters_value where tovar_catalog=".(int)$this->id);
    foreach ($_POST[$this->col_name][$this->id] as $tovar_parameters_id=>$value){
        //заносим вновь новые
        if (is_array($value)){
            //запись массива
            foreach ($value as $v){
                Simba::ReplaceRecord([
                    "tovar_catalog"=>(int)$this->id,
                    "tovar_parameters"=>(int)$tovar_parameters_id,
                    "value"=>$v
                    ],"tovar_category_parameters_value");
            }
        } else {
            Simba::ReplaceRecord([
                "tovar_catalog"=>(int)$this->id,
                "tovar_parameters"=>(int)$tovar_parameters_id,
                "value"=>$value
                ],"tovar_category_parameters_value");
        }
    }
    
    
}
    
    
    
/*рендер элемента в админке*/	
public function render()
{//
    if (isset($_GET['get_interface_input'])) {
        $tovar_category=(int)unserialize(base64_decode($_GET['get_interface_input']));
    } else {
        return "Не верное обращение к элементу F101, нет категории товара";
    }

 	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
    
    $this->_name=$this->name[0];   //имя в виде tovar_parameters[234] - внутри ID строки таблицы tovar_catalog
    
    //проход дерева до 0-го уровня, получим все ID
    $this->tree_ids[]=$tovar_category;
    $this->_un_tree($tovar_category);
    
    
    $this->_parameters=Simba::queryAllRecords("select 
        p.*,
        (select GROUP_CONCAT(`values` separator '~') from `tovar_category_parameters_list` as l where l.tovar_category_parameters=p.id) as list
            from tovar_category_parameters as p
                where p.tovar_category in(". implode(",",$this->tree_ids) .")");
    
    $out="<table class=\"f101\">";
    

    //\Zend\Debug\Debug::dump($params);
    if (Simba::NumRows()){
        //ищем факториал это кол-во комбинаций
        $f = 1;
        for ($i = 1; $i <= Simba::NumRows(); $i++) {$f = $f * $i;}
        $out.="<tr>";
        foreach ($this->_parameters["id"] as $k=>$v){
            $out.="<th>".$this->_parameters["name"][$k]."</th>";
        }
        $out.="<th>Артикул</th><th>Остаток</th><th>Стандартная цена</th>"; 
        $out.="</tr>";

        for ($i = 0; $i < $f; $i++){//цикл по строкам
            $out.="<tr>";
            foreach ($this->_parameters["id"] as $k=>$v){//цикл по колонкам
                $out.="<td>";
                $out.=$this->_create_field($k,$v);
                 $out.="</td>";
            }
            //данные из другой таблицы, артикул, цена, остаток.....
            foreach (["articul","ostatok"] as $type){
                $out.="<td>";
                $out.=$this->_get_sklad();
                $out.="</td>";
            }
            foreach (["price"] as $type){
                $out.="<td>";
                $out.=$this->_get_price();
                $out.="</td>";
            }
            $out.="</tr>";
        }
        
    } 

    
    return $out.'</table>';
}

    
/*генерация полей
$k - номер желемента в $this->_parameters['id'],
$v - значение элемента $this->_parameters['id]
*/
protected function _create_field($k,$v)
{
                //читаем значения
           /* $vv=Simba::QueryOneRecord("select value from tovar_category_parameters_value 
                where tovar_catalog={$id} and tovar_category_parameters={$v}");*/
            $vv["value"]="";
    $out="";
    $list=explode("~",$this->_parameters["list"][$k]);
    $this->_max_parameters_list=max($this->_max_parameters_list,count($list));
            switch ($this->_parameters["field_type"][$k]){
                case 1:{
                    //тип выпадающий список
                    $input = new Element\Select($this->_name."[{$v}]");
                    
                    $input->setEmptyOption("");
                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue($vv["value"]);
                    $out.= $this->view->FormElement($input).'<br/>';
                    break;
                }
                case 2:{
                    //тип радиокнопки список
                    $input = new Element\Radio($this->_name."[{$v}]");

                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue($vv["value"]);
                    $element=$this->view->FormRadio()->setSeparator("<br>");
                    $out.=$element->render($input)."<br/>" ;
                    break;
                }
                case 3:{
                    //тип флажки список
                    $input = new Element\MultiCheckbox($this->_name."[{$v}]");
                    $input->setValueOptions(array_combine($list,$list));
                    $input->setValue(explode("~",$vv["value"]));
                    $element=$this->view->FormMultiCheckbox()->setSeparator("<br>");
                    $out.=$element->render($input)."<br/>" ;
                    break;
                }
                default:{
                    //по умолчанию поле ввода
                    $out.='<button type="button" class="f101_add" data-id="'.$this->_name."[{$v}][]".'">+</button>';
                    $list=explode("~",$vv["value"]);
                    foreach ($list as $kk=>$l){
                        $i=$k*20+$kk;
                        $input = new Element\Text($this->_name."[{$v}][]");
                        $input->setAttribute("id","f101_".$i);
                        $input->setValue($l);
                        $out.= "<br/>".$this->view->FormText($input).'<button type="button" class="f101_del" data-id="f101_'.$i.'">-</button>';
                    
                    }
                }
            }
    return $out;
}

/*возвращает  артикул, остаток, цена,код валюты*/
protected function _get_sklad($type="articul")
{
    $out="";
    for($i=0; $i<$this->_max_parameters_list; $i++){
        $out.="<input type=\"text\"><br>";
    }
    return $out;
}

/*возвращает цена,код валюты*/
protected function _get_price($type="price")
{
    $out="";
    for($i=0; $i<$this->_max_parameters_list; $i++){
        $out.="<input type=\"text\"><br>";
    }
    return $out;
}

    
/*обратный проход дерева для получения всех параметров до 0-го уровня*/
protected function _un_tree($id)
{
    $t=Simba::queryOneRecord("select id,subid,level from tovar_category where id=".(int)$id);
    if ($t["level"]!=0){
        $this->tree_ids[]=(int)$t["subid"];
        $this->_un_tree($t["subid"]);
    }
}

}
