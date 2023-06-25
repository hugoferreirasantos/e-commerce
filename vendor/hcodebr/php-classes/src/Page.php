<?php
//Criando uma class Page:

//Namespace da pasta Hcode:
namespace Hcode;

//Utilizando do namespace do Rian Tpl:
use Rain\Tpl;

//Inicio: Criando a class Page:
class Page {

	//Atributos:
	private $tpl;
	private $options = [];
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];


	//Métodos:

	 //Inicio: Método magico construtor:
	public function __construct($opts = array(), $tpl_dir = "/views/") {

		//Realizando o merge dos arrays:
		$this->options = array_merge($this->defaults, $opts);

		// config
		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]. $tpl_dir,
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]. "/views-cache/",
			"debug"         => false // set to false to improve the speed
		);

		Tpl::configure( $config );

		//Criando uma objeto Tpl:
		$this->tpl = new Tpl();

		$this->setData($this->options["data"]);

		if($this->options["header"] === true)  $this->tpl->draw("header");



	}
	 //fim: Método magico construtor:


	 //Inicio: Método setData:
	private function setData($data = array()){

		foreach($data as $key => $value){

			$this->tpl->assign($key,$value);

		}		

	}
	 //fim: Método setData:


	 //Inicio: Método setTpl:
	public function setTpl($name, $data = array(), $returnHTML = false){

		$this->setData($data);

		//Carregar o template na tela:
		return $this->tpl->draw($name, $returnHTML);



	}


	 //Fim: Método setTpl:



	 //Inicio:Método magico destruct:

	public function __destruct() {

		if($this->options["footer"] === true) $this->tpl->draw("footer");

	 
	}
	 //Fim: Método magico destruct:


}

//Fim: Criando a class Page:





?>