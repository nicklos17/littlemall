<?php

/**
 * Register application modules
 */
$application->registerModules(array(
    'admin' => array(
        'className' => 'Mall\Admin\Module',
        'path' => __DIR__ . '/../apps/admin/Module.php'
    ),

    'mall' => array(
        'className' => 'Mall\Mall\Module',
        'path' => __DIR__ . '/../apps/mall/Module.php'
    ),

    'mobimall' => array(
        'className' => 'Mall\MobiMall\Module',
        'path' => __DIR__ . '/../apps/mobimall/Module.php'
    ),
));
