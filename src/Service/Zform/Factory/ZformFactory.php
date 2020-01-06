<?php
namespace Admin\Service\Zform\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Validator\Translator\TranslatorInterface;
/*

*/

class ZformFactory implements FactoryInterface
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $pluginManager=$container->get('ZformManager');
    $translator = $container->get(TranslatorInterface::class);
    return new $requestedName($pluginManager,$translator);
}
}

