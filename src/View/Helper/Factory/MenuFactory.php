<?php
namespace Admin\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\View\Helper\Menu;
use Zend\Session\SessionManager;

use Zend\Authentication\AuthenticationService;

/**
 * Фабрика помощника меню администратора
 * 
 */
class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       $connection=$container->get('DefaultSystemDb');
	   $sessionManager = NULL;//$container->get(SessionManager::class);
	   $AuthenticationService = $container->get(AuthenticationService::class);
        
        return new $requestedName($connection,$sessionManager,$AuthenticationService);
    }
}

