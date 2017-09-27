<?php
namespace Admin\Entity;

class Login 
{
    protected $login;
    protected $password;

    public function getLogin() 
    {
        return $this->login;
    }       

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setLogin($login) 
    {
        $this->login = $login;
    }
    
    public function getPassword() 
    {
       return $this->password; 
    }
    
    public function setPassword($password) 
    {
       $this->password=$password; 
    }
    
}



