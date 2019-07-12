<?php
namespace Admin\Service;

use Zend\Router\Exception\RuntimeException;
/*
сервис обработки прерывания GetControllersInfoAdmin simba.admin
нужен для генерации ссылок для подстановки в меню сайта или админки для визуализации выбора

*/


class GetControllersInfo 
{
    protected $Router;
    protected $options;
    protected $connection;
    protected $config;
    
    public function __construct($connection,$Router,$options,$config) 
    {
        
        $this->Router=$Router;
        $this->options=$options;
        $this->connection=$connection;
        $this->config=$config;
    }
    
    /**
    * получить все MVC адреса, разбитые по языкам
    */
    public function getMvc()
    {
        $locale="ru_RU";
        //данный модуль содержит только сайтовские описатели описатели
        if ($this->options["category"]!="backend") {return [];}
        //Универсальный редактор информации
        $info["iuniversal"]["description"]="Универсальный редактор информации";
        $rez['name']=[];
        $rez['url']=[];
        if (isset($this->config["interface"]) && $this->config["interface"]){
            foreach ($this->config["interface"] as $name=>$interface){
                $c=include $interface;
                if (isset($c["description"])){
                    $rez["name"][$locale][]=$c["description"];
                    $rez["url"][$locale][]=$this->Router->assemble(["interface"=>$name], ['name' => 'adm/universal-interface']);
                }
            }
        }
        
        $info["iuniversal"]["urls"]=$rez;
        
        //архивация
        $info["backuprestore"]["description"]="Архивация/восстановление базы";
        $info["backuprestore"]["urls"]["name"][$locale][]="Вход";
        $info["backuprestore"]["urls"]["url"][$locale][]=$this->Router->assemble([], ['name' => 'adm/backuprestore']);

        //генератор Entity
        $info["entity"]["description"]="Генератор объектов";
        $info["entity"]["urls"]["name"][$locale][]="Создать объект";
        $info["entity"]["urls"]["url"][$locale][]=$this->Router->assemble([], ['name' => 'adm/entitygenerator']);

        //древовидные таблицы
        $info["tree"]["description"]="Устаревший редактор древовидных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=1 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/tree']);
            $rez["name"][$locale][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][$locale][]=$url;
            $rs->MoveNext();
        }
        $info["tree"]["urls"]=$rez;



        //Линейные таблицы
        $info["line"]["description"]="Устаревший редактор линейных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=0 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/line']);
            $rez["name"][$locale][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][$locale][]=$url;
            $rs->MoveNext();
        }
        $info["line"]["urls"]=$rez;
        
        //древовидные таблицы
        $info["tree"]["description"]="Устаревший редактор древовидных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=1 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/tree']);
            $rez["name"][$locale][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][$locale][]=$url;
            $rs->MoveNext();
        }
        $info["tree"]["urls"]=$rez;
        
        //Конструктор линейных структур
        $info["constructortree"]["description"]="Устаревший конструктор древовидных структур";
        $info["constructortree"]["urls"]["name"][$locale][]="Редактировать";
        $info["constructortree"]["urls"]["url"][$locale][]=$this->Router->assemble([], ['name' => 'adm/constructortree']);

        //Конструктор линейных структур
        $info["constructorline"]["description"]="Устаревший конструктор линейных структур";
        $info["constructorline"]["urls"]["name"][$locale][]="Редактировать";
        $info["constructorline"]["urls"]["url"][$locale][]=$this->Router->assemble([], ['name' => 'adm/constructorline']);
        return $info;
    }
    /**
    * устаревший вызов, для получения списка MVC адресов, в админ панели
    */
    public function GetDescriptors()
    {
        //данный модуль содержит только админксие описатели
        if ($this->options["name"]!="admin") {return [];}

        //Универсальный редактор информации
        $info["iuniversal"]["description"]="Универсальный редактор информации";
        $rez['name']=[];
        $rez['url']=[];
        if (isset($this->config["interface"]) && $this->config["interface"]){
            foreach ($this->config["interface"] as $name=>$interface){
                $c=include $interface;
                if (isset($c["description"])){
                    $rez["name"][]=$c["description"];
                    $rez["url"][]=$this->Router->assemble(["interface"=>$name], ['name' => 'adm/universal-interface']);
                }
            }
        }
        
        $info["iuniversal"]["urls"]=$rez;
        
        //архивация
        $info["backuprestore"]["description"]="Архивация/восстановление базы";
        $info["backuprestore"]["urls"]["name"][]="Вход";
        $info["backuprestore"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/backuprestore']);

        //генератор Entity
        $info["entity"]["description"]="Генератор объектов";
        $info["entity"]["urls"]["name"][]="Вход";
        $info["entity"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/entitygenerator']);

        //древовидные таблицы
        $info["tree"]["description"]="Устаревший редактор древовидных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=1 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/tree']);
            $rez["name"][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][]=$url;
            $rs->MoveNext();
        }
        $info["tree"]["urls"]=$rez;



        //Линейные таблицы
        $info["line"]["description"]="Устаревший редактор линейных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=0 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/line']);
            $rez["name"][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][]=$url;
            $rs->MoveNext();
        }
        $info["line"]["urls"]=$rez;
        
        //древовидные таблицы
        $info["tree"]["description"]="Устаревший редактор древовидных структур";
        $rs=$this->connection->Execute("SELECT interface_name as name,interface_name  FROM design_tables where row_type=0 and table_type=1 order by name");
        $rez['name']=[];
        $rez['url']=[];
        while (!$rs->EOF) {
            $url = $this->Router->assemble(["table"=>$rs->Fields->Item["interface_name"]->Value], ['name' => 'adm/tree']);
            $rez["name"][]=$rs->Fields->Item["name"]->Value;
            $rez["url"][]=$url;
            $rs->MoveNext();
        }
        $info["tree"]["urls"]=$rez;
        
        //Конструктор линейных структур
        $info["constructortree"]["description"]="Устаревший конструктор древовидных структур";
        $info["constructortree"]["urls"]["name"][]="Редактировать";
        $info["constructortree"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/constructortree']);

        //Конструктор линейных структур
        $info["constructorline"]["description"]="Устаревший конструктор линейных структур";
        $info["constructorline"]["urls"]["name"][]="Редактировать";
        $info["constructorline"]["urls"]["url"][]=$this->Router->assemble([], ['name' => 'adm/constructorline']);

        return $info;
    }
    
}



