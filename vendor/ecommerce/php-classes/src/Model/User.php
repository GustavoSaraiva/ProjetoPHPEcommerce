<?php

namespace Ecommerce\Model;

use \Ecommerce\DB\Sql;
use \Ecommerce\Model;
use \Ecommerce\Mailer;
use mysql_xdevapi\Exception;

Class User extends Model {

    const SESSION = "User";
    const SECRET = "ProjectPHP7Ecommerce";

    public static function login($login, $password){

    $sql = new Sql();

      $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
             ":LOGIN"=>$login
          ));

      if (count($results) === 0){
          throw new \Exception("Usuário ou senha Inválidos!");
      }

      $data = $results[0];

      if (password_verify($password, $data["despassword"])=== true){

          $user = new User();
          $user ->setValues($data);
          $_SESSION[User::SESSION] = $user->getValues();
          return $user;
          exit;




      }else{
          throw new \Exception("Usuário ou senha Inválidos!");

      }
    }

    public static function verifyLogin($inadmin = true){

        if(
            !isset($_SESSION[User::SESSION])||
            !$_SESSION[User::SESSION]||
            !(int)$_SESSION[User::SESSION]["iduser"] > 0  ||
            (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
        ){
            header("Location: /admin/login");
            exit;
        }
    }


    public static function logout(){
        $_SESSION[User::SESSION] = NULL;
    }

    public static function listAll(){
        $sql = new Sql();

        return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING (idperson) ORDER BY b.desperson");
    }

    public function save(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
            array(
                ":desperson"=>$this->getdesperson(),
                ":deslogin"=>$this->getdeslogin(),
                ":despassword"=>$this->getdespassword(),
                ":desemail"=>$this->getdesemail(),
                ":nrphone"=>$this->getnrphone(),
                ":inadmin"=>$this->getinadmin()
            )
        );

        $this->setValues($results[0]);
    }
    public function get($iduser){

        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(":iduser"=>$iduser));

        $data = $results[0];

        $data['desperson'] = utf8_encode($data['desperson']);
        //var_dump($data);


        $this->setValues($data);
    }

    public function update(){
        $sql = new Sql();

        $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)",
            array(
                ":iduser"=>$this->getiduser(),
                ":desperson"=>$this->getdesperson(),
                ":deslogin"=>$this->getdeslogin(),
                ":despassword"=>$this->getdespassword(),
                ":desemail"=>$this->getdesemail(),
                ":nrphone"=>$this->getnrphone(),
                ":inadmin"=>$this->getinadmin()
            )
        );
        $this->setValues($results[0]);
        //var_dump($results[0]);
    }

    public function isAdmin($data){
        if(isset($data["inadmin"])){
            if($data["inadmin"] === "on"){
                $data["inadmin"] = 1;
                //var_dump($data["inadmin"]);
            }else {
                $data["inadmin"] = 0;
                //var_dump($data["inadmin"]);
            }
        }
        else{
            $data["inadmin"] = 0;
            //var_dump($data["inadmin"]);
        }
        $this->setValues($data);
    }


    public function delete(){
        $sql = new Sql();

        $sql->query("CALL sp_users_delete(:iduser)",array(":iduser"=>$this->getiduser()));
    }


    public static function getForgot($email){
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tb_persons a INNER JOIN tb_users b USING(idperson) WHERE a.desemail = :email", array(":email"=>$email));

        if(count($results) === 0){
            throw new \Exception("Não foi possível recuperar a senha!");
        }
        else{
            $data = $results[0];

            $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(":iduser"=>$data["iduser"], ":desip"=>$_SERVER["REMOTE_ADDR"]));
            //var_dump($results2);

            if(count($results2)=== 0){
                throw new \Exception("Não foi possível recuperar a senha");
            }
            else{
                $dataRecovery = $results2[0];

                $code_encrypted = openssl_encrypt($dataRecovery["idrecovery"],"AES-128-ECB", User::SECRET);
                //var_dump($code_encrypted);
                //mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB)


                $link = "http://www.projeto01ecommerce.com.br/admin/forgot/reset?code=$code_encrypted";
                $mailer = new Mailer($data["desemail"], $data["desperson"],
                    "Redefinir a Senha do ProjectEcommerce", "forgot",
                    array(
                        "name"=>$data["desperson"],
                        "link"=>$link
                    ));
                //var_dump($data["desemail"]);
                $mailer->send();
                //var_dump($mailer);
            }




        }

    }

    public static function validForgotDecrypt($code_encrypted){
        $code_decrypted = openssl_decrypt($code_encrypted,"AES-128-ECB", User::SECRET);
        //var_dump($code_decrypted);
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM
        tb_userspasswordsrecoveries a INNER JOIN tb_users b USING(iduser)
        INNER JOIN tb_persons c USING (idperson) WHERE a.idrecovery = :code_decrypted
        AND a.dtrecovery IS NULL AND
        DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
        ", array(":code_decrypted"=>$code_decrypted));

        if(count($results)===0){
            throw new \Exception("Não foi possível recuperar a senha!");
        }
        else {
            return $results[0];
        }



    }

    public static function setForgotUsed($idrecovery){

        $sql = new Sql();

        $sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery",
            array(":idrecovery"=>$idrecovery));



    }

    public function setPassword($password)
    {
        $sql = new Sql();

        $sql->query("UPDATE tb_users SET despassword = :password WHERE iduser = :iduser", array(":password"=>$password, ":iduser"=>$this->getiduser()));

    }


}


