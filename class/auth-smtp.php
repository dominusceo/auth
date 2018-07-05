<?php
	class AuthSMTP extends ConnectLdap{
		var $user;
		var $password;
		var $conn;
		function __construct(){
			$this->conn=ConnectLdap::__construct();
		}
		/* Author: Ricardo Carrillo
		 * @date : jue mar  5 11:35:08 CST 2015
		 * @goal : Look up possible users provided by user form. 
		 */
		function findUser($attribute = "uid", $value = "*", $baseDn = "ou=people,dc=ife.org.mx") {
    			$this->result=ldap_search($this->conn, $baseDn, $attribute . '=' . $value);
    			if ($this->result){
        			//if the result contains entries with surnames, sort by surname: 
        			ldap_sort($this->conn, $this->result, "sn");
        			return ldap_get_entries($this->conn, $this->result);
    			}
		}
		/* Author : Ricardo Carrillo
		 * @date  : jue mar  5 10:25:42 CST 2015
		 * @goal  : get Attr from $user, default: most important attributes from $user.
		 */
		function getAttr($user,$attrName,$attr=array("dn","uid","cn","givenName","mail","mailHost","homeDirectory")){
			if(isset($attrName) && $attrName != ""){
				$element=array();
				array_push($element,$attrName);
				$this->result = ldap_search($this->conn, $this->baseDn,"uid=$user", $element) or die ("Error in search query");
				$info = ldap_get_entries($this->conn, $this->result);
				#print_r($element);
				for ($i=0; $i<=$info["count"]; $i++){
					for ($j=0;$j<=@$info[$i]["count"];$j++){
						@$val=$info[$i][$j];
    						$attResult=@$info[$i][$val][$j];
						return $attResult;
					}
				}
			}
		}
		/* Author: Ricardo Carrillo
			 * @date : jue mar  5 11:35:08 CST 2015
			 * @goal : Look up possible users provided by user form. 
			 */
		public function getDataUserLdap($attribute = "uid",$value="",$attrs=array("cn","mail","mailHost", "homeDirectory","uid","idestado","iddistrito")){
			//natcasesort($attrs);
			$this->result = ldap_search($this->conn, $this->baseDn,"($attribute=$value)", $attrs) or die ("Error in search query");
			if ($this->result){
				//if the result contains entries with surnames, sort by surname: 
				ldap_sort($this->conn, $this->result, "cn");
				$info = ldap_get_entries($this->conn, $this->result);
				return $info;
			}else
				return false;
		}
		public function getSpecificDataUserLdap($field="",$usuario=""){
			$this->user=strtolower($usuario);
			$this->ldapField=strtolower($field);
			$this->dataUser=$this->getDataUserLdap("uid",$this->user);
			$this->titulos=array("cn"=>"Nombre(s)","mail"=>"Cuenta de correo","mailhost"=>"Servidor de Correo","homedirectory"=>"Buz&oacute;n","uid"=>"Login","idestado"=>"Clave Estado","iddistrito"=>"Clave Distrito");
			if($this->dataUser["count"]>0){
				$usuarios=array();
				for($i=0;$i<$this->dataUser["count"];$i++){
					for ($j=0;$j<$this->dataUser[$i]["count"];$j++){
							$att=$this->dataUser[$i][$j];
							$datos[$att]=$this->dataUser[$i][$att][0];
							//echo "($i)($j) ".$att.":".($this->dataUser[$i][$att][0])."<br>";
					}
					array_push($usuarios,$datos);
				}
				for($z=0;$z<count($usuarios);$z++){
					foreach($this->titulos as $key => $value){
						if($this->ldapField=="idestado"){
				    		return $usuarios[$z]["idestado"];
						}elseif($this->ldapField=="iddistrito"){
							return $usuarios[$z]["iddistrito"];
						}elseif($this->ldapField=="mail"){
							list($login,$dominio) =split("@", $usuarios[$z][$key]);
							$usuarios[$z]["uid"]=$login;
							return $usuarios[$z]["uid"];
						}elseif($key==$this->ldapField){
				    		return $usuarios[$z][$key];
						}
					}
				}
				return false;
			}
		  	return false;
		}
		function getDN($user){
			$element=array();
			array_push($element,"uid");
			$this->result = ldap_search($this->conn, $this->baseDn, "uid=$user", $element) or die ("Error in search query");
			$info = ldap_get_entries($this->conn, $this->result);
			for ($i=0; $i<=$info["count"]; $i++){
				@$attResult=$info[$i]["dn"];
				return $attResult;
			}
		}
		/* Author: Ricardo Carrillo
		 * @date : jue mar  5 10:25:42 CST 2015
		 * @goal : determining $user existence into ldap server
		 */
		function userExist($user){
			if(isset($user)){
  				$this->user=AuthSMTP::getAttr($user,"uid");
				if(isset($this->user) && $this->user !=""){
					return true;
				}
				return false;
			}
			return false;
		}
		/* Author : Ricardo Carrillo
		 * @date  : jue mar  5 16:58:15 CST 2015
		 * @goal  : detect $user dn based on their ldap entry
		 */
		function identifyUser($user){
			if(AuthSMTP::userExist($user)){
				$det=AuthSMTP::getDN($user);
				$elements = preg_split('/,/', $det, -1, PREG_SPLIT_NO_EMPTY);
				$elements=array_map("strtolower", $elements);
				$roots= preg_split('/=/', $elements[1], -1, PREG_SPLIT_NO_EMPTY);
				switch ($roots[1]) {
					case 'people':
							$toor="p";
						break;
					case 'generica':
							$toor="g";
						break;
					case 'externo':
							$toor="e";
						break;
					default:
							$toor="s";
						break;
				}
				return $toor;
			}
			return false;
		}

		function bindUser($conn,$ldapUserRoot,$password){
			if(isset($conn) && $conn!="" && isset($ldapUserRoot) && $ldapUserRoot!="" && isset($password) && $password!=""){
				$this->ldapbind = ldap_bind($conn, $ldapUserRoot, $password);
				if ($this->ldapbind) {
					return true;
				} else {
					return false;
				}
			}
        	return false;
		}
		/* Author: Ricardo Carrillo
		 * @date : vie mar  6 10:04:25 CST 2015
		 * @goal : determining user password based on $user and $password variables.
		 * @based: Based on http://blog.michael.kuron-germany.de/2012/07/hashing-and-verifying-ldap-passwords-in-php/
		 */
		function checkPassword($user,$password){
			if($this->bindUser($this->conn, $this->ldapUser,$this->ldappass)){
				$this->password=$this->getAttr($user,"userPassword");
				if(isset($password) && isset($this->password)){
					if($this->password == '') {
						 //echo "No password";
						 return FALSE;
 					}
					// plaintext password
					if($this->password{0} != '{') {
						if ($password == $this->password){
 							return TRUE;
						}else{
							return FALSE;
 						}
					// if is a "crypt" hash password 
					}elseif(substr($this->password,0,7) == '{crypt}' || substr($this->password,0,7) == '{CRYPT}'){
 						if (crypt($password, substr($this->password,7)) == substr($this->password,7)){
							return TRUE;
						}else{
 							return FALSE;
						}
					// if is a "MD5" hash password 
					}elseif(substr($this->password,0,5) == '{MD5}' || substr($this->password,0,5) == '{md5}'){
 						#$encrypted_password = '{MD5}' . base64_encode(md5( $password,TRUE));
 						$encrypted_password = substr($this->password,0,5) . base64_encode(md5( $password,TRUE));
					// if is a "SHA1" hash password
					}elseif (substr($this->password,0,6) == '{SHA1}'){
						 #$encrypted_password = '{SHA}' . base64_encode(sha1( $password, TRUE ));
						 $encrypted_password = substr($this->password,0,6) . base64_encode(sha1( $password, TRUE ));
					// if is a "SSHA" hash password
					}elseif (substr($this->password,0,6) == '{SSHA}'){
 						$salt = substr(base64_decode(substr($this->password,6)),20);
						#$encrypted_password = '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ). $salt);
						$encrypted_password = substr($this->password,0,6) . base64_encode(sha1( $password.$salt, TRUE ). $salt);

					// if is a unsupported hash format got it above 
					}else{
 						// Unsupported password hash format
 						return FALSE;
 					}
					// only comparing

					if($this->password==$encrypted_password){
						return TRUE;
					}else{
						return FALSE;
					}
				}

			}else{
				// in case the bind user were wrong (bad credentials)
				return false;
			}
		}
		function authUser($user,$password){
			if($this->userExist($user)){
				if($this->checkPassword($user,$password)){
					return true;
				}
				return false;
			}
			return false;
		}
		/* Author: Ricardo Carrillo
		 * @date : jue mar  5 10:25:42 CST 2015
		 * @goal : Determining status auth based on checkPassword and userExist methods.
		 *  This will be used into auth login form.
		 */
		function setFail(){
  			header("Auth-Status: Invalid login or password");
			#header("Auth-Wait: 3");
			#header("Auth-Error-Code: 535 5.7.8");
  			exit;
		}
	}
?>
