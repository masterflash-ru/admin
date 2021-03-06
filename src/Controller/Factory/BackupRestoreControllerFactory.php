<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\BackupRestoreController;


/**
 */
class BackupRestoreControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        //имя базы данных из конфига
        $config = $container->get('Config');
        $serviceListener = $container->get('ServiceListener');
        return new $requestedName($config);
    }
}

