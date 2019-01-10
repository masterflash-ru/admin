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
        4=>"Чтение",
        5=>"Чтение и запуск",
        6=>"Чтение и запись",
        1=>"Запуск",
        2=>"Запись",
        3=>"Запись и запуск",
        7=>"Полный",
    ];


public function __construct($item_id)
{
	parent::__construct($item_id);

}
	
	
	
public function render()
{
    $input = new Element\Hidden($this->name[0]);
    $input1 = new Element\Button("button_".$this->name[0]);
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
    $user=($permissions["owner_user_name"]) ? $permissions["owner_user_name"] : (int)$permissions["owner_user"];
    $group=($permissions["owner_group_name"]) ? $permissions["owner_group_name"] : (int)$permissions["owner_group"];
    $mode_text=$this->permissionToText($permissions["mode"]);

    /*слева добавим незначащие нули*/
    $mode_oct =str_pad (decoct($permissions["mode"]),4,"0",STR_PAD_LEFT);


    
    $input1->setLabel("{$user}:{$group} {$mode_text} ({$mode_oct})");

    $this->zatr["onclick"]='f57($(this),$(this).next());';
   $this->zatr["class"]="permiss";

    
    $input1->setAttributes($this->zatr);
    $input->setValue(implode(",",[(int)$permissions["owner_user"],(int)$permissions["owner_group"],(int)$permissions["mode"]]));
    
    $html=$this->view->FormButton($input1);
    $html.=$this->view->FormElement($input);
    
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

        //читаем всех юзеров и блокированных тоже
        $uu=simba::QueryAllRecords("select id,name from users");
        for($i=0; $i<simba::numRows();$i++){
            $u[$uu["id"][$i]]=$uu["name"][$i];
        }
        $select = new Element\Select("u");
        $select->setAttributes(["id"=>"u"]);
        $select->setValueOptions($u);
        $select->setEmptyOption("неизвестный");
        $u=$this->view->FormSelect($select);

        //читаем все группы
        $uu=simba::QueryAllRecords("select id,name from users_group");
        for($i=0; $i<simba::numRows();$i++){
            $g[$uu["id"][$i]]=$uu["name"][$i];
        }

        $select = new Element\Select("g");
        $select->setAttributes(["id"=>"g"]);
        $select->setValueOptions($g);
        $select->setEmptyOption("неизвестная");
        $g=$this->view->FormSelect($select);
        
        
        
        $html.='<div id="f57_dialog" title="Управление доступом" style="display:none">
        
<table border="1" cellpadding="5" cellspacing="0" class="permission_editor">
  <tbody>
    <tr>
      <td>Код (восмеричный):</td>
      <td id="mode_f57" style="font-weight: bold"> </td>
    </tr>
    <tr style="background-color:#eee">
      <td>Владелец:</td>
      <td>'.$u.'</td>
    </tr>
    <tr style="background-color:#eee">
      <td>Группа:</td>
      <td>'.$g.'</td>
    </tr>
    
    <tr style="background-color:#ddd">
      <td>SUID:</td>
      <td><input type="checkbox" id="suid" class="perm_bits" value="2048"></td>
    </tr>
    <tr style="background-color:#ddd">
      <td>SGID:</td>
      <td><input type="checkbox" id="sgid" class="perm_bits" value="1024"></td>
    </tr>
    <tr style="background-color:#ddd">
      <td>Sticky:</td>
      <td><input type="checkbox" id="sticky" class="perm_bits" value="512"></td>
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

/*обработка записи

$this->id - ID записи основной таблицы (товара)
*/
public function save()
{
    $values=explode(",",$_POST[$this->col_name][$this->id]);
    /*$values=>[
    владелец,группа,код_доступа
    ]*/
   
    Simba::ReplaceRecord([
        "id"=>(int)$this->id,
        "owner_user"=>$values[0],
        "owner_group"=>$values[1],
        "mode"=>$values[2],
    ],"permissions");

    
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
