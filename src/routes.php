<?php
//Include custom routes first
if(file_exists('src/app_routes.php')){
    include('src/app_routes.php');
}

$app->get('/:model', function ($model) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = new $class();
    $single->params = $app->request->params();
    $template = (!is_null($single->template) ? $single->template : 'base_table.html');
    $mod = array();
    $mod_temp = $single->all();
    foreach ($mod_temp as $value) {
      $value->format();
      $mod[] = $value;
    }
    $relations = array();
    foreach($single->relations as $relation){
        $relations[$relation] = $relation::all();
    }
    $app->render($template,array(
          'model' => $model,
          'page_title' => $class,
          'panel_title' => '',
          'panel_actions' => '',
          'fields' => $single->list_fields,
          'relations' => $relations,
          'regs' => $mod,
          'obj' => $single
          ));
  }else{
    $app->pass();
  }
})->name('list');

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

$app->get('/:model/new', function ($model) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = new $class();
    $relations = array();
    $related = array();
    foreach($single->relations as $relation){
        $relations[$relation] = $relation::all();
        $related[$relation] = $single->$relation;
    }
//    var_dump($relations);exit;
    $columns = $single->attributesToArray();
    if($app->request->isAjax()){
      $app->response->setStatus(200);
      $app->response()->headers->set('Content-Type', 'application/json');
      echo $single->toJson();
    }else{
      $template = (!is_null($single->template_view) ? $single->template_view : 'base_view.html');
      $app->render($template,array(
            'model' => $model,
            'page_title' => $class,
            'panel_title' => $single->panel_title,
            'fields' => $single->view_fields,
            'fields' => $columns,
            'cols' => array_keys($columns),
            'relations' => $relations,
            'related' => $related,
            'obj' => $single,
            'isNew' => true,
            ));
    }
  }else{
    $app->pass();
  }
})->name('new');

$app->get('/:model(/json)/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $relations = array();
    $related = array();
    foreach($single->relations as $relation){
        $relations[$relation] = $relation::all();
        $related[$relation] = $single->$relation;
    }
//    var_dump($relations);exit;
    $columns = $single->attributesToArray();
    $single->format();
    if($app->request->isAjax()){
      $app->response->setStatus(200);
      $app->response()->headers->set('Content-Type', 'application/json');
      echo $single->toJson();
    }else{
      $template = (!is_null($single->template_view) ? $single->template_view : 'base_view.html');
      $app->render($template,array(
            'model' => $model,
            'page_title' => $class,
            'panel_title' => $single->panel_title,
            'fields' => $single->view_fields,
            'fields' => $columns,
            'cols' => array_keys($columns),
            'relations' => $relations,
            'related' => $related,
            'obj' => $single,
            'isNew' => false,
            ));
    }
  }else{
    $app->pass();
  }
})->name('single');

$app->put('/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $single->update($app->request->params());
    if($app->request->isAjax()){
      $app->response->setStatus(200);
      $app->response()->headers->set('Content-Type', 'application/json');
      echo $single->toJson();
    }else{
      $template = (!is_null($single->template_view) ? $single->template_view : 'base_view.html');
      $app->flash('Success', 'Saved');
      $app->redirect($app->urlFor('single',array('model' => $model, 'id' => $id)));
    }
  }else{
    $app->pass();
  }
})->name('update');

$app->post('/:model', function ($model) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = new $class();
    $single->store($app->request->params());
    if($app->request->isAjax()){
      $app->response->setStatus(200);
      $app->response()->headers->set('Content-Type', 'application/json');
      echo $single->toJson();
     }else{
      $app->flash('Success', 'Saved');
      $app->redirect($app->urlFor('single',array('model' => $model, 'id' => $single->id)));
    }
  }else{
    $app->pass();
  }
})->name('store');

$app->delete('/:model/:id', function ($model,$id) use ($app){
  $class = ucwords($model);
  if(class_exists($class)){
    $single = $class::find($id);
    $single->delete();
    if($app->request->isAjax()){
      $app->response->setStatus(200);
      $app->response()->headers->set('Content-Type', 'application/json');
      echo $single->toJson();
    }else{
      $app->flash('Success', 'Deleted');
      $app->redirect($app->request->getRootUri().'/'.$model);
    }
  }else{
    $app->pass();
  }
})->name('delete');

$app->get('/', function() use ($app){
  $app->render('dashboard.html', array(
            'page_title' => 'Dashboard',
            'panel_title' => '',
        ));
})->name('home');


$app->get('/users/new', function () use ($app) {
    $usr = $app->sentry->getUser();
    $admusr = $usr->hasAccess('admin');
    if($admusr){
      $groups = $app->sentry->findAllGroups();
      $app->render('user.html', array(
                'page_title' => 'User details',
                'panel_title' => $user->email,
                'user' => $user,
                'groups' => $groups,
                'isAdmin' => $admusr,
                'isNew' => true,
            ));
    }else{
        $app->redirect($app->urlFor('home'));
    }
})->name('users_new');

$app->get('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $usr = $app->sentry->getUser();
    $admusr = $usr->hasAccess('admin');
    if($admusr || ($user->id == $usr->id)){
      $groups = $app->sentry->findAllGroups();
      $usr_grps = $user->getGroups();
      $usr_groups = array();
      foreach($usr_grps as $usr_group){
        $usr_groups[] = $usr_group->id;
      }
      $app->render('user.html', array(
                'page_title' => 'User details',
                'panel_title' => $user->email,
                'user' => $user,
                'groups' => $groups,
                'usr_groups' => $usr_groups,
                'isAdmin' => $admusr,
                'isNew' => false,
             ));
    }else{
        $app->redirect($app->urlFor('home'));
    }
})->name('user');

