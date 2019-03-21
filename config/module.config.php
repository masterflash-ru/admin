<?php
/**
* админка из старой версии системы
* для работоспособности имеет кучу костылей!
* доступ разрешен только для root
 */

namespace Admin;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Cache\Storage\Plugin\Serializer;
use Zend\Cache\Storage\Adapter\Filesystem;


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
                    /*общий интерфейс*/
                    'universal-interface' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/universal-interface/:interface',
                            'constraints' => [
                                'interface' => '[a-zA-Z0-9_-]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\UinterfaceController::class,
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    
                    
                    /*ввод-вывод для jqgrid*/
                    'io-jqgrid' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/io-jqgrid/:interface/:action',
                            'constraints' => [
                                'interface' => '[a-zA-Z0-9_-]+',
                                'action'=>'[a-zA-Z0-9_-]+',
                            ],

                            'defaults' => [
                                'controller' => Controller\JqGridController::class,
                            ],
                        ],
                    ],
                    /*ввод-вывод для jqgrid*/
                    'io-jqgrid-plugin' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/io-jqgrid-plugin/:name',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+',
                            ],

                            'defaults' => [
                                'controller' => Controller\JqGridController::class,
                                'action'     => 'plugin'
                            ],
                        ],
                    ],
                    /*устаревшее*/
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

			//доступ запрещен
            'accessdenied' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/admin/accessdenied',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'accessdenied',
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
            Controller\JqGridController::class => Controller\Factory\JqGridControllerFactory::class,
			
            
            /*устаревшее ПО*/
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
            Controller\UinterfaceController::class =>Controller\UinterfaceController::class,
        ],
	],


	//помощник вывода меню админки
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class => View\Helper\Factory\MenuFactory::class,
            View\Helper\ITabs::class => View\Helper\Factory\ITabsFactory::class,
            View\Helper\IArray::class => View\Helper\Factory\IArrayFactory::class,
            View\Helper\IUniversal::class => View\Helper\Factory\IUniversalFactory::class,
            View\Helper\IJqgrid::class => View\Helper\Factory\IJqgridFactory::class,
        ],
        'aliases' => [
            'adminMenu' => View\Helper\Menu::class,
            'itabs' => View\Helper\ITabs::class,
            'iarray' => View\Helper\IArray::class,
            'iuniversal' => View\Helper\IUniversal::class,
            'ijqgrid' => View\Helper\IJqgrid::class,
        ],
    ],
	
    'service_manager' => [
        'factories' => [//сервисы-фабрики
			Service\GetControllersInfo::class => Service\Factory\GetControllersInfoFactory::class,
            Service\JqGrid\JqGrid::class => Service\JqGrid\Factory\JqGridFactory::class,
            
            Service\JqGrid\PluginManager::class => Service\JqGrid\Factory\PluginManagerFactory::class,
        ],
        'aliases' => [
            'JqGridManager' => Service\JqGrid\PluginManager::class,
        ],
    ],
    

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
       'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
	
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
    
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
        ],
        'aliases' =>[
        ],
    ],
    /*описатели интерфейсов*/
    "interface"=>[
    ]

];
