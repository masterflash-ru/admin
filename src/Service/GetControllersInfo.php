<?php
namespace Admin\Service;

/*
сервис обработки прерывания GetControllersInfoAdmin simba.admin
нужен для генерации ссылок для подстановки в меню сайта или админки для визуализации выбора

*/


class GetControllersInfo 
{
	protected $Router;
	protected $options;
	protected $connection;
	
    public function __construct($connection,$Router,$options) 
    {
		
		//$router = $this->getEvent()->getRouter();
		//$url    = $router->assemble($params, ['name' => 'route-name']);
		
		$this->Router=$Router;
		$this->options=$options;
		$this->connection=$connection;
		//$url    = $router->assemble(["table"=>"TEST"], ['name' => 'adm/line']);
		
		
		
		//echo "Конструктор GetControllersInfo сервис, нужно сделать плагин url по аналогии с контроллерами {$url}";
		//\Zend\Debug\Debug::dump(get_class($container->get("Application")  ));
    }
    
	
	public function GetDescriptors()
	{
		//данный модуль содержит только админксие описатели
		if ($this->options["name"]!="admin") {return [];}


		//Линейные таблицы
		$info["line"]["description"]="Редактор линейных структур";
		$rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=0 order by name");
		$rez['name']=[];
		$rez['url']=[];
		while (!$rs->EOF)
			{
				$url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/line']);
				$rez["name"][]=$rs->Fields->Item["name"]->Value;
				$rez["url"][]=$url;
				$rs->MoveNext();
			}
		$info["line"]["urls"]=$rez;
		
		//древовидные таблицы
		$info["tree"]["description"]="Редактор древовидных структур";
		$rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=1 order by name");
		$rez['name']=[];
		$rez['url']=[];
		while (!$rs->EOF)
			{
				$url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/tree']);
				$rez["name"][]=$rs->Fields->Item["name"]->Value;
				$rez["url"][]=$url;
				$rs->MoveNext();
			}
		$info["tree"]["urls"]=$rez;
		
		//Конструктор линейных структур
		$info["constructortree"]["description"]="Конструктор древовидных структур";
		$info["constructortree"]["urls"]["name"][]="Редактировать";
		$info["constructortree"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/constructortree']);

		//Конструктор линейных структур
		$info["constructorline"]["description"]="Конструктор линейных структур";
		$info["constructorline"]["urls"]["name"][]="Редактировать";
		$info["constructorline"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/constructorline']);

		//архивация
		$info["backuprestore"]["description"]="Архивация/восстановление базы";
		$info["backuprestore"]["urls"]["name"][]="Вход";
		$info["backuprestore"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/backuprestore']);

		//генератор Entity
		$info["entity"]["description"]="Генератор объектов";
		$info["entity"]["urls"]["name"][]="Вход";
		$info["entity"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/entitygenerator']);


		return $info;
	}
	
}



