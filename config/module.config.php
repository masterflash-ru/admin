<?php
/**
админка из старой версии системы
для работоспособности имеет кучу костылей!
 */

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Authentication\AuthenticationService;


return [
	//маршруты
    'router' => [
        'routes' => [
			
			//форма входа
            'adm' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/adm',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
				'may_terminate' => true,
				'child_routes' => [
							
							'line' => [
								'type' => Segment::class,
								'options' => [
									'route'    => '/line/[:table]',
									'constraints' => [
                               			 'table' => '[a-zA-Z0-9_-]+',
                           			 ],
									'defaults' => [
										'controller' => Controller\LineController::class,
										'action'     => 'index',
									],
								],
							],
							'tree' => [
								'type' => Segment::class,
								'options' => [
									'route'    => '/tree/[:table]',
									'constraints' => [
                               			 'table' => '[a-zA-Z0-9_-]+',
                           			 ],
									'defaults' => [
										'controller' => Controller\TreeController::class,
										'action'     => 'index',
									],
								],
							],

							'constructorline' => [
								'type' => Literal::class,
								'options' => [
									'route'    => '/constructorline',
									'defaults' => [
										'controller' => Controller\ConstructorLineController::class,
										'action'     => 'index',
									],
								],
							],
							'constructortree' => [
								'type' => Literal::class,
								'options' => [
									'route'    => '/constructortree',
									'defaults' => [
										'controller' => Controller\ConstructorTreeController::class,
										'action'     => 'index',
									],
								],
							],
							'backuprestore' => [
								'type' => Literal::class,
								'options' => [
									'route'    => '/backuprestore',
									'defaults' => [
										'controller' => Controller\BackupRestoreController::class,
										'action'     => 'index',
									],
								],
							],
							'getconfig' => [
								'type' => Segment::class,
								'options' => [
									'route'    => '/getconfig/[:name]',
									'constraints' => [
                               			 'name' => '[a-zA-Z0-9_-]+',
                           			 ],
									'defaults' => [
										'controller' => Controller\GetConfigController::class,
										'action'     => 'index',
									],
								],
							],

				],//'child_routes'
			],
			
			
			//форма входа
            'admin1' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
			//форма входа
            'admin' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'login',
                    ],
                ],
			],			
			//собственно вход (авторизация)
			'admin_dologin' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/doadmin',
					'verb' => 'post',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'dologin',
                    ],
                ],
            ],

			//ошибка 403
            'admin403' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/403',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'e403',
                    ],
                ],
			],			

			//специально для F41 поля
            'ckeditorf41' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/ckeditorf41/:field',
					'constraints' => [
                             'field' => '[a-zA-Z0-9_\-]+',
                    ],

                    'defaults' => [
                        'controller' => Controller\CkeditorController::class,
                        'action'     => 'index',
                    ],
                ],
			],			
       
	    ],
    ],
	//контроллеры
    'controllers' => [
        'factories' => [
		
			//если мы используем нашу фабрику вызова, класс должен включать интерфейс FactoryInterface
			Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,	
			Controller\LoginController::class => Controller\Factory\LoginControllerFactory::class,
			Controller\ConstructorLineController::class => Controller\Factory\ConstructorLineControllerFactory::class,
			Controller\ConstructorTreeController::class => Controller\Factory\ConstructorTreeControllerFactory::class,
			Controller\LineController::class => Controller\Factory\LineControllerFactory::class,
			Controller\TreeController::class => Controller\Factory\TreeControllerFactory::class,
			Controller\BackupRestoreController::class => Controller\Factory\BackupRestoreControllerFactory::class,
        ],
    	
		//если у контроллера нет коннструктора или он не нужен или пустой
        'invokables' => [
           Controller\CkeditorController::class => Controller\CkeditorController::class,
			
        ],
	],
	//плагины контроллеров, грубоговоря это дополнительные перегруженные методы внутри контроллера
    'controller_plugins' => [
		//фабрики плагинов
        'factories' => [
           Controller\Plugin\AccessPlugin::class => Controller\Plugin\Factory\AccessPluginFactory::class,
           //Controller\Plugin\CkeditorPlugin::class => Controller\Plugin\Factory\CkeditorFactory::class,
        ],
		//краткое обращение внутри конроллера, например $this->access
        'aliases' => [
            'access' => Controller\Plugin\AccessPlugin::class,
            'currentUser' => Controller\Plugin\CurrentUserPlugin::class,
        ],
    ],
	//помощник вывода меню админки
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
        ],
        'aliases' => [
            'adminMenu' => View\Helper\Menu::class,
        ],
    ],
	
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
			Service\RbacManager::class => Service\Factory\RbacManagerFactory::class,
			Service\GetControllersInfo::class => Service\Factory\GetControllersInfoFactory::class,
			
        ],
    ],

   
   
        /* Determine mode - 'restrictive' (default) or 'permissive'. 
		всем, если мы поставим звездочку (*);
		любому аутентифицированному пользователю, если мы поставим коммерческое at (@);
		конкретному аутентифицированному пользователю с заданным адресом эл. почты личности, если мы поставим (@identity)
		любому аутентифицированному пользователю с заданной привилегией, если мы поставим знак плюса и имя привилегии (+permission).
*/

    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed 
            // under the 'access_filter' config key, and access is denied to any not listed 
            // action for not logged in users. In permissive mode, if an action is not listed 
            // under the 'access_filter' key, access to it is permitted to anyone (even for 
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],

        'controllers' => [
            Controller\IndexController::class => [
                //разрешение для входа
                ['actions' => '*', 'allow' => '+admin.login'],
            ],
            Controller\ConstructorLineController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\ConstructorTreeController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
	        Controller\TreeController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\BackupRestoreController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\LineController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
            Controller\CkeditorController::class => [
                //допуски
                ['actions' => '*', 'allow' => '+admin.login']
            ],
			
        ]
    ],


    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
	
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
	
];
