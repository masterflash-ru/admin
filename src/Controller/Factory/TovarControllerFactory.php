<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 */
class TovarControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
		$config = $container->get('Config');
		$connection=$container->get('ADO\Connection');
		return new $requestedName($connection,$config,$container);
    }
}

