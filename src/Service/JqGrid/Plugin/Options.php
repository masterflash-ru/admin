<?php

namespace Admin\Service\JqGrid\Plugin;


class Options extends AbstractPlugin
{

/*
* передача в сетку списка юзеров и групп (добавление в массив)
* $colModel - массив параметров colModel для данной колонки
*/
public function colModel(array $colModel, array $toolbarData=[])
{
   // $colModel["editoptions"]["users"]=serialize($this->users);
   // $colModel["editoptions"]["groups"]=serialize($this->groups);
    return $colModel;
}

/**
* операция чтения
* $row - вся строка которая передается в сетку
* $index - порядоковый номер строки, начинается с 0
* $value - значение для данной колонки (которую обрабатывает этот плагин) и текущей строки
*/
public function read($value,$index,$row)
{
    //$mode_text=$row[$this->options["owner_user"]].",".$row[$this->options["owner_group"]].",".$value;
    return $value;
}

/**
* обработка записи доступов
* если была ошибка - исключение
* $value - строка 1,2,484 - user,group,permission
*/
public function edit($value,&$postParameters)
{
   // $v=explode(",",$value);
   // $postParameters[$this->options["owner_user"]]=$v[0];
   // $postParameters[$this->options["owner_group"]]=$v[1];
    return $value;
}
    
/**
* обработка записи доступов
* если была ошибка - исключение
* $value - строка 1,2,484 - user,group,permission
*/
public function add($value,&$postParameters)
{
    return $this->edit($value,$postParameters);
}
}
