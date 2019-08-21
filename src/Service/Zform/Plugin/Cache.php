<?php

namespace Admin\Service\Zform\Plugin;


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
    
public function idel(array $postParameters)
{
    $this->iedit($postParameters);
}
/**
* Очистка кеша
*/
public function iedit(array $postParameters)
{
    if (!empty($this->options["keys"])){
        $this->cache->removeItems($this->options["keys"]);
    }
    if (!empty($this->options["tags"])){
        $this->cache->clearByTags($this->options["tags"],true);
    }
}

}