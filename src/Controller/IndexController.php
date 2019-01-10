<?php
/**
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;


class IndexController extends AbstractActionController
{
	protected $authManager;
	protected $authService;
	protected $sessionManager;


public function __construct ($authManager, $authService,$sessionManager)
	{
		$this->authManager=$authManager;
		$this->authService=$authService;
		$this->sessionManager=$sessionManager;
		
	}

/*
сюда переходим при входе в админку после успешной авторизации
*/
public function indexAction()
{
    if(!$this->Acl()->isAllowed("x",__METHOD__)  ){
        //успешная авторизация, но доступ запрещен
       // return $this->redirect()->toRoute('accessdenied');
    }
    
	$viewModel=new ViewModel();
  return $viewModel;
}






}
