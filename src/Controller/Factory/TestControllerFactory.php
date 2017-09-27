<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\TestController;

use Zend\Session\SessionManager;

/**
 */
class TestControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       $connection=$container->get('ADO\Connection');
	   $sessionManager = $container->get(SessionManager::class);
       		
		return new TestController($connection,$sessionManager);
    }
}

