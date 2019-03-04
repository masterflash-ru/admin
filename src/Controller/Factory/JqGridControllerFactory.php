<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Admin\Service\JqGrid\JqGrid;



/**
 */
class JqGridControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
       $config=$container->get("config");
	   $cache = $container->get('DefaultSystemCache');
        $jqgrid=$container->get(JqGrid::class);

       return new $requestedName( $connection,$cache,$config["interface"],$jqgrid);
    }
}

