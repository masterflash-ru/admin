<?php
/**
спецтальный контроллер для поля F41
 */

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container;


class CkeditorController extends AbstractActionController
{



/*вывод левой части фрейма с меню*/
public function indexAction()
{
  return new ViewModel();
}

}
