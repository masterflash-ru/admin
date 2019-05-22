<?php

namespace Admin\Service\JqGrid\Plugin;


class Permissions extends AbstractPlugin
{
    protected $connection;
    protected $users=[];
    protected $groups=[];
    protected $def_options =[
        "owner_user"=>"owner_user",  //имя колонки с владельцем
        "owner_group"=>"owner_group",//имя колонки с группой
        "mode"=>"mode"               //имя колонки с кодом доступа
    ];

    public function __construct($connection) 
    {
		$this->connection=$connection;
        $user_rs=$connection->Execute("select id,concat(ifnull(name,name),' (',login,')') as name from users");
        while (!$user_rs->EOF){
            $this->users[$user_rs->Fields->Item["id"]->Value]=$user_rs->Fields->Item["name"]->Value;
            $user_rs->MoveNext();
        }
        $group_rs=$connection->Execute("select id,name from users_group");
        while (!$group_rs->EOF){
            $this->groups[$group_rs->Fields->Item["id"]->Value]=$group_rs->Fields->Item["name"]->Value;
            $group_rs->MoveNext();
        }

    }

/*
* передача в сетку списка юзеров и групп (добавление в массив)
* $colModel - массив параметров colModel для данной колонки
*/
public function colModel(array $colModel, array $toolbarData=[])
{
    $colModel["editoptions"]["users"]=serialize($this->users);
    $colModel["editoptions"]["groups"]=serialize($this->groups);
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
    $mode_text=$row[$this->options["owner_user"]].",".$row[$this->options["owner_group"]].",".$value;
    return $mode_text;
}

/**
* обработка записи доступов
* если была ошибка - исключение
* $value - строка 1,2,484 - user,group,permission
*/
public function edit($value,&$postParameters)
{
    $v=explode(",",$value);
    $postParameters[$this->options["owner_user"]]=$v[0];
    $postParameters[$this->options["owner_group"]]=$v[1];
    return $v[2];
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
