<?php
$app->config(array(
    'debug' => true,
    'templates.path' => '/src/templates'
));

// Import the necessary classes
use Illuminate\Database\Capsule\Manager as Capsule;

// Create the Sentry alias
class_alias('Cartalyst\Sentry\Sentry', 'Sentry');

// Create a new Database connection
$capsule = new Capsule;

$capsule->addConnection($config_eloquent);

$capsule->bootEloquent();

$app->sentry = new Sentry();

$app->view = new \Slim\Views\Twig();
$app->view->setTemplatesDirectory("src/templates");

// Twig configuration
$view = $app->view();
$view->parserOptions = array('debug' => true);
$view->parserExtensions = array(new \Slim\Views\TwigExtension());
$twig_env = $view->getEnvironment();
$twig_env->addGlobal('logged_user', $app->sentry->getUser());
$twig_env->addGlobal('app_name', $app_name);
