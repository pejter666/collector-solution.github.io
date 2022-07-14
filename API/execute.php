<?php
error_reporting(E_ERROR);

$login = new Login();

switch($_GET["action"]){
    case "registerUser":
        $r = $login->registerUser($_GET["userName"], $_GET["password"], $_GET["repassword"], $_GET["registerKey"]);
        break;
    case "accessAccount":
        $r = $login->accessAccount($_GET["userName"], $_GET["password"], $_GET["registerKey"]);
        break;
    case "generateRegisterKey":
        $r = $login->generateRegisterKey($_GET["adminPassword"]);
        break;
    case "isPremium":
        $r = $login->isPremium($_GET["userName"]);
        break;
	case "IP":
        $r = $login->IP($_GET["userName"], urlencode($_GET["IP"]));
        break;
	 case "hwid":
         $r = $login->hwid($_GET["userName"]);
        break;
	 case "sendhwid":
        $r = $login->sendhwid($_GET["userName"], urlencode($_GET["registerKey"]));
        break;
	 case "Banned":
        $r = $login->Banned($_GET["userName"], $_GET["registerKey"]);
        break;
     case "SelectAPI":
        $r = $login->SelectAPI($_GET["userName"], $_GET["registerKey"]);
        break;
	 case "GETIP":
         $r = $login->GETIP($_GET["userName"]);
        break;
    default:
        $r = "2.0 By Misaki Dev";
}

echo $r;

class Login{
////LOCAL FUNCTION [->]
    private function query($sql, $arg, $fetch = false){
        require "connection.php";
        $q = $db->prepare($sql);
        $q->execute($arg);
        return $fetch ? $q->fetch(2) : $q;
    }

    private function bcrypt($password){
        return password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
    }

    private function userExist($username){
        return $this->query("SELECT accountID FROM apipremium WHERE userName COLLATE latin1_bin LIKE ?", array($username), true)["accountID"];
    }

    private function isBanned($username){
        return $this->query("SELECT isBanned FROM apipremium WHERE accountID = ?", array($this->getAccountID($username)), true)["isBanned"];
    }

    private function getAccountID($username){
        return $this->query("SELECT accountID FROM apipremium WHERE userName COLLATE latin1_bin LIKE ?", array($username), true)["accountID"];
    }
////LOCAL FUNCTION [<-]

////USER FUNCTION [->]
    public function registerUser($username, $password, $repassword, $registerKey){
        if(empty($username) ||empty($password) || empty($registerKey) || empty($repassword)) return "FUCK:MISSING_PARAMETERS";
        if(strlen($username)>20 || strlen($username) < 3) return "FUCK:USERNAME_TOO_SHORT";
        if(strlen($password) < 3) return "FUCK:PASSWORD_TOO_SHORT";   
        if($password != $repassword) return "FUCK:PASSWORDS_NOT_MATCH";        
        $this->query("INSERT INTO apipremium(userName, password) VALUES (?, ?)", array($username, $this->bcrypt($password)));
        return "OK:DONE";
    }

    public function accessAccount($username, $password, $registerKey){ //=login
        if(empty($username) || empty($password) || empty($registerKey)) return "FUCK:MISSING_PARAMETERS";
        if(!$this->userExist($username)) return "FUCK:INVALID_CREDENTIALS";
        if($this->isBanned($username)) return "FUCK:USER_BANNED";
        $pass = $this->query("SELECT password FROM apipremium WHERE userName COLLATE latin1_bin LIKE ?", array($username), true);
        return password_verify($password, $pass["password"]) ? "LOGIN_GOOD:LOGGED_IN" : "FUCK:INVALID_CREDENTIALS";
    }

    public function isPremium($username){
        if(empty($username)) return "FUCK:MISSING_PARAMETERS";
        return $this->query("SELECT isPremium FROM apipremium WHERE accountID  = ?", array($this->getAccountID($username)), true)["isPremium"];
    }
	
	   public function hwid($username){
        if(empty($username)) return "FUCK:MISSING_PARAMETERS";
        $result = $this->query("SELECT whitelist FROM apipremium WHERE accountID  = ?", array($this->getAccountID($username)), true)["whitelist"];
		echo ($result);
    }
	
	    public function sendhwid($username, $registerKey){
         if(empty($username) || empty($registerKey)) return "FUCK:MISSING_PARAMETERS";
		 $this->query("UPDATE apipremium SET whitelist = ? WHERE apipremium.userName = ?", array($registerKey, $username));
		 }
		 
		  public function Banned($username, $registerKey){
         if(empty($username) || empty($registerKey)) return "FUCK:MISSING_PARAMETERS";
		 $this->query("UPDATE apipremium SET isPremium = ? WHERE apipremium.userName = ?", array($registerKey, $username));
		 }

		   public function SelectAPI($username, $registerKey){
         if(empty($username) || empty($registerKey)) return "FUCK:MISSING_PARAMETERS";
		 $this->query("UPDATE apipremium SET API = ? WHERE apipremium.userName = ?", array($registerKey, $username));
		 }
	
	   public function IP($username, $IP){
         if(empty($username) || empty($IP)) return "FUCK:MISSING_PARAMETERS";
		 $this->query("UPDATE apipremium SET IP = ? WHERE apipremium.userName = ?", array($IP, $username));
		 }
		 
		  public function GETIP($username){
        if(empty($username)) return "FUCK:MISSING_PARAMETERS";
        $result = $this->query("SELECT IP FROM apipremium WHERE accountID  = ?", array($this->getAccountID($username)), true)["IP"];
		echo ($result);
    }
    
////USER FUNCTION [<-]

////REGISTER KEY FUNCTION [->]
    public function generateRegisterKey($adminpassword, $size = 10){
        if($adminpassword != "test") return "FUCK:NOT_ENOUGH_PRIVILEGES";
        $exist=false;
        do{
            $alpha = "abcdefhijklmnopqrstuvwxyzABCDEFHIJKLMNOPQRSTUVWXYZ0123456789";
            $key = "";
            for($i = 0; $i<$size; $i++){
                $key .= $alpha[mt_rand(0, strlen($alpha) - 1)];
            }
            if($this->keyExist($key)) $exist = true;
        }while($exist);
        $this->query("INSERT INTO registrationKeys(registerKey) VALUES(?)", array($key));
        return $key;
    }

    private function keyExist($key){
        return $this->query("SELECT registerKey FROM registrationKeys WHERE registerKey COLLATE latin1_bin LIKE ? AND userName IS NULL", array($key), true)["registerKey"];
    }
    
    private function AssignKey($username, $key){
        if(!$this->keyExist($key)) return false;
        $this->query("UPDATE registrationKeys SET userName = ? WHERE registerKey COLLATE latin1_bin LIKE ?", array($username, $key));
        return true;
    }
////REGISTER KEY FUNCTION [<-]
}
					
