<?php


namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

define('SECRET_VI', pack('a16', 'HcodePHP_Secret'));
define('SECRET', pack('a16', 'HcodePHP_Secret'));

//Class User:
class User extends Model {

	//Atributos:
	const SESSION = "User";
	//const SECRET = pack('a16', 'HcodePhp_Secret'); //A Chave da função mycrip_encript precisa ter 16 caracteres para dar certo:
	//const SECRET_VI = pack('a16', 'HcodePhp_Secret');
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSucesss";


	//Métodos:

	 //Inicio: Método getFromSession:
	public static function getFromSession()
	{

		$user = new User();

		//Verificar se a sessão já esta definida:
		if(isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0){

			$user->setData($_SESSION[User::SESSION]);

		}	

		return $user;	

	}
	 //Fim: Método getFromSession:

	 //Inicio: Método checkLogin:
	public static function checkLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;

		} else {

			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {

				return true;

			} else if ($inadmin === false) {

				return true;

			} else {

				return false;

			}

		}

	}
	 //Fim: Método checkLogin:



	 //Inicio: Método login:
	public static function login($login, $password)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b ON a.idperson = b.idperson WHERE a.deslogin = :LOGIN", array(
			":LOGIN"=>$login
		)); 

		if (count($results) === 0)
		{
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"]) === true)
		{

			$user = new User();

			$data['desperson'] = utf8_encode($data['desperson']);

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {
			throw new \Exception("Usuário inexistente ou senha inválida.");
		}
	 
	}
	 //Fim: Método login:

	 //Inicio: Método setData:
	public function setData($data = array())
	{

		foreach($data as $key => $value){

			$this->{"set".$key}($value);

		}

	}
	 //Fim: Método setData:

	 //Inicio: Método verifyLogin:
	public static function verifyLogin($inadmin = true)
	{

		if (!User::checkLogin($inadmin)) {

			if ($inadmin) {
				header("Location: /admin/login");
			} else {
				header("Location: /login");
			}
			exit;

		}

	}
	 //Fim: Método verifyLogin:

	 //Inicio: Método logout:
	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	 //Fim: Método logout:

	 //Inicio: Método listAll():
	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}

	 //Fim:: Método listAll():

	 //Inicio: Método save:
	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)",
			array(
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);


	}
	 //Fim: Método save:

	 //Inicio: Método get():
	public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", 
			array(
			":iduser"=>$iduser
		));

		$data = $results[0];

		$data['desperson'] = utf8_encode($data['desperson']);

		$this->setData($data);



	}

	 //Fim: Método get();

	 //Inicio: Método update():
	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson,:deslogin,:despassword,:desemail,:nrphone,:inadmin)",
			array(
			":iduser"=>$this->getiduser(),
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

	}

	 //Fim: Método update():

	 //Inicio: Método delete():

	public function delete()
	{


		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)",array(
			":iduser"=>$this->getiduser()
		));


	}

	 //Fim: Método delete():

	 //Inicio: Método Forgot();
	public static function getForgot($email)
	{

		//Verificar se o email está cadastrado no banco de dados:
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", 
			array(
				":email"=>$email
		));

		//Se a pesquisa não retornou nada:
		if(count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha");

		}else{

			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)",array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if(count($results2) === 0)
			{

				throw new \Exception("Não foi possível recuperar a senha");

			}else{

				$dataRecovery = $results2[0];

				//$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

				$code = base64_encode(openssl_encrypt($dataRecovery["idrecovery"], 'AES-128-CBC', SECRET,0,SECRET_VI));

				//Processo de enviar os dados por um link:

				$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

				//Instânciar um objeto Mailer:

				$mailer = new Mailer($data["desemail"], $data["desperson"], "Redefinir senha da hcode store.", "forgot",array(
					"name"=>$data["desperson"],
					"link"=>$link
				));

				//enviar o email:
				$mailer->send();

				return $data;


			}



		}



	}

	 //Fim: Método Forgot()

	 //Inicio: Método validForgotDecrypt;
	public static function validForgotDecrypt($code)
	{



		$idrecovery = openssl_decrypt(base64_decode($code), 'AES-128-CBC', SECRET,0,SECRET_VI);

		$sql = new Sql();

		$results = $sql->select("
			SELECT * FROM tb_userspasswordsrecoveries 
			a INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE 
			a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL
			AND
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();

		", array(
			":idrecovery"=>$idrecovery
		));

		if(count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha.");

		}else{

			return $results[0];
		}

	}

	 //Fim: Método validForgotDecrypt;

	 //Inicio: Método setForgotUsed();
	public static function setForgotUsed($idrecovery)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));

	}

	 //Fim: Método setForgotUsed();

	 //Inicio: Método setPassword:
	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser",array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));

	}

	 //Fim: Método setPassword:



	public static function setError($msg)
	{

		$_SESSION[User::ERROR] = $msg;

	}

	public static function getError()
	{

		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

		User::clearError();

		return $msg;

	}

	public static function clearError()
	{

		$_SESSION[User::ERROR] = NULL;

	}

	public static function setSuccess($msg)
	{

		$_SESSION[User::SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[User::SUCCESS] = NULL;

	}

	public static function setErrorRegister($msg)
	{

		$_SESSION[User::ERROR_REGISTER] = $msg;

	}

	public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;

	}

	public static function clearErrorRegister()
	{

		$_SESSION[User::ERROR_REGISTER] = NULL;

	}

	public static function getPasswordHash($password){

		return password_hash($password, PASSWORD_DEFAULT,[
			'cost'=>12
		]);

	}


}




?>