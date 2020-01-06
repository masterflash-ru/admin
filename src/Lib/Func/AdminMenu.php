<?php
namespace Admin\Lib\Func;
$GLOBALS['for_menu_admin_interface_class']=false;


class AdminMenu
{


public function __invoke ($obj,$infa,$struct_arr,$pole_type,$pole_dop,$tab_name,$idname,$const,$id,$action,$i__,$j__)
{
	 
/*
входные параметры:
$obj - Экземпляр объекта с интерфейсом
$infa сама информация
$struct_arr
$pole_type - тип поля (идентификатор)
$obj->pole_dop - массив данных доп. поля
$tab_name - имя редактируемой таблицы СУБД
$idname - имя идентификатора (уникального поля) таблицы СУБД
$const - массив констант для поля
$id - идентификатор редактирумой строки таблицы
$action - действие 0-чтение, 1-запись, 2- удаление

// только для дерева!
$i__ - порядковый номер элемента в строке
$j__ - порядковый номер элемента в колонке

*/
//sp_group_array


if (!$GLOBALS['for_menu_admin_interface_class']) 
{
	$obj->sp['sql'][$j__]['sp_group_array']=[];
	$obj->sp['sql'][$j__]['name']=[];
	$obj->sp['sql'][$j__]['id']=[];
	
	
	$controllers_description=$obj->EventManager->trigger("GetControllersInfoAdmin",NULL,["name"=>"admin","container"=>$obj->container]);
	//цикл по контроллерам
	//конвертируем в старый формат
	foreach ($controllers_description as $name=>$desc)
		{
			//внутри контроллера
			if (is_array($desc))
				{
					foreach ($desc as $meta)
						{
							$obj->sp['sql'][$j__]['sp_group_array'][]=$meta["description"];
							$obj->sp['sql'][$j__]['name'][]=$meta["urls"]['name'];		
							$obj->sp['sql'][$j__]['id'][]=$meta["urls"]['url'];
							//
						}
				}
		}
	//\Laminas\Debug\Debug::dump($obj->sp['sql'][$j__]);
	
}
//$obj->sp['sql'][$j__]['sp_group_array']=array(1,2);
//$obj->sp['sql'][$j__]['name']=array(array(1,2),array(3,4));
//$obj->sp['sql'][$j__]['id']=array(array(1,2),array(3,4));

$GLOBALS['for_menu_admin_interface_class']=true;
return $infa;
}

}