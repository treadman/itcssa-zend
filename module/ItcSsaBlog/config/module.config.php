<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ItcSsaBlog;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\Router\Http\Regex;
use Zend\ServiceManager\Factory\InvokableFactory;
use ItcSsaBlog\Route\StaticRoute;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;


return [
    'router' => [
        'routes' => [
            'blog' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/blog',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'posts' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/posts[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*'
                    ],
                    'defaults' => [
                        'controller'    => Controller\PostController::class,
                        'action'        => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\PostController::class => Controller\Factory\PostControllerFactory::class,
        ],
    ],
    'access_filter' => [
        'options' => [
            'mode' => 'restrictive'
        ],
        'controllers' => [
            Controller\IndexController::class => [
                // Allow anyone
                ['actions' => ['index'], 'allow' => '*'],
                // Allow only authorized users
                ['actions' => [], 'allow' => '@']
            ],
            Controller\PostController::class => [
                // Allow anyone
                ['actions' => ['view'], 'allow' => '*'],
                // Allow only authorized users
                ['actions' => [], 'allow' => '@']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\PostManager::class => Service\Factory\PostManagerFactory::class,
        ],
    ],
    // The following registers our custom view 
    // helper classes in view plugin manager.
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
];
