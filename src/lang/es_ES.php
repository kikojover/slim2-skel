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
    'Reset Password' => 'Reiniciar contraseña',
    'reset_password_title' => 'Reinicio de contraseña',
    'reset_password_text' => 'Hola, has pedido un reinicio de tu contraseña, si has sido tú puedes acceder pinchando en el botón de abajo, "Reiniciar Contraseña", y te llevará a un formulario para cambiarla. Si no has sido tú quien ha pedido el reinicio puede dejar pasar este correo.',
    'reset_password_link' => 'Reiniciar Contraseña',
    'reset_password_alturl' => 'Si el botón no ha funcionado, copia y pega este enlace en tu navegador para acceder:<br />',

    //Users
    'First Name' => 'Nombre',
    'Last Name' => 'Apellidos',
    'New Password' => 'Nueva Contraseña',
    'Repeat Password' => 'Repetir Contraseña',
    'Group' => 'Grupo',
    
    //General
    'sure' => '¿Seguro?',
    'sure_general' => 'Confirma el borrado',
    'delete_general_txt' => '¿Seguro que quieres borrar este elemento?',
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
