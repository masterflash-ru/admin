<?php

namespace Admin\Service\JqGrid\Plugin;


class Cache extends AbstractPlugin
{
	protected $cache;
    protected $def_options =[
        "tags"=>[],             //теги поиска
        "keys" => [],           //ключи поиска

    ];

    public function __construct($cache) 
    {
		$this->cache=$cache;
    }
    

/**
* Очистка кеша
*/
public function write()
{
    if (!empty($this->options["keys"])){
        $this->cache->removeItems($this->options["keys"]);
    }
    if (!empty($this->options["tags"])){
        $this->cache->clearByTags($this->options["tags"],true);
    }
}

}