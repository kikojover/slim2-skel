<?php

class Presupuesto extends Base_model
{
    
  protected $table = 'presupuestos';
  public $list_fields = array(
      'valor_presupuesto',
      'nombre_empresa'
  );
  
  public static function all(){
      
      return self::where('id_consultor',1)->get();
  }

}
