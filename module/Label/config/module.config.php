<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Label\Controller\Label' => 'Label\Controller\LabelController',
            'Label\Controller\Translation' => 'Label\Controller\TranslationController'
        )
        
    ),
    
    'router' => array(
        'routes' => array(
            'label' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/label[/:action][/:param]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'param'  => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Label\Controller\Label',
                        'action'     => 'index',
                    ),
                ),
            ),
            
            'translation' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/translation[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Label\Controller\Translation',
                        'action'     => 'showTranslationTable',
                    ),
                ),
            ),
            
        ),
    ),
    
    'view_manager' => array(
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);