<?php
namespace Admin\Lib\Func;

class GetLocales
{


public function __invoke($obj,$infa,$struct_arr,$pole_type,$pole_dop,$tab_name,$idname,$const,$id,$action)
{
	//выводит список локалей сайта из константы конфигурации
	
	//echo "<pre>";
	//print_r($obj);
	
	
	$l=$obj->config["locale_enable_list"];// массив допустимых локалей                  
		//подменить список
		$obj->dop_sql['name']=$l;
		$obj->dop_sql['id']=$l;
		//это значение по умолчанию
	if  (!$obj->pole_dop[0]) 
		{
			$obj->pole_dop[0]=$obj->dop_sql['id'][0];
			$obj->pole_dop0=$obj->pole_dop[0];
			}
	$_SESSION["LOCALE_ADMIN_KOSTIL1"]=$obj->pole_dop[0];
	return $infa;
}
}