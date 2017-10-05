<?php
/**
спецтальный контроллер для поля F41
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;


class CkeditorController extends AbstractActionController
{



/*вывод левой части фрейма с меню*/
public function indexAction()
{
  return new ViewModel();
}

}
