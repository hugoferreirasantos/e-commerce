<?php

//Namespace:
namespace Hcode;

class Model {

	//Atributos:
	private $values = [];


	//Métodos:
	 public function __call($name, $args)
	 {

	 	$method = substr($name,0,3);
	 	$fildName = substr($name,3, strlen($name));

	 	//var_dump($method, $fildName);
	 	//exit;

	 	switch ($method){

	 		case "get":
	 			return $this->values[$fildName];
	 		break;

	 		case "set":
	 			$this->values[$fildName] = $args[0]; 
	 		break;



	 	}

	 }

	  public function setData($data = array())
	  {

	  	foreach($data as $key => $value){

	  		$this->{"set".$key}($value);

	  	}

	  }

	  public function getValues()
	  {

	  	return $this->values;

	  }



}


?>