<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\ConstructorLineController;

use Zend\Session\SessionManager;

/**
 */
class ConstructorLineControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
       $connection=$container->get('ADO\Connection');
	   $sessionManager = $container->get(SessionManager::class);
		//имя базы данных из конфига
		$config = $container->get('Config');
		define ("DBNAME",$config["db"]["database"]);
		return new ConstructorLineController($connection,$sessionManager);
    }
}

