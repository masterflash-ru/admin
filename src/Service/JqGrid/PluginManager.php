<?php
/**
* менеджер плагинов для работы с сеткой
*/

namespace Admin\Service\JqGrid;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;


class PluginManager extends AbstractPluginManager
{
    /**
     * плагины в виде алиасов
     *
     * @var array
     */
    protected $aliases = [
        "db" => Plugin\Db::class,
        "Db" => Plugin\Db::class,
        "db1" => Plugin\Db::class,
        "Db1" => Plugin\Db::class,
        "db2" => Plugin\Db::class,
        "Db2" => Plugin\Db::class,
        "cache" => Plugin\Cache::class,
        "Cache" => Plugin\Cache::class,
        
        
        
        
        
        "Datetime"=>   Plugin\Datetime::class,
        "Date"=>   Plugin\Date::class,
        "datetime"=>   Plugin\Datetime::class,
        "date"=>   Plugin\Date::class,
        "Images" => Plugin\Images::class,
        "images" => Plugin\Images::class,
        
        "Files" => Plugin\Files::class,
        "files" => Plugin\Files::class,
        
        "Translit" => Plugin\Translit::class,
        "translit" => Plugin\Translit::class,
        
        "Permissions" => Plugin\Permissions::class,
        "permissions" => Plugin\Permissions::class,

        //"Options" => Plugin\Options::class,
        //"options" => Plugin\Options::class,

        
        "SelectFromDb" => Plugin\SelectFromDb::class,
        "selectFromDb" => Plugin\SelectFromDb::class,
        "selectfromdb" => Plugin\SelectFromDb::class,
       
        "SelectConfigStorage" => Plugin\SelectConfigStorage::class,
        "selectConfigStorage" => Plugin\SelectConfigStorage::class,
        "selectconfigstorage" => Plugin\SelectConfigStorage::class,
        
        "Locale" => Plugin\Locale::class,
        "locale" => Plugin\Locale::class,
        
        "ClearContent" => Plugin\ClearContent::class,
        "clearcontent" => Plugin\ClearContent::class,
  
        "LastMod" => Plugin\LastMod::class,
        "lastmod" => Plugin\LastMod::class,
        
        "TreeAdjacency" => Plugin\TreeAdjacency::class,
        "StringToArray"=> Plugin\StringToArray::class,
        
        //"Autocomplete" => Plugin\Autocomplete::class,
        //"autocomplete" => Plugin\Autocomplete::class,
    ];
    

    /**
     * плагины фабрики
     *
     * @var array
     */
    protected $factories = [
        Plugin\Db::class => Plugin\Factory\Db::class,
        Plugin\TreeAdjacency::class => Plugin\Factory\TreeAdjacency::class,
        Plugin\Cache::class => Plugin\Factory\Cache::class,
        
        /*поэлементная обработка*/
        Plugin\Datetime::class => InvokableFactory::class,
        Plugin\StringToArray::class => InvokableFactory::class,
        Plugin\Date::class => InvokableFactory::class,
        Plugin\Images::class => Plugin\Factory\Images::class,
        Plugin\Files::class => Plugin\Factory\Files::class,
        Plugin\Translit::class => InvokableFactory::class,
        Plugin\ClearContent::class => InvokableFactory::class,
        Plugin\LastMod::class => InvokableFactory::class,
        Plugin\SelectFromDb::class => Plugin\Factory\SelectFromDb::class,
        Plugin\SelectConfigStorage::class => Plugin\Factory\SelectConfigStorage::class,
        Plugin\Permissions::class => Plugin\Factory\Permissions::class,
        Plugin\Locale::class => Plugin\Factory\Locale::class,
        Plugin\TreeLevel::class => Plugin\Factory\TreeLevel::class,
       // Plugin\Options::class => InvokableFactory::class,
       // Plugin\Autocomplete::class => Plugin\Factory\Autocomplete::class,
    ];

    /**
     * Whether or not to share by default; default to false (v2)
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Whether or not to share by default; default to false (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Default instance type
     *
     * @var string
     */
    protected $instanceOf = Plugin\JqGridPluginsInterface::class;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached translator, if any, to the currently requested helper.
     *
     * {@inheritDoc}
     */
    public function __construct($configOrContainerInstance = null, array $v3config = [])
    {
        parent::__construct($configOrContainerInstance, $v3config);
    }



}
