<?php


namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;



//Class User:
class Category extends Model {

	

	 //Inicio: Método listAll():
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");

	}
	 //Fim:: Método listAll():

	//Inicio: Método Save();
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory,:descategory)",
			array(
			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory(),
		));

		$this->setData($results[0]);

		Category::updateFile();

	}

	//Fim: Método Save();

	//Inicio: Método Get():
	public function get($idcategory)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory",array(
			":idcategory"=>$idcategory
		));

		$this->setData($results[0]);

	}

	//Fim: Método Get();

	//Inicio: Método Delete();
	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory",array(
			":idcategory"=>$this->getidcategory()
		));

		Category::updateFile();

	}

	//Fim: Método Delete()

	//Inicio: Método para atualizar o arquivo:
	public function updateFile()
	{
		//Pegando tudo que esta no banco de dados das categorias:
		$categories = Category::listAll();

		//Incrementando dinâmicamente o array:
		$html = [];

		foreach($categories as $row){
			array_push($html,'<li><a href="/categories/'.$row["idcategory"].'">'.$row["descategory"].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html", implode('', $html));

	}

	//Fim: Método para atualizar o arquivo:


	//Inicio: Método getProducts:
	public function getProducts($releted = true)
	{

		$sql = new Sql();

		if($releted === true)
		{

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct IN(
					SELECT 
					a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b 
					ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			",[
				":idcategory"=>$this->getidcategory()
			]);

		}else{

			return $sql->select("
				SELECT * FROM tb_products WHERE idproduct NOT IN(
					SELECT 
					a.idproduct
					FROM tb_products a
					INNER JOIN tb_productscategories b 
					ON a.idproduct = b.idproduct
					WHERE b.idcategory = :idcategory
				);
			",[
				":idcategory"=>$this->getidcategory()
			]);

		}



	}

	//Fim: Método getProducts:

	//INICIO: Método getProductsPage:
	public function getProductsPage($page = 1, $itemsPerPage = 8)
	{

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;
		", [
			':idcategory'=>$this->getidcategory()
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>Product::checkList($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}
	//FIM: Método getProductsPage:


	//Inicio: Método addProduct:
	public function addProduct(Product $product)
	{
		$sql = new Sql();

		$sql->query("
			INSERT INTO tb_productscategories(idcategory,idproduct) VALUES(:idcategory,:idproduct)
		",[
			":idcategory" => $this->getidcategory(),
			":idproduct" => $product->getidproduct()
		]);


	}
	//Fim: Método addProduct:


	//Inicio: Método removeProduct:
	public function removeProduct(Product $product)
	{
		$sql = new Sql();

		$sql->query("
			DELETE FROM tb_productscategories WHERE idcategory = :idcategory 
			AND idproduct = :idproduct
		",[
			":idcategory" => $this->getidcategory(),
			":idproduct" => $product->getidproduct()
		]);


	}
	//Fim: Método removeProduct:



	


	
	/*
	//Inicio: Método getProductPage ;
	public function getProductPage($page = 1, $itemsPerPage = 3)
	{
		//Calculo para paginação:
		$start = ($page - 1) * $itemsPerPage ;
		//Calculo para paginação:

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS * FROM tb_products a INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON b.idcategory = c.idcategory
			WHERE c.idcategory  = :idcategory 
			LIMIT $start,$itemsPerPage
		",[
			":idcategory"=>$this->getidcategory()
		]);

		$resultsTotal = $sql->select("
			SELECT FOUND_ROWS() AS nrtotal
		");

		return [
			"data"=>Product::checkList($results),
			"total"=>(int)$resultsTotal[0]["nrtotal"],
			"pages"=>ceil($resultsTotal[0]["nrtotal"] / $itemsPerPage)
		];


	}

	//Fim: Método getProductPage ;
	*/




	 

}




?>