<?php
namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;
use Admin\Service\AuthAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
фабрика адаптера авторизацйии
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * собсвтенно генератор объекта адаптера генератора, передаем в сам объект соединение с базой
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        

        $connection=$container->get('ADO\Connection');       
                        
        return new AuthAdapter($connection);
    }
}
