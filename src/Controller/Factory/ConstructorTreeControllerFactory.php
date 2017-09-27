<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\ConstructorTreeController;

use Zend\Session\SessionManager;

/**
 */
class ConstructorTreeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       $connection=$container->get('ADO\Connection');
	   $sessionManager = $container->get(SessionManager::class);
		//имя базы данных из конфига
		$config = $container->get('Config');
		define ("DBNAME",$config["db"]["database"]);
		return new ConstructorTreeController($connection,$sessionManager);
    }
}

