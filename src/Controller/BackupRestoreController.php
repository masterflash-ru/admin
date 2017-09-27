<?php
/**
старый модуль архивации/разархивации базы данных
 */

namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class BackupRestoreController extends AbstractActionController
{
	protected $config;

public function __construct ($config)
	{
		$this->config=$config;
	}



/*вывод левой части фрейма с меню*/
public function indexAction()
{
  return new ViewModel(["config"=>$this->config]);
	
}



}
