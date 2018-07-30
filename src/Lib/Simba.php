<?php
//=================================================
/*
старая версия работы с базой данных, перекочевало из старой версии системы
используется для работы конструторов таблиц и ввода-вывода их
*/

namespace Admin\Lib;
use ADO\Service\RecordSet;

class Simba 
{

	public static $rs;						//объекст RecordSet
	
	public static $flag=true;			//true -  разрешены мультизапросы
	public static $connection;
	public static $config;
	public static $container;


public static function setConfig($config)
{
	self::$config=$config;
}

public static function setContainer($container)
{
	self::$container=$container;
}


//=получимть константу по ее идентификатору таблицы
public static function get_const($sysname,$return_array_flag=false)
{//получение глобальной константы по ее имени в таболице
//$return_array_flag - если false тогда возвращает список через запятую, иначе возвращает в виде массива
//\Zend\Debug\Debug::dump($sysname);

if (is_array($sysname))
	{
		$arr=[];
		foreach ($sysname as $v) 
			{
				$v=str_replace("'",'"',trim($v));
				if ($v) 
					{
						$k='return \Admin\Lib\Simba::$config'.$v.';';
						$v=eval($k);
						if (empty($v)) {echo "<h2>Константа {$v} не определена!</h2>";exit;}
						$arr[]= rtrim($v,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
					}
					else $arr[]="";
			}
	}
	else 
		{
			$sysname=str_replace("'",'"',trim($sysname));
			if ($sysname) 
				{
					$k='return \Admin\Lib\Simba::$config'.$sysname.';';
					$v=eval($k);
					if (empty($v)) {echo "<h2>Константа {$sysname} не определена!</h2>";exit;}
					return rtrim($v,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
				}
				else {return "";}
		}

return $arr;

}




/*--------------------------------------------СТАРОЕ--------------------------------------------------*/


public static $sql_delim=";";

public static function query($queryString)
{
	$RecordsAffected=0;
self::$connection->Execute($queryString,$RecordsAffected,adExecuteNoRecords);	
return true;
}

public static function queryAllRecords($queryString)
{
	$RecordsAffected=0;
	self::$rs=new RecordSet();
	self::$rs->CursorType = adOpenKeyset;
	self::$rs->open($queryString,self::$connection);
	
	 $sqls=explode(self::$sql_delim,$queryString);
	if ( count($sqls)>1) 
		{
			for($i=1;$i<count($sqls);$i++) 
				{
					//echo "<br>{$i} , COUNT= ";
					if (trim($sqls[$i])) self::$rs->NextRecordset($RecordsAffected);
					//echo self::$rs->RecordCount."<br>";
				}
		}
	if (self::$rs->EOF) return NULL;
	return self::$rs->GetRows (adGetRowsArrType);
}



public static function queryOneRecord($queryString)
{
	
	$RecordsAffected=0;
	self::$rs=new RecordSet();
	self::$rs->CursorType = adOpenKeyset;
	self::$rs->open($queryString,self::$connection);
	
	$sqls=explode(self::$sql_delim,$queryString);
	if ( count($sqls)>1) 
		{
			for($i=1;$i<count($sqls);$i++) 
				{
					if (trim($sqls[$i])) self::$rs->NextRecordset($RecordsAffected);
					
				}
		}
if (self::$rs->EOF) return false;
//цикл по колонкам которые имеются
$rez=array();
foreach (self::$rs->DataColumns as $DataColumn_item) 
	{
		$rez[$DataColumn_item->ColumnName]=self::$rs->Fields->Item[$DataColumn_item->ColumnName]->Value;
	}
	if (self::$rs->EOF) return NULL;
	return $rez;
}

//запись информации в базу
public static function replaceRecord ($rec_array,$tablename)
{//$rec_array ключи-это имена полей, значение - то что записываем
    //$tablename имя таблицы в которую пишем 
    $s=NULL;
    $ss=NULL;
    $RecordsAffected=0;
    $lst=array_keys($rec_array); 
    $j=count($lst);
    for ($i=0; $i<$j; $i++)  {
        //работаем с именами полей
        $s.=$lst[$i].","; 
        //теперь значения
        if (is_null($rec_array[$lst[$i]])){
            $ss.='null ,';
        } else {
            $ss.="'".addslashes ($rec_array[$lst[$i]])."',";
        }
    }
        $s=substr($s,0,strlen($s)-1);
        $ss=substr($ss,0,strlen($ss)-1);
        $sql="replace into $tablename ($s) values ($ss)";
        //\Zend\Debug\Debug::dump($sql);
        self::$connection->Execute($sql,$RecordsAffected,adExecuteNoRecords);
	}





public static function numRows()
{
return self::$rs->RecordCount;
}



//костыли для генерации выпадающего списка
public static $null_simvol='';//значение выпадающего списка с пустым полем если выбрано пустое поле, по умоляанию 0

//======================генерирует тэги для вывода списков
public static function getSpValue1 ($sp,$sp_id,$tekid,$group=0)
	{return self::getSpValue ($sp,$sp_id,$tekid,$group,1);
	}

public static function getSpValueMulti($sp,$sp_id,$value)
	{//так как и обычный список, только мульти выбор
	$value=explode(',',$value);
	if (!is_array($value)) $value=array(); $p='';
    for ($j=0;$j<count($sp);$j++)
			{if (in_array ($sp_id[$j],$value)) $p.="<option value=\"".$sp_id[$j]."\" selected>".$sp[$j]."</option>";
									else $p.="<option value=\"".$sp_id[$j]."\">".$sp[$j]."</option>";
		  }
	return preg_replace ("/\n|\r/",'',$p);
	}



public static function getSpValue ($sp,$sp_id,$tekid,$group=0,$flag=0)
//воход: массив текста, массив идентификаторов, тек идентификатор (массивы параллельные)
//$group-массив групп, список формируется по группам: $sp['индекс_группы'][числовые_ключи], $sp_id['индекс_группы'][числовые_ключи], если групп нет, тогджа в массиве убирается ключ "имя_группы"
{//возвращает структуру для выпадающего списка вход cписок  и текущий элементработает  с номерами списка!
if ($flag==0) $spout='<option value=\''.self::$null_simvol.'\'></option>'; else $spout='';
//обработка по группам
if (is_array($group))
	for($k=0;$k<count($group);$k++) $spout.='<optgroup label=\''.$group[$k].'\'>'. self::getSpValue($sp[$k],$sp_id[$k],$tekid,0,1).'</optgroup>';
else {//обработка без групп
for ($i=0; $i<count($sp); $i++)
					{
					if ($tekid==$sp_id[$i] && preg_match("/[a-z]/i",$tekid)==preg_match("/[a-z]/i",$sp_id[$i])) 
						{
							 $spout.="<option value='$sp_id[$i]' selected>$sp[$i]</option>";
						}
							else 
										{$spout.="<option value='$sp_id[$i]'>$sp[$i]</option>";}
					}
	}
return preg_replace ("/\n|\r/",'',$spout);
}





}//конец класса simba



?>