<?php
namespace Admin\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\View\Helper\Menu;
use Zend\Session\SessionManager;
use Mf\Permissions\Service\RbacManager;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class MenuFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       $connection=$container->get('ADO\Connection');
	   $sessionManager = NULL;//$container->get(SessionManager::class);
	   $RbacManager = $container->get(RbacManager::class);
        
        return new Menu($connection,$RbacManager,$sessionManager);
    }
}

