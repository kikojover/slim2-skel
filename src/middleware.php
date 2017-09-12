<?php

//Auth Middleware
class AuthMiddleware extends \Slim\Middleware
{
    public function call()
    {
        // Get reference to application
        $app = $this->app;

        // // Run inner middleware and application
        $this->next->call();

        $user = $app->sentry->check();
        $req = $app->request;
        if(!$user){
          if(strpos($req->getPath(),'/resetpassword') === false && $req->getPath() != $req->getRootUri().'/lostpassword' && $req->getPath() != $req->getRootUri().'/login' && $req->getPath() != $req->getRootUri().'/auth' && $req->getPath() != $req->getRootUri().'/init'){
              $app->redirect($app->urlFor('login'));
          }
        }

    }
}
$app->add(new \AuthMiddleware());

if(file_exists('src/app_middleware.php')){
    include('src/app_middleware.php');
}
