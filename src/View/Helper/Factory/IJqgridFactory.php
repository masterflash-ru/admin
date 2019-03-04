<?php
namespace Admin\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Service\JqGrid\ColModelHelper;

/**
 * 
 * 
 */
class IJqgridFactory implements FactoryInterface
{
public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    ColModelHelper::setContainer($container);
   $config=$container->get('config');
    return new $requestedName($config["interface"]);
}
}
