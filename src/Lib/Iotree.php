<?php
/*
30.5.18 - обработка дерева переделана на интераторах, для увеличения скорости

21.4.17 - добавлен параметр view из ZEND, для генерации полей этим фреймворком

17.4.15 - изменен алгоритм обработки настроек из ini файлов


добавлены костыли для обработки файлов настроек, они трансформируются в константы для этого модкуля


Манипуляции с древовидным интерфесом структура которого хранится в спец. таблоице.
*/
namespace Admin\Lib;

use Admin\Lib\Tabadmin as tab_admin;
use Admin\Lib\Formitem as form_item;
use Exception;
use Zend\EventManager\EventManager;
use ADO\Service\RecordSet;
use Admin\Lib\Simba;
use Admin\Lib\Tree;
use RecursiveIteratorIterator;
use RecursiveArrayIterator;


class ioTree
{

/*
26.03.2013
введена обработка вывода дерева через RecordSet
так же изменена сопутсвующая функция для обработки интерфейса admin_menu, функция for_admin_menu_interface (динамическая)


17.12.09 Введена обработка ошибок по исключениям, ошибки теперь обрабатывает обработчик Exception, все локализовано теперь
17.12.09 обработчики операций (стандарнтых) можно переназначать, можно назначить действия на определенные имена элементов на форме браузера
Убраны передачи по ссылке в методы и функции
Наведен порядок с ошибками типа Notice

*/

public $interface_txt=[];//интерфейс HTML
public $error_row=[];//список бракованых идентификаторов таблицы, поля не прошли проверку! (целые строки)
public $error_item=[];//индивидуальные бракованные элементы
public $interface_name;//имя интерфейса для работы
public $line_table_obj;//хранит эземпляр объекта который генерирует структуру на низком уровне для линейных таблиц
public $tree_obj;//аналлогично для древовидных
public $struct0;//струтура интерфейса из таблицы
public $struct1;
public $struct2;
public $pole_dop=[];//данные из подолнительных полей
//private $pole__id;//уникальный ключ выводимой таблицы
private $del_record;//строка SQL для удаления записи (защищено, т.к. SQL правится)
public $tab_name;//имя таблицы, в данном интерфейсе
public $dop_sql=[];//массив для генерации выпадающих списков

public $spec_poles=[];//массив имен спец полей 0=>идентификатор 1=>ссылка на родителя 2=>уровень в дереве (0-корень)
private $arr_id;// содержит идентификаторы всех вложений, которые подченены начальному элементу нужно что бы удалить все вложения
public $print_html;//итог всей работы, т.е. то что выходит на экран
public $flag_out_form=true;//флаг, если ложь, тэг формы не выводится
public $get_interface_input;//параметры которые передаются в интерфейс GET запросом (для поля 49)

public $cod_form;//подпись формы уникальная, для исключения подделки

public $result_sql;//массив выборки текущего уровня в дереве
public $sp,$sql,$but;//для внутренних целей, хранят 
//public $result_sql;//хранит результат выборки SQL (после всех преобразований в интерфейсе), возможно это потребуется в обработ.функциях

public $error_code=[];//код ошибки входных данных, ключи массива - имена полей (POST)
public $error_message=[];//текстовые сообщения об ошибках
public $error_form_item=[];//данные об ошибках из объекта form_item
private $value_for_error=[];//сложный массив для выдачи статуса объекта, какие колоки и какие строки были обработаны
private $global_error_code=[];//коды ошибок вообще данного интерфейса, не связано со строками
private $global_error_message=[];//тексты ошибок вообще данного интерфейса, не связано со строками


//имена кнопок на форме по которым совершаются остандартные операции
public $button_save_name='save';//имя кнопки в форме по которой записывается запись
public $button_create_name='create';//имя кнопки в форме по которой создается элемент
public $button_del_name='del';//имя кнопки в форме по которой удаляется запись
public $button_save_all_name='_save_all_';//имя кнопки в форме по которой сохраняется все
public $button_createroot_name='createroot';//имя кнопки в форме по которой создается корневой элмент
public $button_optimize_table_name='_optimize_table_';//имя кнопки в форме по которой производится оптимизация

//имена функций обработчиков стандартных действий (нажатий кнопок на форме)
public $function_save_field_name;//имя функции записи одной записи, если не пусто, то вызывается эта функция, иначе встроеная
public $function_del_field_name;//имя функции обработчика удаления записи если не пусто, то вызывается эта функция, иначе встроеная

private $rs;//хранит RS для вывода дерева

public $view;
public $connection;
public $cache;
public $config;
public $EventManager;
public $container;
protected $permissions;     //массив доступа ACL
protected $aclService;      //сервис проверки ACL

    

function __construct($container,$view)
{//начальная инициализация
//интерфейс

//глобальные параметры
$this->tree_obj=new tree();//объект дерева
$this->tree_obj->menu_type=1;

		$this->tree_obj->menu_name='menu_e';
		if (isset($_COOKIE['menu_e'])) $this->tree_obj->status_old=$_COOKIE['menu_e']; else $this->tree_obj->status_old='';
		if(isset($_COOKIE['menu_e']))$this->tree_obj->status_old_id=$_COOKIE['menu_e']; else $this->tree_obj->status_old_id='';


$this->print_html='';
$this->value_for_error['column_name']=[]; //имена колонок которые были обработаны
$this->value_for_error['row_item']=[]; // ID строк которые были обработаны
$this->get_interface_input='';
$this->result_sql=[];
	
	$this->connection=$container->get('DefaultSystemDb');
	Simba::$connection=$this->connection;
	$this->view=$view;
	$this->cache=$container->get('DefaultSystemCache');
	$this->config=$container->get('Config');
	$SharedEventManager=$container->get('SharedEventManager');
	$this->EventManager=new EventManager($SharedEventManager);
	$this->EventManager->addIdentifiers(["simba.admin"]);
	$this->container=$container;
	$this->_form_item_=new form_item($view,$this->config);
	$this->line_table_obj=new  tab_admin($view,$this->config);//экземпляр линейного интерфейса
	simba::setConfig($this->config);
	simba::setContainer($container);

}//конец конструктора






function save_field($id,$level=0,$subid=0)
{
if (!$this->aclService->checkAcl("w",$this->permission)){
    $this->view->dialog_message="Ошибка записи. Доступ запрещен";
    $this->view->dialog_title="Ошибка";
    return;
}

//проверим код формы и убедимся что это не подделка
if($_SESSION['io_tree_interface'][$this->interface_name]!=$_POST['cod_form'] ) 
	{throw new Exception("Не верная подпись формы") ;//неверная подпись формы
	return false;
	}


//добавлено удаление кеша по тегам
if ($this->struct0['validator'] && $this->struct0['sort_item_flag'])
	{
		$tags=explode(",",$this->struct0['validator']);
		$this->cache->removeItems($tags);//ключи
		$this->cache->clearByTags($tags,true);//теги
		
	}


//получить имена переменных
$this->struct2=simba::queryAllRecords ('select * from design_tables where table_type=1 and row_type=2 and interface_name="'.$this->interface_name.'" and pole_name !=""');//сортируем по порядку столбцов
//сортируем по порядку столбцов
$count=simba::numRows();
$flag_error=false;//флаг ошибки, если истина - ошибка, т.е. вся строка брак

if (!$flag_error)
	{
	for ($i=0;$i<$count;$i++)
		{$c_=explode (",",$this->struct2['pole_global_const'][$i]);
		$validator=unserialize ($this->struct2['validator'][$i]);// print_r($validator);
		for ($p=0;$p<count($c_);$p++) $const[$p]=simba::get_const($c_[$p]);
		if (isset($_POST[$this->struct2['pole_name'][$i]][$id])) $infa=$_POST[$this->struct2['pole_name'][$i]][$id];
			else $infa=NULL;
	
	//проверим, еуказана ли функциЯ которая вызовется до записи поля
		if ($this->struct2['functions_befo'][$i]>'') 
			{
						$fn=$this->struct2['functions_befo'][$i];
						$fn=new $fn;

			$infa=$fn
													($this,
													$infa,
													$this->struct2,
													$this->struct2['pole_type'][$i],
													$this->pole_dop,
													$this->tab_name,
													$this->spec_poles[0],
													$const,
													$id,
													1);
			}
		//установим переменные, для использования в XML-описателе, там есть код PHP для обработки данных из конкретного поля
		$col_name=$this->struct2['pole_name'][$i];// имя колонки
		$row_item=$id;//номер-идентификатор строки по которой производится операция
		$infa=$this->_form_item_->save_form_item
												($row_item,
												$this->struct2['pole_type'][$i],
												$this->tab_name,
												$this->struct2['pole_name'][$i],
												$const,
												$infa,
												unserialize($this->struct2['properties'][$i])
												);
		$this->error_form_item[$col_name][$row_item]=$this->_form_item_->get_status($row_item);//получить по имени элмента формы
		$this->value_for_error['column_name'][]=$col_name;//сохраним имена колонок которые были обработана
		$this->value_for_error['row_item'][]=$row_item;//аналогично для обпределения какие строки обработаны
		//проверка внешним валидатором может быть несколько правил!

		if (preg_match ('/pole_dop([0-9]?)/i',$this->struct2['pole_name'][$i],$c)) 
			{//сохраняемое поле = дополнительному?
			if (!isset($c[1]) || $c[1]=='') { throw new Exception("Ошибка в доп поле");return false;}//немверное дополнительное поле
			if ($this->pole_dop[$c[1]]) 
						$tab_rec[$this->struct2['col_name'][$i]]=$this->pole_dop[$c[1]];//сохранить доп. поле для записи в таблицу
					else
					 {
					//если pole_dop пустое значение, тогда восстановить старое значение из таблицы, если оно там ввобще есть
					$n=simba::queryOneRecord('select '.$this->struct2['col_name'][$i].' from '.$this->tab_name.' where id="'.$id.'"');
					$tab_rec[$this->struct2['col_name'][$i]]=$n[$this->struct2['col_name'][$i]];
					}
			}
				else $tab_rec[$this->struct2['col_name'][$i]]=$infa;
		
		//проверим, еуказана ли функциЯ которая вызовется просле записи поля
		if ($this->struct2['functions_after'][$i]>'') 
				{//получить имя функции из таблицы
				$fn=$this->struct2['functions_after'][$i];
						$fn=new $fn;

				$fn
													($this,
													$infa,
													$this->struct2,
													$this->struct2['pole_type'][$i],
													$this->pole_dop,
													$this->tab_name,
													$this->spec_poles[0],
													$const,
													$id,
													2);
				}
		}//конец for ($i=0;$i<$count;$i++)

	//проверим если ли обработчик записи, если да, вызываем эту функцию и передаем туда все
	if ($this->struct0['properties']>'') 
		{
			$fn=$this->struct0['properties'];
						$fn=new $fn;
						$fn(
							$this, //данный объект со всеми его устновками
							$tab_rec, // массив структура пригодная для записи в базу данных
							$id,     /*0-новая запись, иначе ID редактируемой записи*/
                            $this->spec_poles, /*массив: [имя_поля_идентификатора,имя_поля_ссылки_на_родителя,имя_поля_уровня_в_дереве]*/
                            $this->tab_name, //имя таблицы
							-2
							);
		} else {
            
        $alt_name=explode(',',$this->struct0['pole_global_const']);//получить список псевдонимов, если они есть
        foreach ($alt_name as $v) {
            unset($tab_rec[$v]);
        }
		// переделано на RS
		$rs=new RecordSet();
		$rs->CursorType = adOpenKeyset;
		$rs->open("select * from ".$this->tab_name,$this->connection); //считаем данные
		if ($id)
			{
				//обновление данных
				$rs->Find($this->spec_poles[0]."=".(int)$id,0,adSearchForward);
					foreach ($tab_rec as $field=>$value){
						if ($value==="null" || $value==="NULL") {$value=null;}
						$rs->Fields->Item[$field]->Value=$value;
					}
				$rs->Update();
			}
		else
			{
				//добавление новой записи
				$rs->AddNew();
				$rs->Fields->Item[$this->spec_poles[0]]->Value=$id;
				$rs->Fields->Item[$this->spec_poles[2]]->Value=$level;
				$rs->Fields->Item[$this->spec_poles[1]]->Value=$subid;
					foreach ($tab_rec as $field=>$value){
						if ($value==="null" || $value==="NULL") {$value=null;}
						$rs->Fields->Item[$field]->Value=$value;
					}
				$rs->Update();
				
			}
}
		
	/*$tab_rec[$this->spec_poles[0]]=$id;//это идентификатор записи
	$tab_rec[$this->spec_poles[2]]=$level;//это уровень в дереве
	$tab_rec[$this->spec_poles[1]]=$subid;//ссылка на родителя
	simba::replaceRecord ($tab_rec,$this->tab_name);//echo '<pre>';print_r($tab_rec);echo '</pre>';*/
	//конец IF для проверки типа данных
	}


	
}

function delete_field($id)
{
if (!$this->aclService->checkAcl("d",$this->permission)){
    $this->view->dialog_message="Ошибка записи. Доступ запрещен";
    $this->view->dialog_title="Ошибка";
    return;
}

//проверим код формы и убедимся что это не подделка
if($_SESSION['io_tree_interface'][$this->interface_name]!=$_POST['cod_form'] ) 
	{throw new Exception("Не верная подпись формы");//неверная подпись формы
	return false;
	}
	
//добавлено удаление кеша по тегам
if ($this->struct0['validator'] && $this->struct0['sort_item_flag'])
	{
		$tags=explode(",",$this->struct0['validator']);
		$this->cache->removeItems($tags);//ключи
		$this->cache->clearByTags($tags,true);//теги
		
	}

//удалить стурктуру по идентификатору
//получить уровень и ссылку на родителя
$razd=simba::queryOneRecord("SELECT * FROM ".$this->tab_name." where ".$this->spec_poles[0]."=".$id);
$this->struct2=simba::queryAllRecords ('select * from design_tables where table_type=1 and row_type=2 and interface_name="'.$this->interface_name.'" and pole_name !=""');//сортируем по порядку столбцов
$this->arr_id=array($id);
$count=simba::numRows();//кол-во данных описателя структуры
$this->get_tree_id ($razd[$this->spec_poles[1]],$razd[$this->spec_poles[2]],$razd[$this->spec_poles[0]]);//получить массив идентификаторов подчинунных этому элементу
for($i=0;$i<count($this->arr_id);$i++)
		 {//цикл по строкам таблицы (уникальное - идентификатор удаляемой строки
			for($j=0;$j<$count;$j++)
				{//цикл по всем полям-описаниям для данной таблицы
				$id=$this->arr_id[$i];
				$const=simba::get_const(explode (",",$this->struct2['pole_global_const'][$j]),true);//массив констант для данного поля
			 	//проверим, еуказана ли функциЯ которая вызовется просле записи поля
				if ($this->struct2['functions_befo_del'][$j]>'') 
						{//получить имя функции из таблицы
						$fn=$this->struct2['functions_befo_del'][$j];
						$fn=new $fn;
						$fn(
																$this,
																'',
																$this->struct2,
																$this->struct2['pole_type'][$j],
																$this->pole_dop,
																$this->tab_name,
																$this->spec_poles[0],
																$const,
																$id,
																3);
						}
				}
		 
    
    if ($this->struct0['functions_befo_del']) {//имеется функция удаления
        $fn=$this->struct0['functions_befo_del'];
        $fn=new $fn;
        $fn(
            $this,
            null,
            $id,
            $this->spec_poles,
            $this->tab_name,
            -3    
        );
		} else {//стандартное удаление
        simba::query("delete from ".$this->tab_name." where id='".$this->arr_id[$i]."'");//удаление записи из таблицы СУБД
		 //дополнительная SQL для удаления записей
		 if ($this->del_record>'') 	{
             eval("\$id = \"$id\";");
             eval("\$sql = \"$this->del_record\";");
             simba::query($sql);
         }

    }
    }

}






//**********************************************************вывод на эуркан

public function create_interface($interface_name,$flag_out_form=true)
{$this->flag_out_form=$flag_out_form;

//смотрим внешние  переметры в этот интерфейс
if (isset($_GET['get_interface_input'])) $this->get_interface_input=unserialize(base64_decode($_GET['get_interface_input']));


$this->interface_name=$interface_name; //имя интерфейса

//по идентификатору полуячить имя таблицы с которой работаем
$this->struct0=simba::queryOneRecord ('select * from design_tables where table_type=1 and row_type=0 and interface_name="'.$this->interface_name.'"');

/*получим сервис ACL*/
$this->aclService=$this->view->acl()->GetAclService();
$permissions=@unserialize($this->struct0['caption_style']);
$this->permission=[$permissions['owner_user'],$permissions['owner_group'],$permissions['permission']  ];
/*читать разрешено?*/
if (!$this->aclService->checkAcl("r",$this->permission)){
    $this->view->dialog_message="Ошибка чтения. Доступ запрещен";
    $this->view->dialog_title="Ошибка";
    return;
}

 

$this->tab_name=$this->struct0['table_name'];//$tree_name=$this->struct0['tree_name'];
if (!$this->tab_name) {throw new Exception("Не указано имя таблицы");return false;}
$this->spec_poles=explode (",",$this->struct0['pole_prop']); //получить список симен спец полей для организации дерева (идентификатор, ссылка на родителя, уровень)
$this->tree_obj->menu_name=$this->tab_name;
if (isset($_COOKIE[$this->tab_name])) $this->tree_obj->status_old=$_COOKIE[$this->tab_name]; else $this->tree_obj->status_old=NULL;
if (isset($_COOKIE[$this->tab_name.'_id'])) $this->tree_obj->status_old_id=$_COOKIE[$this->tab_name.'_id']; else $this->tree_obj->status_old_id=NULL;
$this->del_record=str_replace('$pole_dop','$this->pole_dop',$this->struct0['default_sql']);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN

//загрузим текстовый интерфейс для выбранной таблицы из ьаблицы с текстами
$c=simba::queryAllRecords("select item_name,text from design_tables_text_interfase where 
							table_type=1  and interface_name='".$this->interface_name."'");

for ($i=0;$i<simba::numRows();$i++)
	$this->interface_txt[$c['item_name'][$i]]=$c['text'][$i];


if (!$this->spec_poles[0] || !$this->spec_poles[1] || !$this->spec_poles[2]) {throw new Exception("Ошибка в доп поле");return false;}
//определим какие кнопки будут выводиться
$this->but=explode (",",$this->struct0['col_name']);
//проверим количество дополнительных полей и преобразуем их в массив
$this->struct2=simba::queryAllRecords ('select properties,pole_name,col_name,pole_type,pole_global_const from design_tables where table_type=1 and row_type=1 and interface_name="'.$this->interface_name.'"');
$c='pole_dop';//префикс

//проьбежимся по всем переменным и преобразуем в массив
if (isset($this->struct2['pole_type']))
	for ($i=0;$i<count($this->struct2['pole_type']);$i++)
		{if (isset($_POST[$c.$i]) || $this->struct2['pole_type'][$i]==41) 
			{//$this->pole_dop[$i]=$_POST[$c.$i]; 
			$c_=explode (",",$this->struct2['pole_global_const'][$i]);
			for ($p=0;$p<count($c_);$p++) $const[$p]=simba::get_const($c_[$p]);
			$this->pole_dop[$i]=$this->_form_item_->save_form_item(0,
																	$this->struct2['pole_type'][$i],
																	$this->tab_name,
																	$this->struct2['pole_name'][$i],
																	$const,
																	$_POST[$c.$i],
																	unserialize($this->struct2['properties'][$i])
																	);
			}
		else $this->pole_dop[$i]=0;
		$a=$this->pole_dop[$i];//print_r($a);
		eval("\$this->pole_dop$i = \"$a\";");
		}

@$d=array_keys($_POST[$this->button_del_name]);
@$s=array_keys ($_POST[$this->button_save_name]); //определить какой идентификатор нужно обрабатывать
@$c=array_keys ($_POST[$this->button_create_name]);
if (isset($_POST['level'][$s[0]])) $level=$_POST['level'][$s[0]];//это уровень в дереве
 	else $level=0;
if (isset($_POST['subid'][$s[0]]))$subid=$_POST['subid'][$s[0]];//ссылка на родителя
		else $subid=0;

if (isset($_POST[$this->button_optimize_table_name]))
	{//оптимизация таблицы
	simba::query("optimize table $this->tab_name");
	}

if (is_array($d)) {
    if ($this->function_del_field_name) {
        call_user_func($this->function_del_field_name,$d[0],$this);//нестандартный обработчик записи
    } else {
        $this->delete_field($d[0]);
    }
}



//новый корневой элемент
if (isset($_POST[$this->button_createroot_name])) $s[0]=0;//все поля пусты
//подуровень
if (is_array($c))
	 {//новый подуровнь
	$s[0]=0;//новая запись с пустыми полями, кроме дополнительного если оно есть
	$subid=$c[0];//ссылка на родителя
	$level=$_POST[$this->spec_poles[2]][$c[0]]+1;//это уровень в дереве
	
	}

if (is_array($s)) 
	{//сохраняем/создаем новую
            $this->save_field($s[0],$level,$subid);
	}

if (isset($_POST['global_action_id_array_b']))
 {//сохраняем все
	//проверим код формы и убедимся что это не подделка
	if($_SESSION['io_tree_interface'][$this->interface_name]!=$_POST['cod_form'] ) 
		{throw new Exception("Не верная подпись формы");//неверная подпись формы
		return false;
		}
	if (is_array($_POST['id_'])) 
		foreach ($_POST['id_'] as $i) 
			{
				$level=$_POST['level'][$i];//это уровень в дереве
				$subid=$_POST['subid'][$i];//ссылка на родителя
				$this->save_field ($i,$level,$subid);
			}
	if ($this->struct0['properties']>'') 
						{//получить имя функции из таблицы
						$fn=$this->struct0['properties'];
						$fn=new $fn;

								$fn(	$this,NULL,$level,$subid);
								}

	
	}

//это для создания корневого элемента
$s="";//simba::get_style_class_ fromtable($this->struct0['caption_style']);
//генерируем уникальный код формы, подпись, что бы исключить подделки
$_SESSION['io_tree_interface'][$this->interface_name]=md5(microtime());//уникальный код формы
$this->cod_form=$_SESSION['io_tree_interface'][$this->interface_name];

$this->print_html='';
if ($this->flag_out_form)
	{$this->print_html.='<form name="form1" method="post" action="">';
	//встаим код формы, вроде подписи 
	if ($this->cod_form>'') $this->print_html.= '<input name="cod_form" type="hidden" value="'.$this->cod_form.'" />';
	}
$this->print_html.='<center '.$s.'>'.$this->interface_txt['caption0'].'</center>';
//выводим дополнительное поле если оно есть
//получить настройки доп поля ввода до основной таблицы
//получить настройки доп поля ввода до основной таблицы
$this->struct1=simba::queryAllRecords ('select * from design_tables where table_type=1 and row_type=1 and interface_name="'.$this->interface_name.'" order by id');
$dcount=simba::numRows();
for ($jjj=0;$jjj<$dcount;$jjj++)
	{
	$const_dop_pole=simba::get_const($this->struct1['pole_global_const'][$jjj]);//константы доп аоля если есть
	//дополнительное поле до вывода всей таблицы
	if ($this->struct1['pole_type'][$jjj]>0) 
		{//если указан тип поля тогда работаем с ним
		$this->dop_sql=[];
		if ($this->struct1['pole_spisok_sql'][$jjj]) 
			{$sql__=stripslashes($this->struct1['pole_spisok_sql'][$jjj]);//echo $sql__;
			$sql__= preg_replace ("/\"/",'\\\"',$sql__);
			$sql__=str_replace('$pole_dop','$this->pole_dop',$sql__);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
			eval("\$sql__ = \"$sql__\";");
			//$this->dop_sql=simba::spec_parse_sql($sql__);//print_r( $sql__);
			$this->dop_sql=simba::queryAllRecords($sql__);
			
			//if (simba::$errorMessage) throw new Exception(__CLASS__,5,array($jjj));
			//==================проверим, если значение поля входит в диапозон выборки SQL тогда все хорошо, в противном случае надо это поле обнулить
			//т.к. возможно что это поле зависит от состояния предыдущих полей, т.е. надо присвоить значение по умолчанию
			if (is_array($this->dop_sql['id'][0]))
				{//вариант для сложного списка
				$_fl_=false;
				for($x=0;$x<count($this->dop_sql['id']);$x++) if (@!in_array($this->pole_dop[$jjj],$this->dop_sql['id'][$x])) {$_fl_=true;break;}
				if (!$_fl_) $this->pole_dop[$jjj]=0;
				} 
				else //вариант для простого списка
					if (@!in_array($this->pole_dop[$jjj],$this->dop_sql['id'])) {$this->pole_dop[$jjj]=0;}
			}
		//если значение пустое и указано значение по умолчанию, тогда установить 
		if (($this->pole_dop[$jjj]==''  ) && $this->struct1['default_sql'][$jjj]) 
				{$sql__=stripslashes($this->struct1['default_sql'][$jjj]);
				$sql__=str_replace('$pole_dop','$this->pole_dop',$sql__);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
				eval("\$sql__ = \"$sql__\";");//echo $sql__.'<br>';
				$df=simba::queryOneRecord($sql__);//if (simba::$errorMessage) throw new Exception(__CLASS__,6,array($jjj)) ;
				$this->pole_dop[$jjj]=$df['id'];
				$a=$this->pole_dop[$jjj]; 
				eval("\$this->pole_dop$jjj = \"$a\";");//echo "def=$a; jjj=$jjj<br>";//eval ("echo \$this->pole_dop$jjj;");
				}
		if ($this->struct1['functions_befo_out'][$jjj]>'') 
				{//получить имя функции из таблицы
						$fn=$this->struct1['functions_befo_out'][$jjj];
						$fn=new $fn;

				$fn(
														$this,
														$a,
														$this->struct1,
														$this->struct1['pole_type'][$jjj],
														$this->pole_dop,
														$this->tab_name,
														$this->spec_poles[0],
														$const_dop_pole,
														0,
														1);
				}
		$this->line_table_obj->caption($this->interface_txt['caption_dop_'.$jjj],"",$jjj);
		//$style=simba::get_style_class_ fromtable(explode (',',$this->struct1['pole_style'][$jjj]),explode (',',$this->struct1['pole_prop'][$jjj]));//массив стилей полей (если двойное тогда 2 элемента

		
		$this->line_table_obj->row_dop_type($this->struct1['pole_type'][$jjj],
											'pole_dop'.$jjj,
											$this->struct1['pole_prop'][$jjj],//$style,
											@$this->dop_sql['name'],
											@$this->dop_sql['id'],
											$const_dop_pole,
											@$this->dop_sql['group']['name'],
											$jjj,
											$this->struct1['value'][$jjj],
											unserialize($this->struct1['properties'][$jjj]));
		$this->line_table_obj->row_dop_value($this->pole_dop[$jjj],$jjj);
		}
	}

//сама выборка для заполения таблицы
$sql=$this->struct0['pole_spisok_sql'];
$sql= preg_replace ("/\"/",'\\\"',$sql);
$sql=str_replace('$pole_dop','$this->pole_dop',$sql);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
$sql=str_replace('$get_interface_input','$this->get_interface_input',$sql);//поправим для внешних данных
eval("\$sql = \"$sql\";");
if ($sql) $this->sql=" and ".$sql;
	else $this->sql='';

$this->sp=[];
//получить списки которые указаны для полей//получить сортированный список имен и типов полей которые будут выводиться в дереве
$this->struct2=simba::queryAllRecords("SELECT * FROM  design_tables where table_type=1 and row_type=2 and interface_name='".$this->interface_name."' order by col_por asc"); 
$count=simba::numRows();
for ($i=0;$i<$count;$i++) 
		{//выполним SQL запросы и сохраним это в массиве (ключи массива [имя_поля][тип_информации].....[]
		if ($this->struct2['pole_spisok_sql'][$i])
				{$sql__=addslashes($this->struct2['pole_spisok_sql'][$i]);
				eval("\$sql__ = \"$sql__\";");
				$this->sp['sql'][$i]=simba::queryAllRecords($sql__);
				}//если ошибка
		//получим соответствующие стили для каждого из полей
		if ($this->struct2['pole_style'][$i]) $this->sp['pole_style'][$i]="";//simba::get_style_class_ fromtable($this->struct2['pole_style'][$i]);
	//получим соответствующие сво-ва для каждого из полей
		$this->sp['pole_prop'][$i]=$this->struct2['pole_prop'][$i];

		//получим соответствующие константы для каждого из полей
		if ($this->struct2['pole_global_const'][$i]) $this->sp['const'][$i]=simba::get_const($this->struct2['pole_global_const'][$i]);
		//получим соответствующие стили заголовки для каждого из полей
		if ($this->struct2['caption_style'][$i]) $this->sp['caption_style'][$i]="";//simba::get_style_class_ fromtable($this->struct2['caption_style'][$i]);
		}

//рельсы \ADO
$this->rs=new RecordSet();
$this->rs->CursorType = adOpenKeyset;
$this->rs->MaxRecords=0;
$this->rs->open("SELECT * FROM  ".$this->tab_name." where 1=1 ".$this->sql,$this->connection);


$this->create_tree(0,0);


$this->line_table_obj->flag_out_form=false;//не выводить форму
$this->print_html.= $this->line_table_obj->tab_fetch();
//$this->print_html.=$this->tree_obj->get_html();
//$this->tree_obj->menu_type=1;

$this->print_html.=$this->tree_obj->get_tree();
 $this->print_html.="<br>";

if (isset($this->but[3]) && $this->but[3]) $this->print_html.= '<br><br><input name="createroot" style="font-size:xx-small;" type="submit" value="Создать корневой элемент">';
if (isset($this->but[4]) && $this->but[4]) $this->print_html.=  '&nbsp;&nbsp;<input name="global_action_id_array_b" style="font-size:xx-small;background-color:#00ff00;font-weight:bolder;" type="submit" value="Сохранить все">';
if (isset($this->but[5]) && $this->but[5]) $this->print_html.= '&nbsp;&nbsp;<input name="_optimize_table_" style="font-size:xx-small;color:#ffffff; background-color:#0000ff;font-weight:bolder;" type="submit" value="Оптимизировать таблицу">';
if ($this->flag_out_form) $this->print_html.=  '</form><br>';
$this->print_html.= $this->interface_txt['coment0'];

}


public function print_interface()
{
echo $this->print_html;
}

public function get_interface()
{
return $this->print_html;
}




//внутренние

private function get_tree_id ($subid,$lev,$id=0)
{
//получить список подразделов
//возвращает массив $this->arr_id содержит идентификаторы всех вложений, которые подченены начальному элементу
	if ($id>0) $this->get_tree_id ($id,$lev+1);
	else
	{
		$razd=simba::queryAllRecords("SELECT * FROM  ".$this->tab_name."  where ".$this->spec_poles[2]."=".$lev." and ".$this->spec_poles[1]."=".$subid); 
		//$razd=simba::queryAllRecords("SELECT * FROM  ".$this->tab_name." where level='".$lev."' and subid='".$subid ."' and user='".$_POST['user_']."' and language=".language." order by id" );
		$count=simba::numRows();
		if ($count>0)
			 {for ($i=0; $i<$count;$i++) 
				{$this->arr_id[]=$razd['id'][$i];
				$this->get_tree_id ($razd['id'][$i],$lev+1);
				}
			 }
	}
}

public function create_tree($subid,$lev)
{
    $array=[];
    while (!$this->rs->EOF){
        $r=[];
        foreach ($this->rs->DataColumns->Item_text as $column_name=>$columninfo){
            $r[$column_name]=$this->rs->Fields->Item[$column_name]->Value;
        }    
        $array[]=$r;
        $this->rs->MoveNext();
    }
        $tree=[];
    
    foreach($array as $cat) {
        $tree[$cat['id']] = $cat;
       // unset($tree[$cat['id']]['id']);
    }
     $tree['0'] = array(
         'subid' => '',
         'title' => 'Корень',
         'category_url' => ''
     );
    foreach ($tree as $id => $node) {
        if (isset($node['subid']) && isset($tree[$node['subid']])) {
            $tree[$node['subid']]['___sub___'][$id] =& $tree[$id];
        }
    }
    //итоговое дерево
    if (!empty($tree[0]['___sub___'])){
        $tree=$tree[0]['___sub___'];
    } else {
        $tree=[];
    }
    
//\Zend\Debug\Debug::dump($tree);
     $iterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($tree),
            RecursiveIteratorIterator::SELF_FIRST
        );
    foreach ($iterator as $id=>$item){
        if (!is_array($item)){
            continue;
        }
        if ($id==="___sub___"){
            continue;
        }
        $depth = $iterator->getDepth();
        $lev1=$depth;
        if ($depth>0){
            $depth=$depth/2;
        }
        $i=0;
		//работаем в определенном уровне и выводим поля которые указаны при конструировании
        $data='';
		for ($j=0;$j<count($this->struct2['pole_type']);$j++){//это цикл по полям
            //инициализируем переменные на всякий случай
            if (!isset($this->sp['pole_style'][$j]))$this->sp['pole_style'][$j]=NULL;
            if (!isset($this->sp['pole_prop'][$j]))$this->sp['pole_prop'][$j]=NULL;
            if (!isset($this->sp['sql'][$j]['name']))$this->sp['sql'][$j]['name']=NULL;
            if (!isset($this->sp['sql'][$j]['id']))$this->sp['sql'][$j]['id']=NULL;
            if (!isset($this->sp['sql'][$j]['sp_group_array']))$this->sp['sql'][$j]['sp_group_array']=NULL;
            if (!isset($this->sp['const'][$j]))$this->sp['const'][$j]=NULL;
            if (!isset($this->sp['caption_style'][$j]))$this->sp['caption_style'][$j]=NULL;
            
            if (isset($this->interface_txt['caption_col_'.$this->struct2['pole_name'][$j]])){
                    $data.=htmlentities ($this->interface_txt['caption_col_'.$this->struct2['pole_name'][$j]],ENT_COMPAT,"UTF-8");
                }
			//работаем с функцией до вывода поля на экран
			if ($this->struct2['functions_befo_out'][$j]>'') {//получить имя функции из таблицы
                $fn=$this->struct2['functions_befo_out'][$j];
                $fn=new $fn;
                    
                $const=explode (',',$this->sp['const'][$j]);//массив констант
                
                $item[$this->struct2['col_name'][$j]]=$fn
																	($this,
																	//$this->result_sql[$this->struct2['col_name'][$j]][$i],//сама инфа
																	$item[$this->struct2['col_name'][$j]],
																	$this->struct2,
																	$this->struct2['pole_type'][$j],
																	$this->pole_dop,
																	$this->tab_name,
																	$this->spec_poles[0],
																	$const,
																	//$this->result_sql[$this->spec_poles[0]],
																	$id,
																	0,
																	$i,
																	$j ,  //порядковый номер элемента  в элементе строки (ТОЛЬКО ДЛЯ ДЕРЕВА!)
																	$item
																	);

                    $this->sp['const'][$j]=implode (',',$const);//обратно в список, т.к. наша функция может изменять константы
				}
			
            //само поле поле по его идентификатору
            
            
			$data.=$this->_form_item_->create_form_item
						(
							$this->struct2['pole_type'][$j],
							$this->struct2['pole_name'][$j].'['.$id.']',
							@$item[$this->struct2['col_name'][$j]],
								
							$this->sp['pole_style'][$j].' '.$this->sp['pole_prop'][$j],
							
							$this->sp['sql'][$j]['name'],
							$this->sp['sql'][$j]['id'],
							$this->sp['sql'][$j]['sp_group_array'],
			
							$this->sp['const'][$j],
							$this->struct2['value'][$j],
							unserialize($this->struct2['properties'][$j])
						);
			}
			$data2="";
			//кнопки в текущей строке, если они выбраны
			if ($this->but[0]) $b0="<input type=\"submit\" name=\"save[".$id  ."]\" value=\"запись\">"; else $b0='';
			if ($this->but[1]) $b1="<input type=\"submit\" name=\"create[".$id."]\" value=\"нов.подуров.\">"; else $b1='';
			if ($this->but[2]) $b2="<input type=\"submit\" name=\"del[".$id."]\" value=\"удал\" class=\"del\">"; else $b2='';
	
			if ($this->struct0['col_por'] && $this->struct0['col_por']<$lev1) $data2.=$b0.$b2;	else 	$data2.=$b0.$b1.$b2;
			
			$data2.="<input name=\"level[".$id."]\" type=\"hidden\" value=\"".$item[$this->spec_poles[2]]."\">
					<input name=\"subid[".$id ."]\" type=\"hidden\" value=\"".$item[$this->spec_poles[1]]."\">
					<input name=\"id_[]\" type=\"hidden\" value=\"".$id."\">";
			
			$data.=htmlentities ($data2,ENT_NOQUOTES | ENT_XHTML,"UTF-8");

        
        $this->tree_obj->add_item($data,'',$depth,'','',$id);
    }

}



}
?>