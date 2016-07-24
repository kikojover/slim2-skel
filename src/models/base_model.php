<?php

class Base_model extends \Illuminate\Database\Eloquent\Model
{
    
  protected $table = '';
  public $list_fields = array();
  public static $user = null;
  
  public function __construct(){
      global $app;
      
      parent::__construct();
      self::$user = $app->sentry->getUser();
  }

}
