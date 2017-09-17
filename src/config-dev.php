<?php

$config_eloquent = array(
    'driver'    => 'mysql',
    'host'      => '192.168.1.2',
    'database'  => 'segdades',
    'username'  => 'root',
    'password'  => 'K17y4M93',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
);

$config_phpmailer = array(
    'type' => 'smtp',
    'config' => array(
      'Host' => 'kikojover.es',
      'Port' => '25',
      'Username' => 'kiko@kikojover.es',
      'Password' => 'K17y4M93',
      'SMTPAuth' => 'true',
      'AuthType' => 'PLAIN',
      'SMTPSecure' => 'tls',
      'SMTPDebug' => 3,
    )
);


$app->app_name = 'Slim2 Skeleton';
$app->from_email = 'kiko@kikojover.es';
$app->lang = 'ca_ES';
$app->theme = 'default';

//Add fields to user to extend it
$app->extend_user = array();

$app->menu = array(
  'Home' => array(
    'permission' => 'users',
    'url' => '',
    'icon' => 'fa-home'
  )
);