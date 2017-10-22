<?php
/*
21.4.17 - добавлен параметр view из ZEND, для генерации полей этим фреймворком

1.4.16 - добавлен костыль для выпадающего списка в доп поле, значения могут принимать сериализованные данные.

15.3.16 - если поле является псевданимом (в конструкторе, тогда при удалении записи не вызывается описатель удаления поля в html_item.xml)
	(строка 420)

17.4.15 - изменен алгоритм обработки настроек из ini файлов


добавлены костыли для обработки файлов настроек, они трансформируются в константы для этого модкуля



Манипуляции с линейным интерфесом структура которого хранится в спец. таблоице.

Более не поддерживается

*/
namespace Admin\Lib;

use Admin\Lib\Tabadmin as tab_admin;
use Admin\Lib\Formitem as form_item;
use Exception;
use Zend\EventManager\EventManager;
use ADO\Service\RecordSet;
use Admin\Lib\Simba;



class Ioline
{
/*
01.08.2013 - теперь при удалении передаются параметры properties как и при записи



13.05.2012 - сиправлена ошибка, не учитывалось то что возвращает функция до записи поля
05.05.2012 - исправлена ситуация, когда первичный ключ таблиц должен получаться из параметра get_interface_input 
02.08.2010 исправлена обработка параметра get_interface_input в области доп. поля
28.7.2010 добработано вызов глобальных функций для получения/записи/удаления строк интерфейса
21.12.2009 убраны функции серии ereg - для перехода в PHP6
17.12.09 Введена обработка ошибок по исключениям, ошибки теперь обрабатывает обработчик Exception, все локализовано теперь
17.12.09 обработчики операций (стандарнтых) можно переназначать, можно назначить действия на определенные имена элементов на форме браузера
Убраны передачи по ссылке в методы и функции
Наведен порядок с ошибками типа Notice

*/

public $error_row=[];//список бракованых идентификаторов таблицы, поля не прошли проверку! (целые строки)
public $error_item=[];//индивидуальные бракованные элементы
public $interface_name;//имя интерфейса для работы
public $design_tables_text_interfase=[];//хранит текстовые надписи для данного языка
public $line_table_obj;//хранит эземпляр объекта который генерирует структуру на низком уровне
public $struct0;//струтура интерфейса из таблицы
public $struct1;
public $struct2;
public $pole_dop=[];//данные из подолнительных полей
public $pole__id;//уникальный ключ выводимой таблицы
public $del_record;//строка SQL для удаления записи (защищено, т.к. SQL правится)
public $tab_name;//имя таблицы, в данном интерфейсе
public $dop_sql=[];//массив для генерации выпадающих списков

public $result_sql;//хранит результат выборки SQL (после всех преобразований в интерфейсе), возможно это потребуется в обработ.функциях
public $col_function_array_rez=[];//результат работы внутренних функций, все в виде ассоциативного массива

public $error_code=[];//код ошибки входных данных, ключи массива - имена полей (POST)
public $error_message=[];//текстовые сообщения об ошибках
public $error_form_item=[];//данные об ошибках из объекта form_item
private $value_for_error=[];//сложный массив для выдачи статуса объекта, какие колоки и какие строки были обработаны
private $global_error_code=[];//коды ошибок вообще данного интерфейса, не связано со строками
private $global_error_message=[];//тексты ошибок вообще данного интерфейса, не связано со строками
public $get_interface_input;//параметры которые передаются в интерфейс GET запросом (для поля 49)
public $cap=[];//заголовки в колонках

//имена кнопок на форме по которым совершаются остандартные операции
public $button_save_name='save';//имя кнопки в форме по которой записывается запись
public $button_del_name='del';//имя кнопки в форме по которой удаляется запись
public $button_save_all_name='_save_all_';//имя кнопки в форме по которой сохраняется все
public $button_delete_selected_name='delete_selected_';//имя кнопки в форме по которой удаляются выбранные записи
public $button_optimize_table_name='_optimize_table_';//имя кнопки в форме по которой производится оптимизация

//имена функций обработчиков стандартных действий (нажатий кнопок на форме)
public $function_save_field_name;//имя функции записи одной записи, если не пусто, то вызывается эта функция, иначе встроеная
public $function_del_field_name;//имя функции обработчика удаления записи если не пусто, то вызывается эта функция, иначе встроеная

public $form_item;
public $view;
public $connection;
public $cache;
public $config;
public $container;
public $EventManager;


function __construct($container,$view)
{
	
	
	//глобальные параметры
	$this->value_for_error['column_name']=[]; //имена колонок которые были обработаны
	$this->value_for_error['row_item']=[]; // ID строк которые были обработаны
	$this->get_interface_input='';
	$this->cap=[];
	Simba::$connection=$container->get('ADO\Connection');
	$this->connection=Simba::$connection;
	$this->view=$view;
	$this->cache=$container->get('DefaultSystemCache');
	$this->config=$container->get('Config');
	simba::setConfig($this->config);
	simba::setContainer($container);
	$this->container=$container;
	$SharedEventManager=$container->get('SharedEventManager');
	$this->EventManager=new EventManager($SharedEventManager);
	$this->EventManager->addIdentifiers(["simba.admin"]);
	$this->form_item=new form_item($view,$this->config);
	$this->line_table_obj=new tab_admin($view,$this->config);
}//конец конструктора



public function get_interface_status()
{//получить массив статус-формы
return array(
			'error_form_item'=>$this->error_form_item, //ошибки из объекта form_item
			'error_code'=>$this->error_code, //коды ошибок
			'error_message'=>$this->error_message, //текстовое сообщение об ошибке, берется из конструктора!!!!!! все склеивается через <br>
			'column_name'=>array_unique($this->value_for_error['column_name']), //имена колонок которые были обработаны
			'row_item'=>array_unique($this->value_for_error['row_item']), // ID строк которые были обработаны
			'global_error_code'=>$this->global_error_code, //общие ошибки интерфейса
			'global_error_message'=>$this->global_error_message //общие сообщения об ошибках
			);
}


private function set_error($error_code=0,$message='')
{//для внутренних целей, уснанавливает код ошибки и сообщение
/*
генерирует текстовое сообщени об ошибке, из языкового файла
$mess_cod код сообщения
$message - локализованое сообщение
*/

$this->global_error_message[]=$message;
$this->global_error_code[]=$error_code;
}



function save_field($id)
{

//проверим код формы и убедимся что это не подделка
if($_SESSION['io_line_interface'][$this->interface_name]!=$_POST['cod_form'] ) 
	{throw new Exception("Не верная подпись формы");//неверная подпись формы
	return false;
	}

//получить имена переменных
if ($id) $this->struct2=simba::queryAllRecords ('select validator,properties,functions_befo,functions_after,pole_name,col_name,pole_type,pole_global_const,caption_style from design_tables where table_type=0 and row_type=3 and interface_name="'.$this->interface_name.'" and pole_name !="" and col_name REGEXP "^[a-zA-Z]"');
	else $this->struct2=simba::queryAllRecords ('select validator,properties,functions_befo,functions_after,pole_name,col_name,pole_type,pole_global_const,caption_style from design_tables where table_type=0 and row_type=2 and interface_name="'.$this->interface_name.'" and pole_name !="" and col_name REGEXP "^[a-zA-Z]"');
//сортируем по порядку столбцов
$count=simba::numRows();
$flag_error=false;//флаг ошибки, если истина - ошибка, т.е. вся строка брак

//добавлено удаление кеша по тегам
if ($this->struct0['validator'] && $this->struct0['sort_item_flag'])
	{
				$tags=explode(",",$this->struct0['validator']);
				$this->cache->removeItems($tags);//ключи
				$this->cache->clearByTags($tags,true);//теги
    }



//echo "<pre>";print_r($this->struct0);
$i=0;

while ($i<$count) //for ($i=0;;$i++)
	{$c_=explode (",",$this->struct2['pole_global_const'][$i]);
		$validator=unserialize ($this->struct2['validator'][$i]);// print_r($validator);
		for ($p=0;$p<count($c_);$p++) {$const[$p]=simba::get_const($c_[$p]);}
		if (isset($_POST[$this->struct2['pole_name'][$i]][$id])) $infa=$_POST[$this->struct2['pole_name'][$i]][$id];
			else $infa=NULL;
		//проверим, еуказана ли функциЯ которая вызовется до записи поля
			if ($this->struct2['functions_befo'][$i]>'') 
					{
						$fn=$this->struct2['functions_befo'][$i];
						$fn=new $fn;
						$infa=$fn(
									$this,
									$infa,
									$this->struct2,
									$i,
									$this->pole_dop,
									$this->tab_name,
									$this->pole__id,
									$const,
									$id,
									1
								);
						
						
					}

		//установим переменные, для использования в XML-описателе, там есть код PHP для обработки данных из конкретного поля
		$col_name=$this->struct2['pole_name'][$i];// имя колонки
		$row_item=$id;//номер-идентификатор строки по которой производится операция


$infa=$this->form_item->save_form_item($row_item,
											$this->struct2['pole_type'][$i],
											$this->tab_name,
											$this->struct2['pole_name'][$i],
											$const,
											$infa,
											unserialize($this->struct2['properties'][$i])
											);
		$this->error_form_item[$col_name][$row_item]=$this->form_item->get_status($row_item);//получить по имени элмента формы
		$this->value_for_error['column_name'][]=$col_name;//сохраним имена колонок которые были обработана
		$this->value_for_error['row_item'][]=$row_item;//аналогично для обпределения какие строки обработаны
		
		// проверки поля вначале внутренними силами
		if ($this->error_form_item[$col_name][$row_item]['code']>0) $flag_error=true;//код был больше 0, значит ошибка

		//для специальных полей другая обработка!
		if (preg_match('/pole_dop([0-9]?)/i',$this->struct2['pole_name'][$i],$c) || $this->struct2['pole_name'][$i]=='get_interface_input')
			{//обработка дополнительного поля
			if (preg_match('/pole_dop([0-9]?)/i',$this->struct2['pole_name'][$i],$c)) 
				{//print_r($col_name);
				if (!isset($c[1]) || $c[1]=='') {throw new Exception("Ошибка в доп. поле");return false;}
				if (isset($this->pole_dop[$c[1]]) && $this->pole_dop[$c[1]]) $tab_rec[$this->struct2['col_name'][$i]]=$this->pole_dop[$c[1]];
							else {
							//если pole_dop пустое значение, тогда восстановить старое значение из таблицы, если оно там ввобще есть
								$n=simba::queryOneRecord('select '.$this->struct2['col_name'][$i].' from '.$this->tab_name.' where '.$this->pole__id.'="'.$id.'"');
								$tab_rec[$this->struct2['col_name'][$i]]=$n[$this->struct2['col_name'][$i]];
								}
				}
			//обработка поля внешных данных get_interface_input
			if ($this->struct2['pole_name'][$i]=='get_interface_input') 
			
					{//если первичный ключ берется из get_interface_input то его нужно обработать
						$tab_rec[$this->struct2['col_name'][$i]]=$this->get_interface_input;
						if ($this->struct2['col_name'][$i]==$this->pole__id) $id=$this->get_interface_input;
					
					}
			
	
			
			//print_r($tab_rec);
			}
			else
				{
			//проверим наличие функции обработки, если она указана, применим ее
			if ($this->struct2['functions_after'][$i]>'') 
					{//получить имя функции из таблицы
						$fn=$this->struct2['functions_after'][$i];
						$fn=new $fn;
						$infa=$fn(
								$this,
								$infa,
								$this->struct2,
								$i,
								$this->pole_dop,
								$this->tab_name,
								$this->pole__id,
								$const,
								$id,
								2
							); 

					}
				//проверим на псевдоним, если это так, игнорируем добавление в запрос записи данных!
				//if (!in_array(,$alt_name)) 
				//if ($this->struct2['pole_name'][$i]!='get_interface_input') 
				$tab_rec[$this->struct2['col_name'][$i]]=$infa;
				}
	//проверим $flag_error, если истина, тогда отменить все операции по данной строке, т.е. что-то типа откатить транзакцию, с удалением файлов! если они закачивались
	$i++;//след поле (колонка)
	}//конец while
if (!$flag_error) 
	{if (empty($tab_rec[$this->pole__id])) {$tab_rec[$this->pole__id]=$id;}
	//проверим если ли обработчик записи, если да, вызываем эту функцию и передаем туда все
	if ($this->struct0['properties']>'') 
		{
			$fn=$this->struct0['properties'];
						$fn=new $fn;
						$fn(
							$this, //данный объект со всеми его устновками
							$tab_rec, // массив структура пригодная для записи в базу данных
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							NULL,
							-2
							);
		}
		else //нет обработчика, записываем по умолчанию 
			{//если были спевдонимы, их нужно удалить из массива пере записью, на всякий случай
			$alt_name=explode(',',$this->struct0['col_name']);//получить список псевдонимов, если они есть
			foreach ($alt_name as $v) unset($tab_rec[$v]);
			//simba::replaceRecord ($tab_rec,$this->tab_name);//print_r($tab_rec);
					// переделано на RS
			$rs=new RecordSet();
			$rs->CursorType = adOpenKeyset;
			$rs->open("select * from ".$this->tab_name,$this->connection); //считаем данные
			if ($id)
				{
					//обновление данных
					$rs->Find($this->pole__id."='$id'",0,adSearchForward);
					foreach ($tab_rec as $field=>$value)
						$rs->Fields->Item[$field]->Value=$value;
					$rs->Update();
				}
			else
				{
					//добавление новой записи
					$rs->AddNew();
					foreach ($tab_rec as $field=>$value)
						$rs->Fields->Item[$field]->Value=(string)$value;
					$rs->Update();
					
				}
			
			}
	}
}

function save_all()
{
	//вгачале проверим подпись формы
if(isset($_POST['cod_form']) && isset($_SESSION['io_line_interface'][$this->interface_name]) && $_SESSION['io_line_interface'][$this->interface_name]==$_POST['cod_form'] ) 
		{
		$id=[];
		if ($_POST['global_action_id_array']>'') $id=explode(',',$_POST['global_action_id_array']); 
		for ($i=0;$i<count($id);$i++) $this->save_field($id[$i]);
		}
		else {throw new Exception("Не верная подпись формы");//неверная подпись формы
			}

}


function delete_field($id)
{

//проверим код формы и убедимся что это не подделка
if($_SESSION['io_line_interface'][$this->interface_name]!=$_POST['cod_form'] ) 
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
//получить структуру и проанализировать, если там поле закачки файлов, если есть, тогда удалить и файл, если это разрешено
$this->struct2=simba::queryAllRecords('select * from design_tables where table_type=0 and interface_name="'.$this->interface_name.'" and row_type=3');
//пробежимся по всме полям данной таблицы, и выполним все функции и удалим все файлы если это разрешено)
for ($ii=0;$ii<count($this->struct2['col_name']);$ii++)
	{$pole_const=$this->struct2['pole_global_const'][$ii];
	$const=simba::get_const(explode (',',$pole_const),true);//константы переданные для вывода первой строки

	if ($this->struct2['functions_befo_del'][$ii]>'') 
		{//получить имя функции из таблицы
		$fn=$this->struct2['functions_befo_del'][$ii];
		$fn=new $fn;

		$fn($this,
													'',
													$this->struct2,
													$ii,
													$this->pole_dop,
													$this->tab_name,
													$this->pole__id,
													$const,
													$id,
													3);
		}
	
	$col_name=$this->struct2['col_name'][$ii];//echo '<br>'.$const[0];
	//проверим на псевданим это поле, если нет, тогда вызываем описатель
	$_alias=explode(",",$this->struct0["col_name"]);
	
	 //УДАЛЕН ПРОПУСК ОБАРБОТКИ УДАЛЕНИЯ ДЛЯ ПСЕВДОНИМОВ, 15-10-17
	if (!in_array($this->struct2["pole_name"][$ii],$_alias) || true)
		{
		//непосредственно исполнить код в описателе phpDel
		$this->form_item->del_form_item($id,$this->struct2['pole_type'][$ii],$this->tab_name,$col_name,$const,unserialize($this->struct2['properties'][$ii]));//исполним код PHP, которые есть в описателе
		}
	}

if (!$this->del_record && !$this->struct0['functions_befo_del']) {throw new Exception("Нет обработчика удаления записей");;return false;}

if ($this->del_record) 
		{//print("\$id = \"$this->pole__id\";");
		$sql=$this->del_record;
		$sql=str_replace('$'.$this->pole__id,$id,$sql);
		$sql= preg_replace ("/\"/",'\\\"',$sql);
		eval("\$sql = \"$sql\";");
		simba::query($sql);
		} 
if ($this->struct0['functions_befo_del']) 
		{
			$fn=$this->struct0['functions_befo_del'];
			$fn=new $fn;

		$fn(
													  $this,
													  $id,
  												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												-3

													  );
		}

}

public function _create_interface($interface_name,$flag_out_form=true,View $view=NULL)
{
$this->_create_interface($interface_name,$flag_out_form,$view) ;//стандартный обработчик
}



public function create_interface($interface_name,$flag_out_form=true,View $view=NULL)
{
	$this->line_table_obj->view=$view;
//$flag_out_form - если ложь, тэг формы не выводить
//смотрим внешние  переметры в этот интерфейс
if (isset($_GET['get_interface_input'])) $this->get_interface_input=unserialize(base64_decode($_GET['get_interface_input']));
/*
можно использовать в запросах SQL, в виде $get_interface_input (пока это единичный вариант!!!!!!!!!!!!!!!!!!!)
*/

$this->line_table_obj->flag_out_form=$flag_out_form;


$this->interface_name=$interface_name; //имя интерфейса
//по идентификатору полуячить имя таблицы с которой работаем
$this->struct2=simba::queryOneRecord ('select table_name,functions_befo_out from design_tables where table_type=0 and interface_name="'.$this->interface_name.'"');



$this->tab_name=$this->struct2['table_name'];
if (!$this->tab_name && !$this->struct2['functions_befo_out']) {throw new Exception(__CLASS__,3,[]);}
//загрузим текстовый интерфейс для выбранной таблицы из ьаблицы с текстами
$c=simba::queryAllRecords("select item_name,text from design_tables_text_interfase where 
							table_type=0 and interface_name='".$this->interface_name."'");

for ($i=0;$i<simba::numRows();$i++)
	$this->design_tables_text_interfase[$c['item_name'][$i]]=$c['text'][$i];

//провернка на уникальное поле
$this->struct2=simba::queryOneRecord ('select pole_name,default_sql from design_tables where table_type=0 and row_type=0 and interface_name="'.$this->interface_name.'"');
if (!$this->struct2['pole_name']) {throw new Exception(__CLASS__,4,array($this->interface_name));return false;}
$this->pole__id=$this->struct2['pole_name'];//это уникальный идентификатор таблицы
$this->del_record=str_replace('$pole_dop','$this->pole_dop',$this->struct2['default_sql']);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN

$const=[];
//проверим количество дополнительных полей и преобразуем их в массив
$this->struct2=simba::queryAllRecords ('select properties,pole_name,col_name,pole_type,pole_global_const from design_tables 
			where table_type=0 and row_type=1 and interface_name="'.$this->interface_name.'" order by col_por,id');
$c='pole_dop';//префикс

//проьбежимся по всем переменным и преобразуем в массив
if (isset($this->struct2['pole_type']))
	for ($i=0;$i<count($this->struct2['pole_type']);$i++)
		{if ( 1==1) 
			{//$this->pole_dop[$i]=$_POST[$c.$i]; 
			$c_=explode (",",$this->struct2['pole_global_const'][$i]);
			for ($p=0;$p<count($c_);$p++) $const[$p]=simba::get_const($c_[$p]);
			$this->pole_dop[$i]=$this->form_item->save_form_item(
																0,
																$this->struct2['pole_type'][$i],
																$this->tab_name,
																$this->struct2['pole_name'][$i],
																$const,
															(isset($_POST[$c.$i])) ? $_POST[$c.$i]:NULL,
																unserialize($this->struct2['properties'][$i])
																);
			}
		else {$this->pole_dop[$i]=0;}
		$a=$this->pole_dop[$i];
		if ($this->isSerialized($a))
			{//костыли для сериализованный строк
				$a=addslashes($a);
				eval("\$this->pole_dop$i = \"$a\";");
				$pn="pole_dop$i";
				$this->{$pn}=addslashes($this->{$pn});
				simba::$sql_delim="@@@@@@@@@";
			}
			else
				{
					eval("\$this->pole_dop$i = \"$a\";");
				}
		}

//пролучить общие настройки
$this->struct0=simba::queryOneRecord('select * from design_tables where table_type=0 and interface_name="'.$this->interface_name.'" and row_type=0');

//если у нас вывод в виде формы, тогда смотрим нужно ли выводить кнопки создать запись и переходы по записям (это хранится в колонке value)
$a=unserialize($this->struct0['value']);
$this->line_table_obj->button_create_new_item_flag=$a['form_elements_new_record'];
$this->line_table_obj->buttons_jmp_flag=$a['form_elements_jmp_record'];



//запись
//массовые операции global_action_id_array- список активных идентификаторов таблицы, т.е. те, которые выведены на экран
if (isset($_POST[$this->button_save_all_name]))
	{//массовая запись
	$this->save_all() ;
	}



if (isset($_POST[$this->button_delete_selected_name]))
	{//массовое удаление
	$id=[];
	if ($_POST['global_action_id_array']>'') {$id=explode(',',$_POST['global_action_id_array']);}
	if (is_array($_POST['_select_item']))
		{
			foreach ($_POST['_select_item'] as $k=>$v)
				{
					if ($v>0) {$this->delete_field($id[$k]);}
				}
		}
	}

if (isset($_POST[$this->button_optimize_table_name]))
	{//оптимизация
	simba::query("optimize table $this->tab_name");
	}

//запись в базу
@$s=array_keys($_POST[$this->button_save_name]);//это идентификатор строки когда нажали кнопку сохранить
@$d=array_keys($_POST[$this->button_del_name]);//это идентификатор строки когда нажали кнопку сохранить



//удаление
if (is_array($d)) 
		{
		if ($this->function_del_field_name) call_user_func($this->function_del_field_name,$d[0],$this);//нестандартный обработчик записи
			else  {
				$this->delete_field($d[0]);}//стандартный обработчик
		}

if (is_array($s))
	{//сохранение/добваление
	if ($this->function_save_field_name) call_user_func($this->function_save_field_name,$s[0],$this);//нестандартный обработчик записи
		else  {
				$this->save_field($s[0]);}//стандартный обработчик
	}


//общий заголовок (если языкового файла нет, тогда берем из базы как есть)

//массив 0/1 для каждой из кнопок 0-нет.1-да
if (preg_match("/1/",$this->struct0['pole_prop'])) 
	$this->line_table_obj->global_action=explode(',',$this->struct0['pole_prop']); 
		else $this->line_table_obj->global_action=[];

//общий заголовок таблицы
@$this->line_table_obj->caption=$this->design_tables_text_interfase['caption0'];

//форма ввода
$this->line_table_obj->form_input_type=$this->struct0['col_por'];
if($this->line_table_obj->form_input_type==2) $this->line_table_obj->form_input_type--;//для 3-го типа варианта ввода!!!!
//$nn=simba::get_const($this->struct0['pole_global_const']);//получить константу по ее идентификатору, это есть флаг, выводить постранично таблицу или нет
$this->line_table_obj->row_page_flag=$this->struct0['pole_global_const'];//получить кол-во строк вывода



$this->create_dop_filelds();

//====================================общее===
//сама выборка для заполения таблицы
//получим имена колонок, что бы потом по индексу номера колонки определить что сортировать
$struct3=simba::queryAllRecords ('select col_name from design_tables where table_type=0 and row_type=3 and interface_name="'.$this->interface_name.'"  and pole_type>0 ORDER BY `col_por` ASC');
$sql=$this->struct0['pole_spisok_sql'];
$sql= preg_replace ("/\"/",'\\\"',$sql);
$sql=str_replace('$pole_dop','$this->pole_dop',$sql);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
$sql=str_replace('$get_interface_input','$this->get_interface_input',$sql);//поправим для внешних данных
eval("\$sql = \"$sql\";");

//разбираемся с сортировками, если они есть и щелкнули на заголовок таблицы
	//имя поля для сортировки
	$_sort='';
	if (isset($_POST['sort_item']) && isset($_POST['sort_item']) && isset($struct3['col_name'][$_POST['sort_item']])) $_sort=' order by '.$struct3['col_name'][$_POST['sort_item']];
	//направление сортировки
	if (isset($_POST['sort_type'])) 
		switch ($_POST['sort_type'])
			{case 0:$_sort='';break;
			case 1:$_sort.=' asc';break;
			case -1:$_sort.=' desc';break;
			};
$sql.=$_sort;//приклеить к запросу для сортировки


//==================================помечаем ошибочнуый элемент
$this->error_item1=[];
//проверим что является источником данных, если функция, тогда SQL не делаем
if ($this->struct0['functions_befo_out']) 
		{
		$fn=$this->struct0['functions_befo_out'];
		$fn=new $fn;

			$arr=$fn(
												$this,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												NULL,
												-1
												);//print_r($arr);
		
		@$count=count($arr[$this->pole__id]);
		}
	else 
		{
			
			try {
				
				$arr=simba::queryAllRecords($sql);
			}
			catch (Exception $e){echo "<b>Ошибка в SQL запросе: </b>";\Zend\Debug\Debug::dump($sql);}
			$count=simba::numRows();
		}

$this->result_sql=$arr;


//если ввод в виде формы, то определить откуда брать список, если пусто, то просто нумерация
if ($this->struct0['pole_type'] && isset($arr[$this->struct0['pole_type']])) $this->line_table_obj->form_input_array=$arr[$this->struct0['pole_type']];

if ($this->line_table_obj->row_page_flag>0 && !$this->struct0['functions_befo_out']) 
							{//$this->line_table_obj->row_page_flag - кол-во строк которое будет выводиться
							//$count всего строк (т.е. без учета limit)
								if ($this->line_table_obj->row_page_flag>$count) $this->line_table_obj->row_page_flag=$count+1;
								if (isset($_POST['row_page'])) $n=$_POST['row_page']; else $n=0;
								if (isset($_POST['pagen']) && ($n+$this->line_table_obj->row_page_flag<$count)) $n+=$this->line_table_obj->row_page_flag;
								if (isset($_POST['pagep']) && ($n-$this->line_table_obj->row_page_flag>=0)) $n-=$this->line_table_obj->row_page_flag;
								//начало или конец  только для ВЫВОДА В ВИДЕ ФОРМЫ
								if (isset($_POST['page_start']) && $_POST['page_start'])  $n=0;
								if (isset($_POST['page_end']) && $_POST['page_end'])  $n=$count-$this->line_table_obj->row_page_flag+1;
								$sql.=' limit '.$n.','.$this->line_table_obj->row_page_flag;
								$this->line_table_obj->row_page=$n;
								$n++;
								$nnn=$n+$this->line_table_obj->row_page_flag-1;
								$this->line_table_obj->row_page_text="Записи с: $n до $nnn, Всего: $count";
								$this->line_table_obj->row_page_count=ceil($count/$this->line_table_obj->row_page_flag);//передать общее кол-во страниц
								$arr=simba::queryAllRecords($sql);
								}

if (isset ($arr[$this->pole__id]) &&is_array($arr[$this->pole__id]) && isset($arr[$this->pole__id])) 
			{$this->line_table_obj->global_action_id_array=implode(',',$arr[$this->pole__id]);//это массив идентификаторов, порядок совпадает с порядком строк в выводимой стаблице! нужно для массовых операций
			
			}
//первая строка таблицы
$this->struct2=simba::queryAllRecords ('select pole_name from design_tables where table_type=0 and row_type=2 and interface_name="'.$this->interface_name.'" and pole_type=0 ');//сортируем по порядку столбцов
//проверим правильность имени дополнительного поля

if (isset($this->struct2['pole_name']) && $this->struct2['pole_name']) 
	foreach ($this->struct2['pole_name'] as $nnn) 
		if ($nnn>'' && $nnn!='get_interface_input') 
			{
				preg_match ('/pole_dop([0-9]?)/i',$nnn,$c) ;
			if (!isset($c[1]) || $c[1]=='')  throw new Exception(__CLASS__,5,[]);
			}

$count=$this->create_start_end_items(2);//генерировать первую строку, заодно получить кол-во колонок


//=============================основное модержимое таблицы

if (isset($arr[$this->pole__id]) )
	{$struct3=simba::queryAllRecords ('select pole_name,sort_item_flag from design_tables where table_type=0 and row_type=3 and interface_name="'.$this->interface_name.'" and pole_type=0 ');//сортируем по порядку столбцов
	//проверим правильность имени дополнительного поля
	if ( isset($struct3['pole_name'])) 
		foreach ($struct3['pole_name'] as $nnn) 
			if ($nnn>'' && $nnn!='get_interface_input') 
				{
					preg_match('/pole_dop([0-9]?)/',$nnn,$c) ;
					if ($c[1]=='') {
                            //\Zend\Debug\Debug::dump($struct3); exit;
                        throw new \Exception('Ошибка в строке '.__LINE__);
                    }
				}
	for ($i=0;$i<$count;$i++)
		{//все оставшиеся строки таблицы порядок такойже как и для первой строки!!!!!!!!!!
		$struct3=simba::queryOneRecord ('select * from design_tables where table_type=0 and row_type=3 and col_name="'.$this->struct2['col_name'][$i].'" and interface_name="'.$this->interface_name.'" ');
		//обрабатываем те функции которые применяются ко всей колонке (все они указаны в конструкторе)
		//на выходе получаем массив ключи которого равны именам функций
		$col_function_array=unserialize($struct3['col_function_array']);//получить функции которые надо применить ко всей колонке
		if (is_array($col_function_array))
				{
				foreach ($col_function_array as $col_function)
					{
					switch ($col_function)
							{
							case 'sum': $this->col_function_array_rez[$col_function]=@array_sum($arr[$struct3['col_name']]);break;
							case 'count': $this->col_function_array_rez[$col_function]=count($arr[$struct3['col_name']]); break;
							case 'min': $a_=$arr[$struct3['col_name']];sort($a_);$this->col_function_array_rez[$col_function]=$a_[0];break;
							case 'max': $a_=$arr[$struct3['col_name']];rsort($a_);$this->col_function_array_rez[$col_function]=$a_[0]; break;
							
							}
					}
				}
		
		$this->dop_sql=[];
			if ($struct3['pole_spisok_sql']) 
				{$sql__=$struct3['pole_spisok_sql'];
				$sql__= preg_replace ("/\"/",'\\\"',$sql__);
				$sql__=str_replace('$pole_dop','$this->pole_dop',$sql__);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
				$sql__=str_replace('$get_interface_input','$this->get_interface_input',$sql__);//поправим для внешних данных


				eval("\$sql__ = \"$sql__\";");
				$this->dop_sql=simba::queryAllRecords($sql__);
				}

			$pole_name=explode(',',$struct3['pole_name']);//получить имена полей
			
			$nnn=$this->line_table_obj->create_array_names($arr[$this->pole__id],$pole_name[0],(isset($pole_name[1]))?$pole_name[1]:NULL);
			
			
			if (!isset($arr[$struct3['col_name']])) {$arr[$struct3['col_name']]=[];}
			
			
			$this->line_table_obj->sort_cols_flag[$i]=$struct3['sort_item_flag'];//флаги сортировки выбранных колонок
			//это константа передаываемая пдля вывода
			$const=simba::get_const(explode (',',$struct3['pole_global_const']),true);//получить константы в виде массива
			
			//проверим наличие функции для вызова перед выводом всей колонки данного поля
			if ($struct3['functions_befo_out']>'') 
							{//получить имя функции из таблицы
							$fn=$struct3['functions_befo_out'];
							$fn=new $fn;

							$arr[$struct3['col_name']]=$fn(
																			$this,
																			$arr[$struct3['col_name']],
																			$struct3,
																			$struct3['pole_type'],
																			@$this->pole_dop,
																			@$this->tab_name,
																			$this->pole__id,
																			$const,
																			$arr[$this->pole__id],
																			1);
							}

			$this->line_table_obj->row_def_type(
												$i,
												$struct3['pole_type'],
												$nnn,
												$struct3['pole_prop'],//$style,
												@$this->dop_sql['name'],
												@$this->dop_sql['id'],
												@implode (',',$const),
												@$this->dop_sql['group']['name'],
												$struct3['value'],
												unserialize($struct3['properties']),
												@$this->design_tables_text_interfase['values_message_'.$struct3['col_name'].'3']
												);//print_r(unserialize($struct3['properties']));
												
			//проверить, если это кнопки, тогда внести фиктивный массив идентификаторов, что бы было не пусто!
			if (preg_match ("/^[1-9]/",$struct3['col_name'])) $this->line_table_obj->row_all_value($i,$arr[$this->pole__id],[]);//$arr['__error_flag__']); 
					else  {
								@$this->line_table_obj->row_all_value($i,$arr[$struct3['col_name']],$this->error_item1[$struct3['col_name']]);//это знаяение по имени колонки
							
							}
			
		}
}
$this->create_start_end_items(4);//генерировать последнюю строку, она аналогично формируется как и первая

//print_r($this->col_function_array_rez);
$this->line_table_obj->col_name=$this->cap;//это заголовки колонок из языкового файла
$_SESSION['io_line_interface'][$this->interface_name]=md5(microtime());//уникальный код формы
$this->line_table_obj->cod_form=$_SESSION['io_line_interface'][$this->interface_name];

}

public function print_interface()
{
echo $this->line_table_obj->tab_print();
}

public function get_interface()
{
return $this->line_table_obj->tab_fetch();
}



//*************************************************** Внутренние функции, служебные



private function create_start_end_items($row_type=2)
{
/*
$row_type-номер типа строки, 2- первая, 4-проследняя
функция фозвращает кол-во колонок в таблице, генерирует заголовки и все это передает в спец.объект который генерирует таблицу
*/

$this->struct2=simba::queryAllRecords ('select * from design_tables where table_type=0 and row_type='.$row_type.' and interface_name="'.$this->interface_name.'" and pole_type>0 ORDER BY `col_por` ASC');//сортируем по порядку столбцов
$count=simba::numRows();
for ($i=0;$i<$count;$i++)
{
	@$this->cap[$i]=$this->design_tables_text_interfase['caption_col_'.$this->struct2['col_name'][$i]];
	$this->dop_sql=[];
	if ($this->struct2['pole_spisok_sql'][$i]) 
		{$sql_=$this->struct2['pole_spisok_sql'][$i];
		$sql_= preg_replace ("/\"/",'\\\"',$sql_);
		$sql_=str_replace('$pole_dop','$this->pole_dop',$sql_);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN

		eval("\$sql_ = \"$sql_\";");
		//$this->dop_sql=simba::spec_parse_sql($sql_);//парсинг сложных запросов, джля сложных списков
		$this->dop_sql=simba::queryAllRecords($sql_);
		//echo simba::$errorMessage;
		}//возможная выборка, если список
	
	//$style=simba::get_style_class_ fromtable(explode (',',$this->struct2['pole_style'][$i]), explode (',',$this->struct2['pole_prop'][$i]));//массив стилей полей (если двойное тогда 2 элемента
	$pole_name=explode(',',$this->struct2['pole_name'][$i]);//получить имена полей
	//формируем имена элементов
	if ($row_type==2) $nnn=$pole_name[0].'[0]'; 
		else $nnn=$pole_name[0].'[end]'; 
	$const=simba::get_const(explode (',',$this->struct2['pole_global_const'][$i]),true);//константы переданные для вывода первой строки

	if ($this->struct2['functions_befo_out'][$i]>'') 
			{//получить имя функции из таблицы
			$fn=$this->struct2['functions_befo_out'][$i];//echo $f_[1];
			$fn=new $fn;

			$fn($this,
															NULL,//$arr[$this->struct2['col_name'][$i]],
															$this->struct2,
															$i,
															$this->pole_dop,
															$this->tab_name,
															$this->pole__id,
															$const,
															0,
															1);
			}
	//настройка вида/типа/значения
	if ($row_type==2) {$this->line_table_obj->row_start_type($i,
										$this->struct2['pole_type'][$i],
										$nnn,
										$this->struct2['pole_prop'][$i],//$style,
										(isset($this->dop_sql['name']))?$this->dop_sql['name']:NULL,
										(isset($this->dop_sql['id'])) ? $this->dop_sql['id']:NULL,
										@implode(',',$const),
										(isset($this->dop_sql['group']['name']))?$this->dop_sql['group']['name']:NULL,
										(isset($this->struct2['value'][$i]))?$this->struct2['value'][$i]:NULL,
										unserialize($this->struct2['properties'][$i]),
										(isset($this->design_tables_text_interfase['values_message_'.$this->struct2['col_name'][$i].$row_type]))?$this->design_tables_text_interfase['values_message_'.$this->struct2['col_name'][$i].$row_type]:NULL
										);//echo($this->struct2['properties'][$i]);
	}
				
					else $this->line_table_obj->row_end_type($i,
										$this->struct2['pole_type'][$i],
										$nnn,
										$style,
										$this->dop_sql['name'],
										$this->dop_sql['id'],
										@implode(',',$const),
										$this->dop_sql['group']['name'],
										$this->struct2['value'][$i],
										unserialize($this->struct2['properties'][$i]),
										$this->design_tables_text_interfase['values_message_'.$this->struct2['col_name'][$i].$row_type]
										);

	if ($row_type==2) $this->line_table_obj->row_start_value($i,
										(isset($this->error_row[$this->struct2['col_name'][$i]]))?$this->error_row[$this->struct2['col_name'][$i]]:NULL,
										(isset($this->error_item[$this->struct2['col_name'][$i]]))?$this->error_item[$this->struct2['col_name'][$i]]:NULL
										);//это знаяение по имени колонки
					else $this->line_table_obj->row_end_value($i,
										$this->error_row[$this->struct2['col_name'][$i]],
										$this->error_item[$this->struct2['col_name'][$i]]
										);//это знаяение по имени колонки
}
return $count;//возвращает кол-во колонок в таблице, актуально только для первой строки
}

private function create_dop_filelds ($row_type=1)
{
//получить настройки доп поля ввода до основной таблицы
$this->struct1=simba::queryAllRecords ('select * from design_tables where table_type=0 and row_type='.$row_type.' and interface_name="'.$this->interface_name.'" order by col_por');
$dcount=simba::numRows();
for ($jjj=0;$jjj<$dcount;$jjj++)
{
$const_dop_pole=simba::get_const($this->struct1['pole_global_const'][$jjj]);//константы доп аоля если есть
//дополнительное поле до вывода всей таблицы
if ($this->struct1['pole_type'][$jjj]>0) 
	{//если указан тип поля тогда работаем с ним
	$this->dop_sql=[];
	if ($this->struct1['pole_spisok_sql'][$jjj]) 
		{
			$sql__=stripslashes($this->struct1['pole_spisok_sql'][$jjj]);//echo $sql__;
		$sql__= preg_replace ("/\"/",'\\\"',$sql__);
		$sql__=str_replace('$pole_dop','$this->pole_dop',$sql__);//т.к. работаем в объекте, поправим $pole_dopN на $this->pole_dopN
		$sql__=str_replace('$get_interface_input','$this->get_interface_input',$sql__);//поправим для внешних данных
		eval("\$sql__ = \"$sql__\";");//echo $sql__;
		//$this->dop_sql=simba::spec_parse_sql($sql__);
		$this->dop_sql=simba::queryAllRecords($sql__);
		//if (simba::$errorMessage) throw new Exception(__CLASS__,5,array($jjj)); 
		//==================проверим, если значение поля входит в диапозон выборки SQL тогда все хорошо, в противном случае надо это поле обнулить
		//т.к. возможно что это поле зависит от состояния предыдущих полей, т.е. надо присвоить значение по умолчанию
		if (isset($this->dop_sql['id'][0]) && is_array($this->dop_sql['id'][0]))
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
			$sql__=str_replace('"$get_interface_input"','\'$get_interface_input\'',$sql__);
			$sql__=str_replace('$get_interface_input','$this->get_interface_input',$sql__);//поправим для внешних данных
			
			eval("\$sql__ = \"$sql__\";");
			$df=simba::queryOneRecord($sql__);//if (simba::$errorMessage) throw new Exception(__CLASS__,6,array($jjj)) ;
			$this->pole_dop[$jjj]=$df['id'];
			$a=$this->pole_dop[$jjj]; 


			eval("\$this->pole_dop$jjj = \"$a\";");//echo "def=$a; jjj=$jjj<br>";//eval ("echo \$this->pole_dop$jjj;");
			
			} else $a=NULL;
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
												$this->pole__id,
												$const_dop_pole,
												0,
												1);
				}


	@$this->line_table_obj->caption($this->design_tables_text_interfase['caption_dop_'.$jjj],
									"",
									$jjj);

	//$style=simba::get_style_class_ fromtable(explode (',',$this->struct1['pole_style'][$jjj]),explode (',',$this->struct1['pole_prop'][$jjj]));//массив стилей полей (если двойное тогда 2 элемента
	

	$this->line_table_obj->row_dop_type(
								$this->struct1['pole_type'][$jjj],
								'pole_dop'.$jjj,
								$this->struct1['pole_prop'][$jjj],//$style,
								@$this->dop_sql['name'],
								@$this->dop_sql['id'],
								$const_dop_pole,
								(isset($this->dop_sql['group']['name'])) ? $this->dop_sql['group']['name']:'',
								$jjj,$this->struct1['value'][$jjj],
								unserialize($this->struct1['properties'][$jjj]));
	$this->line_table_obj->row_dop_value($this->pole_dop[$jjj],$jjj);
	}
}


}

protected function isSerialized($str) {
    return ($str == serialize(false) || @unserialize($str) !== false);
}

}
?>