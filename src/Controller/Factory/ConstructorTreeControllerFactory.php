<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\ConstructorTreeController;

use Laminas\Session\SessionManager;

/**
 */
class ConstructorTreeControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
        $sessionManager = $container->get(SessionManager::class);

        $config = $container->get('Config');
        /*парсим строку соединения с базой и извлекаем имя нашей базы данных, это костыль для конструкторов интерфейса*/
        define ("DBNAME",trim(parse_url($connection->ConnectionString, PHP_URL_PATH),"/"));

        return new $requestedName($connection,$sessionManager,$config);
    }
}

