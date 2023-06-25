<?php
//Método que vai criar gets and sets para as outras classe que extenderem desta:

namespace Hcode;

class Model {
	//Atributos:
	private $values = [];

	//Métodos:

	public function __call($name, $args)
	{

		$method = substr($name,0,3);
		$fildName = substr($name, 3, strlen($name));

		//var_dump($method, $fildName);
		//exit;

		//Aplicando um switch case para tratar as informações:
		switch($method)
		{
			case "get":
				return (isset($this->values[$fildName])) ? $this->values[$fildName] : NULL;
			break;

			case "set":
				$this->values[$fildName] = $args[0];
			break;
		}

	}

	//Inicio: Método setData:
	public function setData($data = array())
	{

		foreach($data as $key => $value){

			$this->{"set".$key}($value);

		}

	}
	 //Fim: Método setData:

	public function getValues()
	{

		return $this->values;

	}


}


?>