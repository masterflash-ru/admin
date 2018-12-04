<?php
/**
* админка из старой версии системы
* для работоспособности имеет кучу костылей!
* доступ разрешен только для root
 */

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

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
							'entitygenerator' => [
								'type' => Literal::class,
								'options' => [
									'route'    => '/entity',
									'defaults' => [
										'controller' => Controller\EntityController::class,
										'action'     => 'index',
									],
								],
							],
                    
							'tovar_category_parameters' => [
								'type' => Literal::class,
								'options' => [
									'route'    => '/tovar_category_parameters',
									'defaults' => [
										'controller' => Controller\TovarController::class,
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
			Controller\EntityController::class => Controller\Factory\EntityControllerFactory::class,
            Controller\TovarController::class => Controller\Factory\TovarControllerFactory::class,
        ],
    	
		//если у контроллера нет коннструктора или он не нужен или пустой
        'invokables' => [
           Controller\CkeditorController::class => Controller\CkeditorController::class,
			
        ],
        /*доступы для контроллеров, запуск* /
        'permission' => [
            Controller\IndexController::class=>[
                "index" => [1,1,0710],
            ],
        ],*/
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
			Service\GetControllersInfo::class => Service\Factory\GetControllersInfoFactory::class,
			
        ],
    ],

   
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
	
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
    // Настройка кэша.
    'caches' => [
        'DefaultSystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    'cache_dir' => './data/cache',
                    'ttl' => 60*60*2 
                ],
            ],
            'plugins' => [
                [
                    'name' => Serializer::class,
                    'options' => [
                    ],
                ],
            ],
        ],
    ],

];
