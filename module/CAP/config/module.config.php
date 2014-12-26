<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'log' => array(
        'Log\App' => array(
            'writers' => array(
                array(
                    'name'     => 'stream',
                    'priority' => 1000,
                    'options'  => array(
                        'stream' => 'data/logs/cap.log',
                    ),
                ),
            ),
        ),
    ),
    'router' => array(
        'routes' => array(

            /* restful routes */
            'rest-login' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/login',
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Login',
                    )
                ),
            ),
            'rest-password' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/password',
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Password',
                    )
                ),
            ),

            'rest-customer' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/customer[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Customer',
                    )
                ),
            ),

            'rest-mentee' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/mentee[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Mentee',
                    )
                ),
            ),

            'rest-note' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/note[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Note',
                    )
                ),
            ),

            'rest-mentor' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/mentor[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Mentor',
                    )
                ),
            ),

            'rest-admin' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/admin[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Admin',
                    )
                ),
            ),

            'rest-questionnaire' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/questionnaire[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Questionnaire',
                    )
                ),
            ),


            'rest-dashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/rest/dashboard[/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Dashboard',
                    )
                ),
            ),

            'rest-settings' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/rest/settings',
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Rest\Settings',
                    )
                ),
            ),

            /* template routes */
            'home' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Index',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            ),

            'logout' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/logout',
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Dashboard',
                        'action' => 'logout'
                    )
                ),
            ),

            'dashboard' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/dashboard[/:action][/:id]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Dashboard',
                        'action' => 'index'
                    ),
                ),
                'may_terminate' => true,
            ),

            /* partials */
            'partials-index' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/partials[/:action]',
                    'constraints' => array(
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'CAP\Controller\Partials',
                        'action' => 'index'
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => 'CAP\Service\Factory\AuthenticationFactory',
            'mail.transport'                            => 'CAP\Service\Factory\MailTransportFactory',
            'cap_module_options'                        => 'CAP\Service\Factory\ModuleOptionsFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'CAP\Controller\Index'      => 'CAP\Controller\IndexController',
            'CAP\Controller\Dashboard'  => 'CAP\Controller\DashboardController',
            'CAP\Controller\Partials'   => 'CAP\Controller\PartialsController',

            'CAP\Controller\Rest\Customer'   => 'CAP\Controller\Rest\CustomerController',
            'CAP\Controller\Rest\Mentee'     => 'CAP\Controller\Rest\MenteeController',
            'CAP\Controller\Rest\Mentor'     => 'CAP\Controller\Rest\MentorController',
            'CAP\Controller\Rest\Admin'      => 'CAP\Controller\Rest\AdminController',
            'CAP\Controller\Rest\Note'       => 'CAP\Controller\Rest\NoteController',
            'CAP\Controller\Rest\Questionnaire' => 'CAP\Controller\Rest\QuestionnaireController',
            'CAP\Controller\Rest\Dashboard'  => 'CAP\Controller\Rest\DashboardController',
            'CAP\Controller\Rest\Login'      => 'CAP\Controller\Rest\LoginController',
            'CAP\Controller\Rest\Password'   => 'CAP\Controller\Rest\PasswordController',
            'CAP\Controller\Rest\Settings'   => 'CAP\Controller\Rest\SettingsController',
        ),
    ),
    'view_manager' => array(
         'strategies' => array(
            'ViewJsonStrategy',
         ),

        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'cap/index/index'         => __DIR__ . '/../view/cap/login/index.phtml', //homepage goes to login for now
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(
        'configuration' => array(
            'orm_default' => array(
                'generate_proxies' => true,
            ),
        ),
        'authentication' => array(
            'orm_default' => array(
                'object_manager'      => 'Doctrine\ORM\EntityManager',
                'identity_class'      => 'CAP\Entity\Customer',
                'identity_property'   => 'email',
                'credential_property' => 'password',
                'credential_callable' => 'CAP\Service\UserService::verifyHashedPassword',
            ),
        ),
        'driver' => array(
            'cap_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/CAP/Entity',
                ),
            ),
            'orm_default' => array(
                'drivers' => array(
                    'CAP\Entity' => 'cap_driver',
                ),
            ),
        ),
    ),

    // Placeholder for console routes
    /*
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    */
);
