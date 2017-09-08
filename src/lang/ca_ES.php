<?php
$lang = array(
    //Notifications
    //ERROR
    'Login field is required.' => 'És necessari introduïr l\'usuario.',
    'Password field is required.' => 'És necessari introduïr la contrassenya.',
    'User was not found.' => 'L\'usuari no existeix',
    'Wrong password, try again.' => 'Contrassenya incorrecta, torna a provar.',
    
    //INFO
    "You've logged out." => 'Has eixit de l\'aplicació.',
    "We've sent an email with instrucions" => 'Hem enviat un correu amb les instruccions.',
    
    //SUCCESS
    "Success" => "Èxit",
    
    //Login
    'Login Form' => 'Accés',
    'Log in' => 'Accés',
    'Submit' => 'Enviar',
    'Username' => 'Usuari',
    'Password' => 'Contrassenya',
    'Lost your password?' => 'Recuperar contrassenya',
    'Lost password' => 'Recuperar contrassenya',
    
    //General
    'sure' => 'Segur?',
    'sure_general' => 'Confirma l\'esborrat',
    'delete_general_txt' => 'Segur que vols esborrar aquest element?',
    'close' => 'Tanca',
    'confirm' => 'Confirma',
    'New' => 'Nou',
    'Users' => 'Usuaris',
    'Profile' => 'Perfil',
    'Log Out' => 'Eixir',
    'Home' => 'Inici',
    'Action' => 'Acció',
    'Create' => 'Crear',
    'Edit' => 'Editar',
    'Delete' => 'Esborrar',
    'Deleted' => 'Esborrat',
    'Save' => 'Desar',
    'Saved' => 'Desat'
);

if(file_exists('src/lang/'.basename(__FILE__,'.php').'_ovr.php')){
    include('src/lang/'.basename(__FILE__,'.php').'_ovr.php');
    $lang = array_merge($lang,$lang_ovr);
}

return $lang;
