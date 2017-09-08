<?php
$lang = array(
    //Notifications
    //ERROR
    'Login field is required.' => 'Es necesario introducir el usuario.',
    'Password field is required.' => 'Es necesario introducir la contraseña.',
    'User was not found.' => 'El usuario no existe',
    'Wrong password, try again.' => 'Contraseña incorrecta, vuelve a probar.',
    
    //INFO
    "You've logged out." => 'Has salido de la aplicación.',
    "We've sent an email with instrucions" => 'Hemos enviado un email con las instrucciones.',
    
    //SUCCESS
    "Success" => "Éxito",
    
    //Login
    'Login Form' => 'Acceso',
    'Log in' => 'Acceso',
    'Submit' => 'Enviar',
    'Username' => 'Usuario',
    'Password' => 'Contraseña',
    'Lost your password?' => 'Recuperar contraseña',
    'Lost password' => 'Recuperar contraseña',
    
    //General
    'close' => 'Cerrar',
    'confirm' => 'Confirmar',
    'New' => 'Nuevo',
    'Users' => 'Usuarios',
    'Profile' => 'Perfil',
    'Log Out' => 'Salir',
    'Home' => 'Inicio',
    'Action' => 'Acción',
    'Create' => 'Crear',
    'Edit' => 'Editar',
    'Delete' => 'Borrar',
    'Deleted' => 'Borrado',
    'Save' => 'Guardar',
    'Saved' => 'Guardado'
);

if(file_exists('src/lang/'.basename(__FILE__,'.php').'_ovr.php')){
    include('src/lang/'.basename(__FILE__,'.php').'_ovr.php');
    $lang = array_merge($lang,$lang_ovr);
}

return $lang;
