<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

use Admin\Service\Zform\Zform;



/**
 */
class ZformControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
       $config=$container->get("config");
	   $cache = $container->get('DefaultSystemCache');
        $zform=$container->get(Zform::class);
        

       return new $requestedName( $connection,$cache,$config["interface"],$zform);
    }
}

