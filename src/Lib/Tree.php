<?php
//генерация дерева чего-либо
/*
скопировано со старой админки, некоторые св-ва выброшены, но логика полностью повторяет старую версию

*/

namespace Admin\Lib;

class Tree
{

public $statusmsg='';
public $mnu=[];//сам массив меню
public $ss=0;//внутренний счетчик
public $menu_name="menu";//имя меню
public $target='_parent';//в каком окне открывать при щелчке на ссылке
public $div_atr='';//атрибуты тега DIV 
public $menu_type=0;//если 0 тогда обычный вывод, 1- вывод в табличной форме
public $status_old;//старое состояние меню
public $status_old_id;//старый список идентификаторов эл-ов меню список или массив



function __construct()
{
	$this->mnu['text']=[];
	$this->mnu['url']=[];
	$this->mnu['level']=[];
	$this->mnu['link_prop']=[];
	$this->mnu['title']=[];
	$this->mnu['item_id']=[];
	
}


function add_item($text,$url='',$level=0,$link_prop='',$title='',$id=0)
{
	$url=htmlspecialchars($url,ENT_NOQUOTES);
	$url=preg_replace ("/\040/",'%20',$url);
    $text=str_replace ("'","\'",$text);
	$text=preg_replace("/\n|\r/",'',$text);	//в одну строку
	$this->mnu['text'][$this->ss]=$text;
	$this->mnu['url'][$this->ss]=$url;
	$this->mnu['level'][$this->ss]=$level;
	$this->mnu['link_prop'][$this->ss]=$link_prop;
	//$this->mnu['title'][$this->ss]=$title;
	$this->mnu['item_id'][$this->ss]=$id;
	$this->ss++;
}

function get_tree_array()
{return $this->mnu;}



function tree_out()
{
	echo $this->get_tree();
}



function get_tree()
{
	$status='';
//список идентификаторов меню либо список либо массив, все примести к массиву
if (is_array($this->status_old_id)) $status_old_id=$this->status_old_id; else $status_old_id=explode(',',$this->status_old_id);

if (strlen($this->status_old)!=count ($this->mnu['level']) && count($this->mnu['item_id'])>0){
    //проверить идентификаторы которые были, соответсвенно взять их состояние, если нет такого, тогда состояние элемента 0
    for ($i=0;$i<count($this->mnu['item_id']);$i++)	{
        $b=array_search($this->mnu['item_id'][$i],$status_old_id);
        if (false!==$b) $status.=substr($this->status_old,$b,1); else $status.='0';
    }
} else  {
    $status=$this->status_old;
}
if (count($this->mnu['item_id'])>0) {
    $status_id=implode(',',$this->mnu['item_id']);//ничего не изменилось, вернуть старое все
} else {
    $status_id='';
}


//определения
$out= '<div id="'.$this->menu_name.'_out" '.$this->div_atr.'> <!--  сюда вставляется дерево меню, программно--></div>'."\n";
$out.='<SCRIPT LANGUAGE="JavaScript">'."\n";

$out.= 'db["'.$this->menu_name.'"] = [];'."\n";
//сам массив дерева
for ($i=0;$i<count ($this->mnu['text']);$i++){
    //есть след подуровень?
    if (isset($this->mnu['level'][$i+1]) && $this->mnu['level'][$i+1]>$this->mnu['level'][$i]) $fl='true'; else $fl='false';
	$out.='db["'.$this->menu_name.'"][db["'.$this->menu_name.'"].length] = new dbRecord('.$fl.",'".$this->mnu['text'][$i]."','".$this->mnu['url'][$i]."','".$this->mnu['level'][$i]."','' ,'');\n";
}	
$out.='mycookie["'.$this->menu_name.'"] = document.cookie;
//обнуление
setCurrState("'.$status.'","'.$this->menu_name.'");// начальное состояние
setCurrState("'.$status_id.'","'.$this->menu_name.'_id");// начальное состояние уникальные идентификаторы меню
if (getCurrState("'.$this->menu_name.'") == "" || getCurrState("'.$this->menu_name.'").length != (db["'.$this->menu_name.'"].length)) {
  initState = "";
  for (i = 0; i < db["'.$this->menu_name.'"].length; i++){
    initState += "0"
    }
	setCurrState(initState,"'.$this->menu_name.'")
}';
if (!$this->menu_type) $out.='out_intab("'.$this->menu_name.'");'; else $out.='out("'.$this->menu_name.'");';
$out.="\n";
$out.= '</script>';
return $out;
	
}
}