<?php
namespace Admin\Entity;


class Users
{

/*
Карта имен полей таблицы и имен местных переменных
по формату:
имя в таблице => имя в этом объекте
* /
	
private static $__map__=[
	"fields"=>[
			/*Имя поля таблицы => имя в этой сущности + параметры* /
			"id"=>["name"=>"id","type"=>"int","length"=>11],
			"email"=>["name"=>"email"],
			"name"=>["name"=>"name"],
			"pass"=>["name"=>"pass"],
			"fullname"=>["name"=>"fullname","type"=>"string","length"=>100],
			"email"=>["name"=>"email"],
			"tel_mobil"=>["name"=>"tel_mobil"]
			],
	"table"=>"Admins"
	];
*/
	const STATUS_ACTIVE       = 1; //нормальное состояние
    const STATUS_NONACTIVE    = 0; //не активный.
	
	
    //описания полей таблицы
    protected $id;
    protected $email;
    protected $fullName;
    protected $password;
    protected $name;
    protected $roles;


    public function getId() 
    {
        return $this->id;
    }
    public function setId($id) 
    {
        $this->id = $id;
    }


    public function getemail() 
    {
        return $this->email;
    }

    public function setemail($email) 
    {
        $this->email = $email;
    }
    

    public function getFullname() 
    {
        return $this->fullName;
    }       


    public function setFullname($fullName) 
    {
        $this->fullName = $fullName;
    }

    public function getRoles() 
    {
        return explode(",",$this->roles);
    }

    

    public function setroles($r) 
    {
        $this->roles = $r;
    }   
    

    public function getPassword() 
    {
       return $this->password; 
    }

    public function setpassword($password) 
    {
        $this->password = $password;
    }
    

    
}



