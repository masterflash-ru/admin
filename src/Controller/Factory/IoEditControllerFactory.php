<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Admin\Service\GpGrid;



/**
 */
class IoEditControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
       $config=$container->get("config");
	   $cache = $container->get('DefaultSystemCache');
        $jpgrid=$container->get(GpGrid::class);

       return new $requestedName( $connection,$cache,$config,$jpgrid);
    }
}

