<?php
// Use the ridiculously long Symfony namespaces
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;
use Carbon\Carbon;

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
$app->view->setTemplatesDirectory("src/templates/".$app->theme);

//PHPMailer configuration
$mail = new PHPMailer();
if($config_phpmailer['type'] == 'smtp'){
  $mail->isSMTP();
  foreach($config_phpmailer['config'] as $key => $value){
    if(!empty($value)){
      $mail->$key = $value;
    }
  }
}
$app->mail = $mail;
if(class_exists('mPDF')){
  $app->pdf = new mPDF();
  $app->pdf->setAutoTopMargin = 'stretch'; 
  $app->pdf->setAutoBottomMargin = 'stretch'; 
}
$translator = new Translator($app->lang, new MessageSelector());
$translator->addLoader('php', new PhpFileLoader());
// Add language files here
$translator->addResource('php', './src/lang/'.$app->lang.'.php', $app->lang);
 
// Twig configuration
$view = $app->view();
$view->parserOptions = array('debug' => true);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new TranslationExtension($translator)
);
$twig_env = $view->getEnvironment();
$user = $app->sentry->getUser();
$isAdmin = false;
if($user){
  $isAdmin = $user->hasAccess('admin');
}
$twig_env->addGlobal('logged_user', $user);
$twig_env->addGlobal('isAdmin', $isAdmin);
$twig_env->addGlobal('app_name', $app->app_name);
$twig_env->addGlobal('lang', $app->lang);

$def_menu = array(
    'Users' => array(
    'permission' => 'admin',
    'url' => 'users',
    'icon' => 'fa-user'
  )
);
$app->menu = array_merge($app->menu,$def_menu);

$twig_env->addGlobal('menu', $app->menu);
