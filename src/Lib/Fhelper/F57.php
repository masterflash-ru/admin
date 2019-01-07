<?php
/*
вызов другого интерфейса (кнопка/ссылка)
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;
use Admin\Lib\Simba;

class F57 extends Fhelperabstract 
{
	protected $hname="Редактор владельцев и доступов";
	protected $category=100;
	protected $itemcount=1;
    protected static $flag_dialog=true;
    protected static $permissions_text=[
        0=>"Нет доступа",
        1=>"Запуск",
        2=>"Запись",
        3=>"Запись и запуск",
        4=>"Чтение",
        5=>"Чтение и запуск",
        6=>"Чтение и запись",
        7=>"Полный",
    ];


public function __construct($item_id)
{
	parent::__construct($item_id);

}
	
	
	
public function render()
{
    $input1 = new Element\Button($this->name[0]);
    //выделим идентификатор записи
    preg_match("/[0-9a-z]+\[([0-9]+)\][0-9-a-z\-_\[\]]?/ui",$this->name[0],$_id);
	$id=(int)$_id[1];
    //читаем запись 
    $permissions=Simba::queryOneRecord("select 
            owner_user,
            (select name from users where permissions.owner_user=id) as owner_user_name,
            owner_group, 
            (select name from users_group where permissions.owner_group=id) as owner_group_name,
            mode 
                from permissions where id=$id");
    $user=($permissions["owner_user_name"]) ? $permissions["owner_user_name"] : $permissions["owner_user"];
    $group=($permissions["owner_group_name"]) ? $permissions["owner_group_name"] : $permissions["owner_group"];
    $mode_text=$this->permissionToText($permissions["mode"]);
    $mode_oct=decoct($permissions["mode"]);
    
    $input1->setLabel("{$user}:{$group} {$mode_text} ({$mode_oct})");
    
    
    $this->zatr["onclick"]="f57($(this))";
   
    
    $input1->setAttributes($this->zatr);
    $input1->setValue(implode(",",[$permissions["owner_user"],$permissions["owner_group"],$permissions["mode"]]));
    $html=$this->view->FormElement($input1);
    
    if (static::$flag_dialog){
        $select = new Element\Select("p1");
        $select->setValueOptions(static::$permissions_text);
        $p1=$this->view->FormSelect($select);
        
        $select = new Element\Select("p2");
        $select->setValueOptions(static::$permissions_text);
        $p2=$this->view->FormSelect($select);
       
        $select = new Element\Select("p3");
        $select->setValueOptions(static::$permissions_text);
        $p3=$this->view->FormSelect($select);

        $html.='<div id="f57_dialog" title="Управление доступом" style="display:none">
        
<table border="1" cellpadding="5" cellspacing="0" class="permission_editor">
  <tbody>
    <tr>
      <td>Код:</td>
      <td id="mode_f57" style="font-weight: bold"> </td>
    </tr>
    <tr>
      <td>Доступ владельца:</td>
      <td>'.$p1.'</td>
    </tr>
    <tr>
      <td>Доступ группы:</td>
      <td>'.$p2.'</td>
    </tr>
    <tr>
      <td>Доступ остальных:</td>
      <td>'.$p3.'</td>
    </tr>
  </tbody>
</table>

        
        
    </div>';
        static::$flag_dialog=false;
    }
    return $html;
}


    
    
protected function permissionToText($perms)
{
    $info = (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

// Группа
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

// Мир
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
return $info;
}
}
