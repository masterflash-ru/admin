<?php
namespace Admin\View\Helper;

use Zend\View\Helper\AbstractHelper;

//для вывода меню слева
use Admin\Lib\Tree;
use ADO\Service\RecordSet;

/**
 * помощник - генератор меню админки
 */
class Menu extends AbstractHelper 
{
	protected $connection;
	protected $sessionManager;
	protected $rbacManager;

public function __construct ($connection,$rbacManager,$sessionManager)
	{
		$this->connection=$connection;
		$this->sessionManager=$sessionManager;
		$this->rbacManager=$rbacManager;
		
	}
    
    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
 public function render() 
    {
		if (!$this->rbacManager->isGranted(null, "admin.login")) {return "<h1>403</h1>";}
		$tree=new tree();
		$tree->menu_name='menu_l';
		if (isset($_COOKIE['menu_l'])) $tree->status_old=$_COOKIE['menu_l']; else $tree->status_old='';
		if(isset($_COOKIE['menu_l']))$tree->status_old_id=$_COOKIE['menu_l']; else $tree->status_old_id='';
		
		//$tree->target='mainFrame';//фреймовой окно
		
		$rs=new RecordSet();
		$rs->CursorType = adOpenKeyset;
		$rs->maxRecords=0;
		$rs->open("SELECT * FROM  admin_menu    order by id",$this->connection);
		
		$this->rs=$rs;
		$this->tree=$tree;
		$this->tovtree(0,0);
		$tree->menu_type=1;
		return $tree->tree_out();
 }

protected function tovtree($subid,$lev)
{
	//получить список подразделов
	$rs1=clone $this->rs;
	$rs1->Filter="subid=$subid and level=$lev";

	$status='';
	while (!$rs1->EOF) 
		{
				$lev1=$lev+1;
				if ($rs1->Fields->Item['url']->Value) $url=$rs1->Fields->Item['url']->Value; else $url='';
				if ($rs1->Fields->Item['level']->Value) 
					{
						$this->tree->add_item(htmlentities('<span class="menu">'.$rs1->Fields->Item["name"]->Value."</span>",ENT_NOQUOTES | ENT_XHTML),$url,$rs1->Fields->Item["level"]->Value,"","",$rs1->Fields->Item["id"]->Value);//это только для коргня раздела
					}
				else  
					{
						$this->tree->add_item(htmlentities('<span class="menu0">'.$rs1->Fields->Item['name']->Value."</span>",ENT_NOQUOTES | ENT_XHTML),$url,$rs1->Fields->Item['level']->Value,'','',$rs1->Fields->Item['id']->Value);
					}
			$this->tovtree ((int)$rs1->Fields->Item['id']->Value,(int)$lev1);
			$rs1->MoveNext();
		}

}

}
