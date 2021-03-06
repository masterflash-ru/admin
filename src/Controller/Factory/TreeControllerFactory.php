<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\TreeController;


/**
 */
class TreeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
        $cache = $container->get('DefaultSystemCache');
        $config = $container->get('Config');
        $SharedEventManager = $container->get('SharedEventManager');
        return new TreeController($container);
    }
}

