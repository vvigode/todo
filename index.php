<?php
// Включаем автозагрузку composer
require_once __DIR__ . '/vendor/autoload.php';

// Путь к файлам фреймворка Yii
$yii = __DIR__ . '/vendor/yiisoft/yii/framework/yii.php';

// Временный режим отладки – отключите в продакшене
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once($yii);

$config = __DIR__ . '/protected/config/main.php';

Yii::createWebApplication($config)->run(); 