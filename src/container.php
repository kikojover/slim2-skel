<?php
// Use the ridiculously long Symfony namespaces
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Translator;

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
$app->pdf = new mPDF();
$app->pdf->setAutoTopMargin = 'stretch'; 
$app->pdf->setAutoBottomMargin = 'stretch'; 
 
$translator = new Translator("es_ES", new MessageSelector());
$translator->addLoader('php', new PhpFileLoader());
// Add language files here
$translator->addResource('php', './src/lang/es_ES.php', 'es_ES'); // Norwegian
 
// Twig configuration
$view = $app->view();
$view->parserOptions = array('debug' => true);
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new TranslationExtension($translator)
);
$twig_env = $view->getEnvironment();
$twig_env->addGlobal('logged_user', $app->sentry->getUser());
$twig_env->addGlobal('app_name', $app->app_name);
