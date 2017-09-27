<?php
/*
вывод картинки как есть
*/

namespace Admin\Lib\Fhelper;
use Zend\Form\Element;


class F11 extends Fhelperabstract 
{
	protected $hname="Изображение";
	protected $category=7;
	protected $itemcount=1;
	protected $constcount=1;
	protected $const_count_msg=["Относительный путь к библиотеке изображений:"];
	
public function __construct($item_id)
{
		parent::__construct($item_id);
}
	
	
	
public function render()
{
if (preg_match("/\.swf$/i",$this->value)) 
	{
		$s=@getimagesize(ROOT_FILE_SYSTEM.$this->const[0].$this->value);
		$item_html='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="'.$s[0].'" height="'.$s[1].'"><param name="allowScriptAccess" value="sameDomain"><param name="movie" value="'.ROOT_URL.$this->const[0].$this->value.'"><param name="PLAY" value="false" /><param name="quality" value="high"><embed src="'.ROOT_URL.$this->const[0].$this->value.'" quality="high" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'.$s[0].'" height="'.$s[1].'" play="false"></embed></object>';}
	else 
		{
			$item_html="<img alt='' src=\"".ROOT_URL.$this->const[0].$this->value."\">";
		}
	return $this->view->formHidden($this->name[0],$this->value).$item_html;
}



}
