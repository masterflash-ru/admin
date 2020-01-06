<?php
/**
контроллер конструктора линейных структур
 */

namespace Admin\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Admin\Lib\Simba;


class ConstructorLineController extends AbstractActionController
{
    protected $connection;
    protected $sessionManager;
    protected $config;

public function __construct ($connection,$sessionManager,$config)
{
    $this->connection=$connection;
    $this->sessionManager=$sessionManager;
    $this->config=$config;
}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
    $view=new ViewModel(["config"=>$this->config]);
    if (!$this->acl([get_class($this),"index"])->isAllowed("r")){
        /*чтение разрешено?*/
        /*разрешение на запуск проверяется в файле module.php, там это делается оптом*/
        $view->setTemplate("admin/index/accessdenied");
    }
    Simba::$connection=$this->connection;
    return $view;
}



}
