<?php
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Yii1 + Vue.js App',

    // Контроллер по умолчанию
    'defaultController' => 'site',

    // Автозагрузка классов моделей и компонентов
    'import' => array(
        'application.models.*',
        'application.components.*',
    ),

    // Компоненты приложения
    'components' => array(
        // База данных SQLite в файле protected/data/todo.db
        'db' => array(
            'connectionString' => 'sqlite:' . dirname(__FILE__) . '/../data/todo.db',
            'tablePrefix' => '',
        ),

        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                'api/<action:\w+>' => 'api/<action>',
            ),
        ),

        // Отключаем проверку CSRF для упрощённого API
        'request' => array(
            'enableCsrfValidation' => false,
        ),
    ),
); 