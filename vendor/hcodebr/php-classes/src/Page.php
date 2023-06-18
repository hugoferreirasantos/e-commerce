<?php

//Namespace:
namespace Hcode;

use Rain\Tpl;

class Page {

	//Atributos:
	private $tpl;
	private $options = [];
	private $defaults = [
		"data" => []
	];


	//Métodos:

	 public function __construct($opts = array()) 
	 {

	 	$this->options = array_merge($this->defaults, $opts);

	 	// config
		$config = array(
			"tpl_dir"  => $_SERVER["DOCUMENT_ROOT"]."/views/",
			"cache_dir" =>$_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"     => false // set to false to improve the speed
			);

		Tpl::configure( $config );

		$this->tpl = new Tpl;

		//Realizando um foreach no options["data"];
		$this->setData($this->options["data"]);

		$this->tpl->draw("header");

	 }

	 
	 private function setData($data = array())
	 {

	 	foreach($data as $key => $value){

			$this->tpl->assign($key,$value);

		}

	 }
	 


	 public function setTpl($name, $data = array(), $returnHTML = false)
	 {

	 	$this->setData($data); //Set Dados


	 	return $this->tpl->draw($name, $returnHTML);


	 }



	 public function __destruct()
	 {

	 	$this->tpl->draw("footer");

	 }



}


?>