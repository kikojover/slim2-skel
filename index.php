<?php
session_start();

require __DIR__.'/vendor/autoload.php';

$app = new \Slim\Slim();

include_once __DIR__.'/src/config.php';

include_once __DIR__.'/src/container.php';

include_once __DIR__.'/src/middleware.php';

include_once __DIR__.'/src/routes.php';
// var_dump($app->view,$app->container);exit;
// $app->sentinel->getRoleRepository()->createModel()->create(array(
//     'name'          => 'Admin',
//     'slug'          => 'admin',
//     'permissions'   => array(
//         'user.create' => true,
//         'user.update' => true,
//         'user.delete' => true
//     ),
// ));
//
// $app->sentinel->getRoleRepository()->createModel()->create(array(
//     'name'          => 'User',
//     'slug'          => 'user',
//     'permissions'   => array(
//         'user.update' => true
//     ),
// ));

$app->run();
