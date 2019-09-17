<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;


/**
 */
class ZrkubanControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $connection=$container->get('DefaultSystemDb');
        $GalleryLib=$container->get('GalleryLib');
        return new $requestedName($GalleryLib,$connection,$config,$container);
    }
}

