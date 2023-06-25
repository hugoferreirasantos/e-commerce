<?php


namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;



//Class User:
class Product extends Model {

	

	 //Inicio: Método listAll():
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

	}
	 //Fim:: Método listAll():

	//Inicio: Método checkList():
	public static function checkList($list)
	{

		foreach($list as &$row){
			$p = new Product();
			$p->setData($row);
			$row = $p->getValues();

		}

		return $list;

	}
	//Fim: Método checkList():

	


	//Inicio: Método Save();
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct,:desproduct,:vlprice,:vlwidth,:vlheight,:vllength,:vlweight,:desurl)",
			array(
			":idproduct"=>$this->getidproduct(),
			":desproduct"=>$this->getdesproduct(),
			":vlprice"=>$this->getvlprice(),
			":vlwidth"=>$this->getvlwidth(),
			":vlheight"=>$this->getvlheight(),
			":vllength"=>$this->getvllength(),
			":vlweight"=>$this->getvlweight(),
			":desurl"=>$this->getdesurl()
		));

		$this->setData($results[0]);

		

	}

	//Fim: Método Save();

	//Inicio: Método Get():
	public function get($idproduct)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct",array(
			":idproduct"=>$idproduct
		));

		$this->setData($results[0]);

	}

	//Fim: Método Get();

	//Inicio: Método Delete();
	public function delete()
	{

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct",array(
			":idproduct"=>$this->getidproduct()
		));

		

	}
	//Fim: Método Delete()

	//Inicio: Método checkPhoto() Verifica se a foto existe:
	public function checkPhoto()
	{

		if(file_exists($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
			"res".DIRECTORY_SEPARATOR.
			"site".DIRECTORY_SEPARATOR.
			"img".DIRECTORY_SEPARATOR.
			"products".DIRECTORY_SEPARATOR.
			$this->getidproduct() . ".jpg"
		)){

			$url =  "/res/site/img/products/". $this->getidproduct() . ".jpg";
		}else{

			$url =  "/res/site/img/products.jpg";
		}

		return $this->setdesphoto($url);

	}
	//Fim: Método checkPhoto():

	//Inicio: Método getValues();
	public function getValues()
	{
		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}
	//Fim: Método getValues()

	//Inicio: Método setPhoto();
	public function setPhoto($file)
	{

		$extension = explode('.',$file['name']);
		$extension = end($extension);

		switch($extension){

			case "jpg":
			case "jpeg":
			$image = imagecreatefromjpeg($file['tmp_name']);
			break;

			case "gif":
			$image = imagecreatefromgif($file['tmp_name']);
			break;

			case "png":
			$image = imagecreatefrompng($file['tmp_name']);
			break;

		}

		$dist = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.
			"res".DIRECTORY_SEPARATOR.
			"site".DIRECTORY_SEPARATOR.
			"img".DIRECTORY_SEPARATOR.
			"products".DIRECTORY_SEPARATOR.
			$this->getidproduct() . ".jpg";

		imagejpeg($image,$dist);

		imagedestroy($image);

		$this->checkPhoto();



	}

	//Fim: Método setPhoto();

	//Inicio: Método getFromURL:
 	public function getFromURL($desurl)
	{
		$sql = new Sql();

		$row = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1",[
			'desurl'=>$desurl
		]);

		$this->setData($row[0]);


	}

	//Fim: Método getFromURL:

	//Inicio: Método getCategories:
	public function getCategories()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories a INNER JOIN tb_productscategories b  ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct", 
		[
			'idproduct'=>$this->getidproduct()
		]);

	}

	//Fim: Método getCategories:

	

	 

}






?>