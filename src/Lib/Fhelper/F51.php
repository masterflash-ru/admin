<?php

/*
Общие параметры товара
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Admin\Lib\Simba;

class F51 extends Fhelperabstract 
{
	protected $hname="Специальное для zrkuban.ru - выбор типа автомобиля";
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
    
$out=<<<EOT
<label>Выберите марку: <select name="avto_brend" id="avto_brend"></select></label><br/>
<label>Выберите модель: <select name="avto_model" id="avto_model"></select></label>
<input type="hidden" value="{VALUE}" id="brend_model" name="{NAME0}" >
<script>
$(function() {
var start_avto_brend=0,start_avto_model=0;

//загрузка моделей
$("#avto_brend").change(function(e) {
        var start=start_avto_brend, avto_brend=0;
		if (start>0) {avto_brend=start;} else {avto_brend=$(this).val();}
        $("#avto_model").load("/admin/ajax",{type:"load_avto_model",avto_brend:avto_brend},
                function()
                    {
					start_avto_brend=0;
                       if (start>0) 
                        {
                            $("#avto_brend option[value="+start+"]").prop("selected",true);
                            $("#avto_brend").val(start);
                         } 
                       $("#avto_model").change();
                    });

});

//загрузка марок
$("#avto_brend").load("/admin/ajax",{type:"load_avto_brend"},function()
    {
		var starts=$("#brend_model").val();
		starts=starts.split(",");
		start_avto_brend=starts[0];start_avto_model=starts[1];
        //первоначальная загрузка
        $("#avto_brend").change();
        }) ;

$("#avto_model").change(function(e) {
    var start=start_avto_model;
    if (start>0)    
        {
		start_avto_model=0;
            $(this).val(start);
            $("#avto_model option[value="+start+"]").prop("selected",true);
			
        }
		$("#brend_model").val($("#avto_brend").val()+","+$(this).val());
    
});
});
</script>

EOT;

    
    return $out;
}



}
