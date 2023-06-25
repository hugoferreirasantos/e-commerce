<?php

namespace Hcode\Model;

//Use:
use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;
use \Hcode\Model\User;


//Class Cart ou Carrinho de compras:
class Cart extends Model {

	//Constante de nome Sessão:
	const SESSION = "Cart";

	//Inicio: Método Estatico getFromSession:
	
	public static function getFromSession()
	{

		$cart = new Cart();

		if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {

			$cart->get((int)$_SESSION[Cart::SESSION]['idcart']);

		} else {

			$cart->getFromSessionID();

			if (!(int)$cart->getidcart() > 0) {

				$data = [
					'dessessionid'=>session_id()
				];

				if (User::checkLogin(false)) {

					$user = User::getFromSession();
					
					$data['iduser'] = $user->getiduser();	

				}

				$cart->setData($data);

				$cart->save();

				$cart->setToSession();


			}

		}

		return $cart;

	}

	//Fim: Método Estatico getFromSession:


	//Inicio: Método setToSession:
	public function setToSession()
	{

		$_SESSION[Cart::SESSION] = $this->getValues();

	}

	//Fim: Método setToSession:



	//Inicio: Método getFromSessionID:
	public function getFromSessionID()
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_cart WHERE dessessionid = :dessessionid" ,[
			":dessessionid"=>session_id()
		]);

		if (count($results) > 0){

			$this->setData($results[0]);

		}

	}

	//Fim: Método getFromSessionID:




	//Inicio: Método get:
	public function get(int $idcart)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_cart WHERE idcart = :idcart" ,[
			":idcart"=>$idcart
		]);

		if (count($results) > 0){

			$this->setData($results[0]);

		}

	}

	//Fim: Método get:


	//Inicio: Método Save:
	public function save()
	{

		$sql = new Sql();


		$results = $sql->select("CALL sp_carts_save(:idcart, :dessessionid, :iduser, :deszipcode, :vlfreight,:nrdays)",
			[
				":idcart"=>$this->getidcart(),
				":dessessionid"=>$this->getdessessionid(),
				":iduser"=>$this->getiduser(),
				":deszipcode"=>$this->getdeszipcode(),
				":vlfreight"=>$this->getvlfreight(),
				":nrdays"=>$this->getnrdays()

			]);

		$this->setData($results[0]);



	}
	//Fim: Método Save:


	//Inicio: addProduct:
	public function addProduct(Product $product)
	{

		$sql = new Sql();

		$sql->query("INSERT INTO tb_cartsproducts (idcart, idproduct) VALUES(:idcart, :idproduct)", [
			":idcart"=>$this->getidcart(),
			":idproduct"=>$product->getidproduct()
		]);


	}
	//Fim: addProduct:

	//Inicio: removeProduct:
	public function removeProduct(Product $product, $all = false )
	{

		$sql = new Sql();



		//Verificar se o usuário quer remover todos os produtos:
		if($all){

			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct AND dtremoved IS NULL",[
				":idcart" =>$this->getidcart(),
				":idproduct"=>$product->getidproduct()
			]);

		}else{

			$sql->query("UPDATE tb_cartsproducts SET dtremoved = NOW() WHERE idcart = :idcart AND idproduct = :idproduct  AND dtremoved IS NULL LIMIT 1",[
				":idcart" =>$this->getidcart(),
				":idproduct"=>$product->getidproduct()
			]);
		}



	}

	//Fim: removeProduct:

	//Inicio: getProducts:
	public function getProducts()
	{


		$sql = new Sql();

		$rows = $sql->select("
		SELECT b.idproduct, b.desproduct , b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl, COUNT(*) AS nrqtd, SUM(b.vlprice) AS vltotal 
			FROM tb_cartsproducts a 
			INNER JOIN tb_products b ON a.idproduct = b.idproduct 
			WHERE a.idcart = 10 AND a.dtremoved IS NULL 
			GROUP BY b.idproduct, b.desproduct , b.vlprice, b.vlwidth, b.vlheight, b.vllength, b.vlweight, b.desurl 
			ORDER BY b.desproduct
		",[
			":idcart"=>$this->getidcart()
		]);



		return Product::checkList($rows);

	}

	//Fim: getProducts



}


?>