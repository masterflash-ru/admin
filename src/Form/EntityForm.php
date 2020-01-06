<?php
namespace Admin\Form;

use Laminas\Form\Form;

class EntityForm extends Form
{
    /**
     * Constructor.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct('entity-admin-form');
     
        
        $this->setAttribute('method', 'post');
        $this->setAttribute('action', '/adm/entity');
       
        
        $this->addElements();
    }
    
    protected function addElements() 
    {
        $this->add([            
            'type'  => 'textarea',
            'name' => 'sql',
            'attributes' => [                
                'cols' => '100',
                'rows' => '10',
                'style'=>'font-size:1rem'
            ],

            'options' => [
                'label' => 'Запрос SQL:',
            ],
        ]);
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Получить код',
                'id' => 'submit',
            ],
        ]);
    }
    
}

