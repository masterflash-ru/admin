<?php
namespace Admin\Service\Factory;

use Interop\Container\ContainerInterface;
use Admin\Service\GetControllersInfo;

/*
Фабрика 
сервис обработки прерывания GetControllersInfoAdmin simba.admin
нужен для генерации ссылок для подстановки в меню сайта или админки для визуализации выбора

$options - массив с ключами
name =>имя раздела сайта, admin - админка, "" - сам сайт

*/

class GetControllersInfoFactory
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
		$connection=$container->get('ADO\Connection');
       $Router=$container->get("Application")->getMvcEvent()->getRouter();
        return new GetControllersInfo($connection,$Router,$options);
    }
}

