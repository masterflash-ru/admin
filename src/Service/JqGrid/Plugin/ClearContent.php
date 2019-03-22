<?php
namespace Admin\Service\JqGrid\Plugin;

/*
*/
use Admin\Service\JqGrid\Plugin\AbstractPlugin;




class ClearContent extends AbstractPlugin

{
public function add($value,$postParameters)
{
    return $this->edit($value,$postParameters);
}
public function edit($value,$postParameters)
{
    if (empty(trim($value))) {
        $value=$this->strip_only($value,'<font>',false);
    }

    $value=preg_replace('/-{2,}/','-',$value);

    return $value;    
}


protected function strip_only($str, $tags, $stripContent = false) {
    $content = '';
    if(!is_array($tags)) {
        $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
        if(end($tags) == '') array_pop($tags);
    }
	
    foreach($tags as $tag) {
        if ($stripContent)
             $content = '(.+</'.$tag.'[^>]*>|)';
         $str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#isu', '', $str);
    }
    return $str;
} 


}