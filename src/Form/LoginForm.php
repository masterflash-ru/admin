<?php
namespace Admin\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('login-admin-form');
     
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/admin');
       
        
		$this->addElements();
       // $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "email" field
        $this->add([            
            'type'  => 'text',
            'name' => 'login',
            'options' => [
               // 'label' => 'Login',
            ],
        ]);
        
        // Add "password" field
        $this->add([            
            'type'  => 'password',
            'name' => 'password',
            'options' => [
               // 'label' => 'Password',
            ],
        ]);
        
        
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Submit',
                'id' => 'submit',
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
                
        
        // Add input for "password" field
        $inputFilter->add([
                'name'     => 'password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 6,
                            'max' => 64
                        ],
                    ],
                ],
            ]);     
        
    }        
}

