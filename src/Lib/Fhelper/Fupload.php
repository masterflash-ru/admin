<?php

namespace Admin\Lib\Fhelper;

class Fupload extends Fhelperabstract
{
	
	public function __construct($item_id)
{
		parent::__construct($item_id);
}


public function file_upload($name,$path,$file_enable_extended=array(),$max_file_size=0,$mode=0666,$file_name_add='',$name_add_type=0)
{/*
загрузка ОДНОГО ФАЙЛА на сервер
$name - имя элмента загрузки файла, если у нас много файлов(массив), тогда имя нужно указать как массив array(ID_поля_files=>имя_поля_files)
$path - путь куда загружаем файл(ы)
$mode- код доступа загруженного файла
$file_name_add - строка которая добавляется в конце имени, что бы снихзить вероятность перезаприси существующих файлов
$max_file_size - максимальный допустимый размер файла
$name_add_type 0- остется как есть  1- в имя добавляется $file_name_add, 2 - тогда имя меняется полностью на $prefix, 3-генерирует имя md5(microtime())
$file_enable_extended - массив допустимых типов файлов, если пустой, тогда все можно


возвращает массив ключи
type=> //типы файлов (mime -загловки)
size=> //размеры загр. файлов в байтах
error=> //коды ошибок при загрузке 0-4-код ошибки при загрузке
name=> то под каким именем файл записан на сервере
real_name=> имена файлов которые были загружены (реальное которое указал юзер)

коды ошибок:
0- нет ошибки
1-4 физические ошибки, см. документацию на PHP
5 - недопустимф тип файла
6 - превышен размер в байтах
7 - неверное обращение к данной функции

*/
$path=rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

$max_file_size=(int)$max_file_size;
if(is_array($name))
	{//имя как массив, входные данные $_FILES немного отличаются
		
		if (count($name)>1)
			{//немерное обращение к функции
				return array(
						'error'=>7,
						'type'=>'',
						'size'=>0,
						'name'=>'',
						'real_name'=>''
							);
			}
    $val=reset($name);
    $key=key($name);
    
	$FILE_ITEM_NAME=$_FILES[$val]['name'][$key];
	$FILE_ITEM_TYPE=$_FILES[$val]['type'][$key];
	$FILE_ITEM_SIZE=$_FILES[$val]['size'][$key];
	$FILE_ITEM_ERROR=$_FILES[$val]['error'][$key];
	$FILE_ITEM_TMP_NAME=$_FILES[$val]['tmp_name'][$key];
	}
	else
	{//массива нет, просто имя
	$FILE_ITEM_NAME=$_FILES[$name]['name'];
	$FILE_ITEM_TYPE=$_FILES[$name]['type'];
	$FILE_ITEM_SIZE=$_FILES[$name]['size'];
	$FILE_ITEM_ERROR=$_FILES[$name]['error'];
	$FILE_ITEM_TMP_NAME=$_FILES[$name]['tmp_name'];

	}



if ($FILE_ITEM_NAME) 
		{
		
		//костыли для мультизагрузки в современных браузерах
		//if (!is_array())
		
		
		//добавляем в имя файла если оно указано
		$file_name=$FILE_ITEM_NAME;//имя вайла которое пересылает юзер
		$file_name=preg_replace('/"|\'| |,|&/','_',stripslashes($file_name));//заменим запрещеные символы в имени файла, что бы не допуститиь ошибок
		//проверим на предмет ошибки загрузки
		if($FILE_ITEM_ERROR>0)
					return array(
								'error'=>$FILE_ITEM_ERROR,
								'type'=>$FILE_ITEM_TYPE,
								'size'=>$FILE_ITEM_SIZE,
								'name'=>'',
								'real_name'=>$file_name
								);
		$tt=pathinfo ($file_name);//получаем части имена для анализа
		if (count($file_enable_extended)>0)
			{//указаны типы допустимых расширений
			//преобразуем регистр к единому
			foreach ($file_enable_extended as $k__=>$v__) $file_enable_extended[$k__]=strtolower($file_enable_extended[$k__]);
			
			if (!in_array(strtolower($tt['extension']),$file_enable_extended)) 
					{$this->errors=5;
					return array(
								'error'=>5,
								'type'=>$FILE_ITEM_TYPE,
								'size'=>$FILE_ITEM_SIZE,
								'name'=>'',
								'real_name'=>$file_name
								);
					};//в списке нет загружаем недопустимый файл, выход и ошибка
			}
		$real_name=$file_name;//запомним реальное имя файла
		switch ($name_add_type)
			{
			case 1://в имя добавим строку
					$file_name=str_replace('.'.$tt['extension'],$file_name_add.'.'.$tt['extension'],$file_name);
					break;
			case 2:$file_name=$file_name_add.'.'.$tt['extension'];//все имя - новая строка
					break;
			case 3:$file_name=md5(microtime()).'.'.$tt['extension'];//все имя - новая строка
					break;
			
			}
		//проверим на размер, если он превысит допустимы, ошибка
		if ($max_file_size>0 && $FILE_ITEM_SIZE>$max_file_size)
					return array(//превышен допустимы размер файла в байтах
								'error'=>6,
								'type'=>$FILE_ITEM_TYPE,
								'size'=>$FILE_ITEM_SIZE,
								'name'=>$file_name,
								'real_name'=>$real_name
								);
		//ошибок не обнаружено, загружаем
		move_uploaded_file($FILE_ITEM_TMP_NAME,$path.$file_name);
		chmod ($path.$file_name,$mode);
		return array(//все хорошо с загрузкой
					'error'=>0,
					'type'=>$FILE_ITEM_TYPE,
					'size'=>$FILE_ITEM_SIZE,
					'name'=>$file_name,
					'real_name'=>$real_name
					);
		}



}

	
}

