<?php
namespace Admin\Service\Admin\JqGrid\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\EventManager\EventManager;

/*

*/

class FactoryGetAdminUrls
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $SharedEventManager=$container->get('SharedEventManager');
    $SharedEventManager=new EventManager($SharedEventManager);
    $SharedEventManager->addIdentifiers(["simba.admin"]);
    //$controllers_descriptions=$SharedEventManager->trigger("GetControllersInfoAdmin",NULL,["name"=>"admin","container"=>$container]);
    $controllers_descriptions=$SharedEventManager->trigger("GetMvc",NULL,["category"=>"backend"]);
    return new $requestedName($controllers_descriptions);
}
}

