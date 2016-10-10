<?php

$app->get('/:model', function ($model) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = new $class();
    $template = (!is_null($single->template) ? $single->template : 'base_table.html');
    $mod = $single->all();
    $app->render($template,array(
          'model' => $model,
          'page_title' => $class,
          'panel_title' => '',
          'fields' => $single->list_fields,
          'regs' => $mod
          ));
  }else{
    $app->pass();
  }
});

$app->get('/pdf/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $app->response->headers->set('Content-type', 'application/pdf');
    $app->response->write($single->pdf('pdf','S'));
  }else{
    $app->pass();
  }
});

$app->get('/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $single->format();
    $template = (!is_null($single->template_view) ? $single->template_view : 'base_view.html');
    $app->render($template,array(
          'model' => $model,
          'page_title' => $class,
          'panel_title' => '',
          'fields' => $single->view_fields,
          'obj' => $single
          ));
  }else{
    $app->pass();
  }
});

$app->put('/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $single->update($app->request->params());
    $template = (!is_null($single->template_view) ? $single->template_view : 'base_view.html');
    $app->render($template,array(
          'model' => $model,
          'page_title' => $class,
          'panel_title' => '',
          'fields' => $single->view_fields,
          'obj' => $single
          ));
  }else{
    $app->pass();
  }
});

$app->post('/:model', function ($model) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = new $class();
    $single->store($app->request->params());
    $template = (!is_null($single->template) ? $single->template : 'base_table.html');
    $mod = $single->all();
    $app->render($template,array(
          'model' => $model,
          'page_title' => $class,
          'panel_title' => '',
          'fields' => $single->list_fields,
          'regs' => $mod,
          'obj' => $single
          ));
  }else{
    $app->pass();
  }
});

$app->delete('/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $single->delete();
    $app->redirect($app->request->getRootUri().'/'.$model);
  }else{
    $app->pass();
  }
});

$app->get('/', function() use ($app){
  $app->render('dashboard.html', array(
            'page_title' => 'Dashboard',
            'panel_title' => '',
        ));
})->name('home');


$app->get('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $app->render('user.html', array(
              'page_title' => 'User details',
              'panel_title' => $user->email,
              'user' => $user
          ));
})->name('user');

$app->post('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $user->first_name = $app->request->post('first_name');
    $user->last_name = $app->request->post('last_name');
    $user->email = $app->request->post('email');
    $user->save();
    $app->redirect($app->urlFor('users'));
});

$app->get('/users', function () use ($app) {
      $users = $app->sentry->findAllUsers();
      foreach ($users as $key => $user) {
        $users[$key]->edit_path = $app->urlFor('user', array('id' => $user->id));
      }
      $app->render('users.html', array(
              'users' => $users,
              'page_title' => 'Users',
              'panel_title' => 'User list',
          ));
})->name('users');

$app->get('/login',function () use ($app) {
  $app->render('login.html', array(
          'page_title' => 'Login',
      ));
})->setName('login');

$app->get('/logout',function () use ($app) {
  $app->sentry->logout();
  $app->flash('Info', 'You\'ve logged out.');
  $app->redirect($app->urlFor('login'));
})->setName('logout');

$app->post('/auth',function () use ($app) {
  try{
    $user = $app->sentry->authenticate(array('email' => $app->request->params('username'),'password' => $app->request->params('password')));
    if($user){
      $app->redirect($app->urlFor('home'));
    }
  }
  catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
  {
    $app->flash('Error', 'Login field is required.');
    $app->redirect($app->urlFor('login'));
  }
  catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
  {
    $app->flash('Error', 'Password field is required.');
    $app->redirect($app->urlFor('login'));
  }
  catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
  {
    $app->flash('Error', 'Wrong password, try again.');
    $app->redirect($app->urlFor('login'));
  }
  catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
  {
    $app->flash('Error', 'User was not found.');
    $app->redirect($app->urlFor('login'));
  }
  catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
  {
    $app->flash('Error', 'User is not activated.');
    $app->redirect($app->urlFor('login'));
  }
  // The following is only required if the throttling is enabled
  catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e)
  {
    $app->flash('Error', 'User is suspended.');
    $app->redirect($app->urlFor('login'));
  }
  catch (Cartalyst\Sentry\Throttling\UserBannedException $e)
  {
    $app->flash('Error', 'User is banned.');
    $app->redirect($app->urlFor('login'));
  }

})->setName('auth');

$app->post('/lostpassword',function () use ($app) {
  $email = $app->request->params('email');
  try
  {
      // Find the user using the user email address
      $user = $app->sentry->findUserByLogin($email);

      // Get the password reset code
      $resetCode = $user->getResetPasswordCode();

      // Now you can send this code to your user via email for example.
      $app->mail->setFrom('kiko@kikojover.es');
      $app->mail->addAddress($email);
      $app->mail->Subject = "[{$app->app_name}] Reset Password";
      $app->mail->Body = $app->request->getUrl().$app->urlFor('resetpass',array('username' => $email,'passcode' =>  $resetCode));
      $app->mail->send();
      $app->flash('Info','We\'ve sent an email with instrucions');
  }
  catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
  {
    $app->flash('Error','User was not found.');
  }
  $app->redirect($app->urlFor('login'));
})->setName('lostpassword');

$app->get('/resetpassword/:username/:passcode',function ($username,$passcode) use ($app){
  $app->render('resetpassword.html',array(
      'page_title' => 'Reset password',
      'username' => $username,
      'passcode' => $passcode
  ));
})->name('resetpass');

$app->post('/resetpassword',function () use ($app){
  $username = $app->request->post('username');
  $passcode = $app->request->post('passcode');
  $new_password = $app->request->post('password');
  $repeat_password = $app->request->post('repassword');
  if($new_password == $repeat_password){
    try
    {
        // Find the user using the user id
        $user = $app->sentry->findUserByLogin($username);

        // Check if the reset password code is valid
        if ($user->checkResetPasswordCode($passcode))
        {
            // Attempt to reset the user password
            if ($user->attemptResetPassword($passcode, $new_password))
            {
              $app->flash('Info','Password reseted. You can login now');
            }
            else
            {
              $app->flash('Error','Password reset failed. Try again.');
            }
        }
        else
        {
          $app->flash('Error','The provided password reset code is Invalid.');
        }
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
    {
        $app->flash('Error','User was not found.');
    }
  }
  else {
    $app->flash('Error','Password mismatch.');
  }
  $app->redirect($app->urlFor('login'));

})->name('doresetpass');

// $app->get('/init', function (Request $request, Response $response, $args) {
// //   $this->sentinel->getRoleRepository()->createModel()->create(array(
// //       'name'          => 'Admin',
// //       'slug'          => 'admin',
// //       'permissions'   => array(
// //           'user.create' => true,
// //           'user.update' => true,
// //           'user.delete' => true
// //       ),
// //   ));
// //
// //   $this->sentinel->getRoleRepository()->createModel()->create(array(
// //       'name'          => 'User',
// //       'slug'          => 'user',
// //       'permissions'   => array(
// //           'user.update' => true
// //       ),
// //   ));
// //
//   $user = $this->sentinel->register(['email'=>'kiko@kikojover.es','password' => 'kiko']);
//   $role = $this->sentinel->findRoleByName('Admin');
//   $role->users()->attach($user);
//   $activation = new Cartalyst\Sentinel\Activations\IlluminateActivationRepository;
//   $usr_activation = (new Cartalyst\Sentinel\Activations\IlluminateActivationRepository)->create($user);
//   $activation->complete($user,$usr_activation->code);
// });
