<?php

$app->get('/', function() use ($app){
  $app->render('dashboard.html', array(
            'page_title' => 'Dashboard',
            'panel_title' => '',
        ));
})->name('home');


$app->get('/users/:id', function ($id) use ($app) {
    $user = $app->sentry->findUserById($id);
    $app->render('user.html', array(
              'page_title' => $user->email,
              'panel_title' => 'User details',
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
          'url_auth' => $app->urlFor('auth')
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
  }catch(\Exception $e){
    $app->flash('Error', 'Wrong username or password.');
    $app->redirect($app->urlFor('login'));
  }

  if($user){
    $app->redirect($app->urlFor('home'));
  }
})->setName('auth');

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