$app->post('/users', function () use ($app) {
    $arr_usr = array(
      'first_name' => $app->request->post('first_name'),
      'last_name' => $app->request->post('last_name'),
      'email' => $app->request->post('email'),
      'activated' => 1,
    );
    foreach ($app->extend_user as $field) {
      $arr_usr[$field] = $app->request->post($field);
    }
    if(!empty($app->request->post('password'))){
      if($app->request->post('password') == $app->request->post('repeat-password')){
        $arr_usr['password'] = $app->request->post('password');
      }else{
        $app->flash('Error', 'Passwords don\'t match.');
        $app->redirect($app->urlFor('users_new'));
      }
    }
    $user = $app->sentry->createUser($arr_usr);
    if(!is_null($app->request->post('group'))){
      $new_groups = $app->request->post('group');
      foreach ($new_groups as $new_group) {
        $group = $app->sentry->findGroupById($new_group);
        $user->addGroup($group);
      }
    }
    $app->flash('Success', 'Saved');
    $app->redirect($app->urlFor('user',array('id'=>$user->id)));
});

$app->put('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $user->first_name = $app->request->post('first_name');
    $user->last_name = $app->request->post('last_name');
    $user->email = $app->request->post('email');
    if(!is_null($app->request->post('group'))){
      $old_groups = $user->getGroups();
      foreach ($old_groups as $old_group) {
        $user->removeGroup($old_group);
      }
      $new_groups = $app->request->post('group');
      foreach ($new_groups as $new_group) {
        $group = $app->sentry->findGroupById($new_group);
        $user->addGroup($group);
      }
    }
    foreach ($app->extend_user as $field) {
      if(!is_null($app->request->post($field))){
        $user->$field = $app->request->post($field);
      }
    }
    if(!empty($app->request->post('password'))){
      if($app->request->post('password') == $app->request->post('repeat-password')){
        $user->password = $app->request->post('password');
      }else{
        $app->flash('Error', 'Passwords don\'t match.');
        $app->redirect($app->urlFor('user',array('id'=>$id)));
      }
    }
    $user->save();
    $app->flash('Success', 'Saved');
    $app->redirect($app->urlFor('user',array('id'=>$id)));
});

$app->delete('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $user->delete();
    $app->flash('Success', 'Deleted');
    $app->redirect($app->urlFor('users'));
});

$app->get('/users', function () use ($app) {
    $usr = $app->sentry->getUser();
    $admusr = $usr->hasAccess('admin');
    if($admusr){
      $users = $app->sentry->findAllUsers();
      foreach ($users as $key => $user) {
        $users[$key]->edit_path = $app->urlFor('user', array('id' => $user->id));
      }
      $app->render('users.html', array(
              'users' => $users,
              'page_title' => 'Users',
              'panel_title' => 'User list',
          ));
    }else{
      $app->redirect($app->urlFor('home'));
    }
})->name('users');

$app->get('/login',function () use ($app) {
  // $app->render('mail/forgotpassword.html', array(
  //         'page_title' => 'Login',
  //     ));
  $app->render('login.html', array(
          'page_title' => 'Login',
      ));
})->setName('login');

$app->get('/logout',function () use ($app) {
  $app->sentry->logout();
  $app->flash('Info', 'You\'ve logged out.');
  $app->redirect($app->urlFor('login'));
})->setName('logout');

$app->get('/lostpassword',function () use ($app) {
  $app->render('lostpassword.html', array(
          'page_title' => 'Lost password',
      ));
});

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
      $body = $app->view->fetch('mail/forgotpassword.html', array(
              'user' => $user,
              'url' => $app->request->getUrl().$app->urlFor('resetpass',array('username' => $email,'passcode' =>  $resetCode)),
      ));
      $subject = $app->view->fetch('mail/forgotpassword_subject.html', array(
              'user' => $user,
              'url' => $app->request->getUrl().$app->urlFor('resetpass',array('username' => $email,'passcode' =>  $resetCode)),
      ));
      $app->mail->setFrom($app->from_email);
      $app->mail->addAddress($email);
      $app->mail->Subject = $subject;
      $app->mail->Body = $body;
      $app->mail->IsHTML(true);
      $app->mail->CharSet = 'UTF-8';
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

$app->get('/init', function () use ($app) {
  try{
    $user = $app->sentry->findUserByCredentials(array(
          'email' => 'kiko@kikojover.com',
    ));
  }catch(Cartalyst\Sentry\Users\UserNotFoundException $e){
    try
    {
        $group = $app->sentry->findGroupByName('admin');
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
    {
      $group = $app->sentry->createGroup(array(
          'name'        => 'admin',
          'permissions' => array(
              'admin' => 1,
              'users' => 1,
          ),
      ));
      $group = $app->sentry->createGroup(array(
          'name'        => 'users',
          'permissions' => array(
              'admin' => 0,
              'users' => 1,
          ),
      ));
    }
    $user = $app->sentry->createUser(array(
        'first_name' => 'Kiko',
        'last_name' => 'Jover',
        'email'     => 'kiko@kikojover.com',
        'password'  => 'kiko',
        'activated' => true,
    ));
    $adminGroup = $app->sentry->findGroupByName('admin');

    // Assign the group to the user
    $user->addGroup($adminGroup);

  }
});

