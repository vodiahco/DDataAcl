<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(

    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
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
    'acl' => array(
        
        /**
         * The route where users are redirected if access is denied.
         * Set to empty array to disable redirection.
         */
        'redirect_route' => array(
            'params' => array(
                'controller' => 'Application\Controller\Index',
                'action' => 'index',
            ),
            'options' => array(
                'name' => 'home',
            ),
        ),
        /**
         * Access Control List
         * -------------------
         */
        'roles' => array(
            'guest' => null,
            'member' => 'guest',
            'admin' => 'member',
        ),
        'resources' => array(
            'allow' => array(
                                'Application\Controller\Index' => array(
                                        'index'        => 'guest',
                                ),
                                'company' => array(
                                        'index'        => 'member',
                                ),
                
                                
            ),
            'deny' => array(
                                'Application\Controller\Index' => array(
                                        'login' => 'member'
                                ),
                               'Application\Controller\Register' => array(
                                        'index' => 'member',
                                ),
            )
        )
    )

);
