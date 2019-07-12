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
    
public function del(array $postParameters)
{
    $this->edit($postParameters);
}
/**
* Очистка кеша
*/
public function edit(array $postParameters)
{
    if (!empty($this->options["keys"])){
        $this->cache->removeItems($this->options["keys"]);
    }
    if (!empty($this->options["tags"])){
        $this->cache->clearByTags($this->options["tags"],true);
    }
}
/**
* Очистка кеша
*/
public function add(array $postParameters)
{
    if (!empty($this->options["keys"])){
        $this->cache->removeItems($this->options["keys"]);
    }
    if (!empty($this->options["tags"])){
        $this->cache->clearByTags($this->options["tags"],true);
    }
}

}