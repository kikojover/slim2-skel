<?php

class Base_model extends \Illuminate\Database\Eloquent\Model
{
    
  protected $table = '';
  public $list_fields = array();
  public static $user = null;
  public $template = null;
  public $template_view = null;
  public $params = null;
  
  public function __construct(){
      global $app;
      
      parent::__construct();
      self::$user = $app->sentry->getUser();
  }

  public function format(){
  }

}
