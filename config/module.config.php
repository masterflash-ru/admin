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
use Zend\ServiceManager\Factory\InvokableFactory;

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
                    /*ввод-вывод для Zform*/
                    'io-zform' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/io-zform/:interface/:action',
                            'constraints' => [
                                'interface' => '[a-zA-Z0-9_-]+',
                                'action'=>'[a-zA-Z0-9_-]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\ZformController::class,
                            ],
                        ],
                    ],
                    /*ввод-вывод для zform*/
                    'io-zform-plugin' => [
                        'type' => Segment::class,
                        'options' => [
                            'route'    => '/io-zform-plugin/:name',
                            'constraints' => [
                                'name' => '[a-zA-Z0-9_-]+',
                            ],
                            'defaults' => [
                                'controller' => Controller\ZformController::class,
                                'action'     => 'plugin'
                            ],
                        ],
                    ],
                    
                    'admin_menu' => [
                        'type' => Literal::class,
                        'options' => [
                            'route'    => '/admin_menu',
                            'defaults' => [
                                'controller' => Controller\IndexController::class,
                                'action'     => 'amenu'
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
			//для JS и CSS
            'asset' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/:type/admin/[:folder/]:file',
					'constraints' => [
                        'file' => '[a-zA-Z0-9_\-\.]+',
                        'folder' => 'images|font',
                        'type' => 'css|js',
                    ],
                    'defaults' => [
                        'controller' => Controller\AssetController::class,
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
			Controller\ZformController::class => Controller\Factory\ZformControllerFactory::class,
            Controller\EntityController::class => Controller\Factory\EntityControllerFactory::class,
            Controller\BackupRestoreController::class => Controller\Factory\BackupRestoreControllerFactory::class,
            
            /*устаревшее ПО*/
            Controller\ConstructorLineController::class => Controller\Factory\ConstructorLineControllerFactory::class,
			Controller\ConstructorTreeController::class => Controller\Factory\ConstructorTreeControllerFactory::class,
			Controller\LineController::class => Controller\Factory\LineControllerFactory::class,
			Controller\TreeController::class => Controller\Factory\TreeControllerFactory::class,
            Controller\TovarController::class => Controller\Factory\TovarControllerFactory::class,
        ],
    	
		//если у контроллера нет коннструктора или он не нужен или пустой
        'invokables' => [
            Controller\CkeditorController::class => Controller\CkeditorController::class,
            Controller\UinterfaceController::class =>Controller\UinterfaceController::class,
            Controller\AssetController::class => Controller\AssetController::class,
        ],
	],


	//помощник вывода меню админки
    'view_helpers' => [
        'factories' => [
            View\Helper\ITabs::class => View\Helper\Factory\ITabsFactory::class,
            View\Helper\IArray::class => View\Helper\Factory\IArrayFactory::class,
            View\Helper\IUniversal::class => View\Helper\Factory\IUniversalFactory::class,
            View\Helper\IJqgrid::class => View\Helper\Factory\IJqgridFactory::class,
            View\Helper\IZform::class => View\Helper\Factory\IZformFactory::class,
            
            //новые элементы формы, вид
            Service\Zform\Element\View\uploadImg::class => InvokableFactory::class,
        ],
        'aliases' => [
            'itabs' => View\Helper\ITabs::class,
            'iarray' => View\Helper\IArray::class,
            'iuniversal' => View\Helper\IUniversal::class,
            'ijqgrid' => View\Helper\IJqgrid::class,
            'iZform' => View\Helper\IZform::class,
            'izform' => View\Helper\IZform::class,
            
            'uploadImg'=>Service\Zform\Element\View\uploadImg::class,
        ],
    ],
    'service_manager' => [
        'factories' => [//сервисы-фабрики
			Service\GetControllersInfo::class => Service\Factory\GetControllersInfoFactory::class,
            Service\JqGrid\JqGrid::class => Service\JqGrid\Factory\JqGridFactory::class,
            Service\Zform\Zform::class => Service\Zform\Factory\ZformFactory::class,
            Service\JqGrid\PluginManager::class => Service\JqGrid\Factory\PluginManagerFactory::class,
            Service\Zform\PluginManager::class => Service\Zform\Factory\PluginManagerFactory::class,
        ],
        'aliases' => [
            'JqGridManager' => Service\JqGrid\PluginManager::class,
            'ZformManager' => Service\Zform\PluginManager::class,
        ],
    ],
    
    //расширенные элементы для генерации форм
    'form_elements' => [
        'factories' => [
            Service\Zform\Element\Form\uploadImg::class => InvokableFactory::class,
        ],
        'aliases' => [
            'uploadImg'=>Service\Zform\Element\Form\uploadImg::class,
            'uploadimg'=>Service\Zform\Element\Form\uploadImg::class
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
    'view_helper_config' => [
        'asset' => [
            'resource_map' => [
                'backend'=>[
                    'js'=>[
                        "js/admin/jquery-3.1.0.min.js",
                        "js/admin/jquery.cookie.js",
                        "js/admin/jquery-ui.min.js",
                        "js/admin/jquery.jqGrid.min.js",
                        "js/admin/grid.locale-ru.js",
                        "js/admin/context-menu.js",
                        "js/admin/jqgrid-ext.js?".date("d.Y"),
                        "js/admin/jquery-ui-timepicker-addon.min.js",
                        "js/admin/jquery.form.min.js",
                        "js/admin/zform.js?".date("d.Y"),
                        "htmledit/ckeditor.js",
                        "htmledit/adapters/jquery.js",
                        "js/admin/admin_lib.js?".date("d.Y"),
                    ],
                    'css'=>[
                        "css/admin/jquery-ui-timepicker-addon.css",
                        "css/admin/jquery-ui.min.css",
                        "css/admin/jquery-ui.theme.min.css",
                        "css/admin/jquery-ui-1.12.icon-font.min.css",
                        "css/admin/bootstrap.css",
                        "css/admin/ui.jqgrid.css",
                        "css/admin/admin.css",
                    ],
                ],
            ],
        ],
    ],
	//конфигурация хранения дампов
	'backup_folder'=>"data/backup",
    
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
            Service\Admin\JqGrid\Plugin\GetAdminUrls::class=>Service\Admin\JqGrid\Plugin\FactoryGetAdminUrls::class,
        ],
        'aliases' =>[
            "GetAdminUrls"=>Service\Admin\JqGrid\Plugin\GetAdminUrls::class,
        ],
    ],
    /*плагины для интерфейса типа ФОРМА*/
    "ZformPlugin"=>[
        'factories' => [
        ],
        'aliases' =>[
        ],
    ],
    /*описатели интерфейсов*/
    "interface"=>[
        "admin_menu"=>__DIR__."/admin.admin_menu.php",
    ],
    /*доступы к объектам по умолчанию*/
    "permission"=>[
        "objects" =>[
            "Admin\Controller\LoginController/e403"  =>         [1,1,0711],
            "Admin\Controller\LoginController/accessdenied" =>  [1,1,0711],
            "Admin\Controller\AssetController/*" =>             [1,1,0711],
            "Admin\Controller\LoginController/login" =>         [1,1,0711],
            "Admin\Controller\BackupRestoreController/index" => [1,1,0710],
            "Admin\Controller\TreeController/index" =>          [1,1,0710],
            "Admin\Controller\LineController/index" =>          [1,1,0710],
            "Admin\Controller\EntityController/index" =>        [1,1,0710],
            "Admin\Controller\ConstructorLineController/index"=>[1,1,0710],
            "Admin\Controller\ZformController/*" =>             [1,1,0770],
            "Admin\Controller\UinterfaceController/index" =>    [1,1,0770],
            "Admin\Controller\IndexController/*" =>             [1,1,0710],
            "Admin\Controller\JqGridController/*" =>            [1,1,0770]
        ],
    ],

];
