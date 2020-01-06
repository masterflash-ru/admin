<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Laminas\Form\Element;
use Admin\Lib\Simba;

class F101 extends Fhelperabstract 
{
	protected $hname="Складская информация по товару";
    protected $category=101;
	protected $itemcount=1;
    protected $tree_ids=[];

	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	

        
/*обработка записи

$this->id - ID записи основной таблицы (товара)
*/
public function save()
{
    if (isset($_GET['get_interface_input']) || isset($_GET["id"])) {
        $tovar_category=(int)$_GET["id"];
    } else {
        return "Не верное обращение к элементу F101, нет категории товара";
    }

    //смотрим параметры, если они есть выдаем фрейм с редактором иначе простую форму для ввода
    //проход дерева до 0-го уровня, получим все ID
    $this->tree_ids[]=$tovar_category;
    $this->_un_tree($tovar_category);
     
    $parameters=Simba::queryOneRecord("select count(*) as c
            from tovar_category_parameters as p
                where p.tovar_category in(". implode(",",$this->tree_ids) .")");

    if ($parameters["c"]>0){return true;}
    
   
   $values=$_POST[$this->col_name][$this->id];

    Simba::query("delete from tovar_catalog_sklad where tovar_catalog=".(int)$this->id);
    Simba::ReplaceRecord([
                        "tovar_catalog"=>(int)$this->id,
                        "articul"=>$values["articul"],
                        "ostatok"=>$values["ostatok"]
                        ],"tovar_catalog_sklad");
    $money_type=Simba::queryAllRecords("select * from tovar_price_type");
    Simba::query("delete from tovar_catalog_price where tovar_catalog=".(int)$this->id);
    foreach ($money_type["sysname"] as $k=>$mt){
            Simba::ReplaceRecord([
                "tovar_catalog"=>(int)$this->id,
                "price"=>str_replace(",",".",$values[$mt]),
                "tovar_price_type"=>$mt,
                "currency"=>"RUB"
            ],"tovar_catalog_price");

        }
    
}
    
    
    
/*рендер элемента в админке*/	
public function render()
{//
    if (isset($_GET['get_interface_input'])) {
        $tovar_category=(int)$_GET['id'];
    } else {
        return "Не верное обращение к элементу F101, нет категории товара";
    }

 	preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
    
    //смотрим параметры, если они есть выдаем фрейм с редактором иначе простую форму для ввода
    //проход дерева до 0-го уровня, получим все ID
    $this->tree_ids[]=$tovar_category;
    $this->_un_tree($tovar_category);


    $parameters=Simba::queryOneRecord("select count(*) as c
            from tovar_category_parameters as p
                where p.tovar_category in(". implode(",",$this->tree_ids) .")");
       
    
    if ($parameters["c"]>0){
            return '<iframe frameborder="0" width="100%" id="ff101" src="/adm/tovar_category_parameters?get_interface_input=1&tovar='.$id.'&tovar_category='.$tovar_category.'"></iframe>';
    }
    //формируем простые поля, параметров расширяющих товар нет
    //]итаем типы цен
    $money_type=Simba::queryAllRecords("select * from tovar_price_type");
    
    //информация склада
    $sklad=Simba::queryOneRecord("select * from tovar_catalog_sklad 
                    where 
                        tovar_catalog={$id} and 
                        tovar_category_parameters_value is null");
    
    $out="<table class=\"f101\">";
    $out.="<th>Артикул</th><th>Остаток</th>";
    foreach ($money_type["name"] as $name){
        $out.="<th>Цена: {$name}</th>";
    }
    $out.="<tr>";
    foreach (["articul","ostatok"] as $item){
        $input = new Element\Text($this->name[0].'['.$item.']');
        $input->setValue($sklad[$item]);
        $out.="<td>".$this->view->FormText($input)."</td>";
    }
    
    //читаем цены
    foreach ($money_type["sysname"] as $mt){
        $price=Simba::queryOneRecord("select price from tovar_catalog_price 
                        where 
                            tovar_catalog={$id} and
                            tovar_price_type='{$mt}' and
                            tovar_category_parameters_value is null");
        $input = new Element\Text($this->name[0].'['.$mt.']');
        $input->setValue($price["price"]);
        $out.="<td>".$this->view->FormText($input)."</td>";

    }
    $out.="</tr>";
    return $out.'</table>';
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
