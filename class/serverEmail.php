<?php
/* Author : Ricardo David Carrillo
 * @date  : vie mar  6 09:37:52 CST 2015
 * @goal  : This Class defines all email server logic  for the account assing to.
 * Based on @_http://wiki.nginx.org/ImapAuthenticateWithApachePhpScript
 */
class EmailConnect extends AuthSMTP {
    var $backend_ip;
    var $euser;
    var $email;
    var $elogin;
    var $login;
    var $clogin;
    var $eserver;
    var $sprotocol;
    var $bport;
    var $mailhost;
    var $localhost;
    var $newLine;
    var $idEstado;
    var $idDistrito;
    function __construct() {
        $this->conn = ConnectLdap::__construct();
	// First case: just for the example of segregating users starting from "a" to "b" letters on correo-n1 server.
        // Second case: all users for "circunscripcion 1"
        //$this->backend_ip["correo-n1"] = "10.0.48.42";
        //$this->backend_ip["correo-n1"] = "correo-n1.example.com";
        $this->backend_ip["correo-n1"] = "192.168.100.42";
        // First case: just for the example of segregating users starting from "c" to "f" letters on correo-n2 server.
        // Second case: all users for "circunscripcion 2"
        //$this->backend_ip["correo-n2"] = "10.0.48.43";
        //$this->backend_ip["correo-n2"] = "correo-n2.example.com";
        $this->backend_ip["correo-n2"] = "192.168.100.43";
	// First case: just for the example of segregating users starting from "g" to "z" letters on correo-n3 server.
        // Second case: all users for "circunscripcion 3"
        //$this->backend_ip["correo-n3"] = "10.0.48.44";
	//$this->backend_ip["correo-n3"] = "correo-n3.example.com";
	$this->backend_ip["correo-n3"] = "192.168.100.44";
	// First case: just for the example of segregating users starting from "g" to "z" letters on correo-n3 server.
	// Second case: all users for "circunscripcion 4"
        //$this->backend_ip["correo-n4"] = "10.0.48.45";
	//$this->backend_ip["correo-n4"] = "correo-n4.example.com";
	$this->backend_ip["correo-n4"] = "192.168.100.45";
	// First case: just for the example of segregating users starting from "g" to "z" letters on correo-n3 server.
	// Second case: all users for "circunscripcion 5"
	//$this->backend_ip["correo-n5"] = "10.0.48.46";
	//$this->backend_ip["correo-n5"] = "correo-n5.example.com";
	$this->backend_ip["correo-n5"] = "192.168.100.46";
	// First case: just for the example of segregating users starting from "g" to "z" letters on correo-n3 server.
	// Second case: all users for "Oficinas Centrales" -- perhaps another server to save 'genericas' and another accounts
	//$this->backend_ip["correo-n0"] = "correo-n0.ine.mx";
	$this->backend_ip["correo-n0"] = "192.168.100.47";
        $this->localhost = "localhost";
        $this->newLine = "\r\n";
    }
    /* Author : Ricardo Carrillo
     * @date  : mar mar 10 18:08:43 CST 2015
     * @goal  : Select email port depending of user client.
     */
    function getPortbyProtocol($protocol) {
        if (isset($protocol)) {
            switch (strtolower($protocol)) {
		case 'pop':
		case 'pop3':
                    $this->bport = "110";
                    break;
		case 'smtp':
		    $this->bport = "587";
                    break;
		case 'imap':
                default :
                    $this->bport = "143";
                    break;
            }
            return $this->bport;
        } else {
            return false;
        }
    }

    /* Author: Ricardo Carrillo
     * @date : jue mar  5 17:45:15 CST 2015
     * @goal : determining $user existence into ldap server
     
    function getMailServer($user) {
        $this->euser = $this->identifyUser($user);
        $this->email = $this->getAttr($user, "mailHost");
        // accounting identification by type (ldap ine.mx branch)
        if ($this->euser == "p") {
            $this->elogin = strtolower($user);
            // segregating accounts by last name.
            if (preg_match('/[a-zA-Z]{2,15}\.[a-b][a-zA-Z]{2,15}$/', $this->elogin, $lmatches)) {
                return $this->backend_ip["correo-n1"];
            } elseif (preg_match('/[a-zA-Z]{2,15}\.[c-f][a-zA-Z]{2,15}$/', $this->elogin, $lmatches)) {
                return $this->backend_ip["correo-n2"];
               # return $this->backend_ip["correo-n1"];
            } elseif (preg_match('/[a-zA-Z]{2,15}\.[g-z][a-zA-Z]{2,15}$/', $this->elogin, $lmatches)) {
		return $this->backend_ip["correo-n3"];
            } else {
                // we do not know other account type, then set "Invalid account type"" message.
                $this->setUnknownMsg();
            }
        } elseif ($this->euser == "g") {
            return $this->backend_ip["correo-n4"];
        } elseif ($this->euser == "e" || $this->euser == "s") {
            $this->setSystemAccountMsg();
        } else {
            // we do not know other account type, then set "Invalid account type" message.
            $this->setUnknownMsg();
        }
    }
    */
	 
	/* Author: Ricardo Carrillo
	 * @date : mie nov 25 13:17:34 CST 2015
 	 * @goal : determining "circunscription" for $user, based on $idEstado 
	 * 		   Estado:codigo:circunscripcion: -> Circunscripcion 1: bc:2:1, bcs:3:1, chih:8:1, dgo:10:1, jal:14:1, nay:18:1, son:26:1, sin:25:1
	 **/
    function getMailServer($user) {
        //$this->euser = $this->identifyUser($user);
        //$this->email = $this->getAttr($this->euser,"mailHost");
		$this->clogin=strtolower($user);
		if(isset($this->clogin) && $this->clogin!=""){
			$this->idEstado=$this->getSpecificDataUserLdap("idestado",$this->clogin);
			//$this->idDistrito=$this->getSpecificDataUserLdap("iddistrito",$this->euser);
	        // Circunscripcion 1: bc:2:1, bcs:3:1, chih:8:1, dgo:10:1, jal:14:1, nay:18:1, son:26:1, sin:25:1
			if($this->idEstado==2 ||$this->idEstado==3 || $this->idEstado==8 || $this->idEstado==10 || $this->idEstado==14||$this->idEstado==18||$this->idEstado==26 ||$this->idEstado==25){
				return $this->backend_ip["correo-n1"];
			}elseif($this->idEstado==1 ||$this->idEstado==5 || $this->idEstado==11 || $this->idEstado==19 || $this->idEstado==22||$this->idEstado==24||$this->idEstado==28 ||$this->idEstado==32){
			// Circunscripcion 2: ags:1:2,coah:5:2,gto:11:2,nl:19:2,qro:22:2,slp:24:2,tamps:28:2,zac:32:2
			 	return $this->backend_ip["correo-n2"];
			}elseif($this->idEstado==4 ||$this->idEstado==7 || $this->idEstado==20 || $this->idEstado==23 || $this->idEstado==27||$this->idEstado==30||$this->idEstado==31){
			// Circunscripcion 3: camp:4:3,chis:7:3,oax:20:3,qr:23:3,tab:27:3,ver:30:3,yuc:31:3
			 	return $this->backend_ip["correo-n3"];
			}elseif($this->idEstado==9||$this->idEstado==12||$this->idEstado==17||$this->idEstado==21||$this->idEstado==29){
			// Circunscripcion 4: df:9:4,gro:12:4,mor:17:4,pue:21:4,tlax:29:4
				return $this->backend_ip["correo-n4"];
			}elseif($this->idEstado==6||$this->idEstado==15||$this->idEstado==16||$this->idEstado==13){
				// Circunscripcion 5: col:6:5,mex:15:5,mich:16:5,hgo:13:5
				return $this->backend_ip["correo-n5"];
			}else{
				// Rest is for "Oficinas Centrales": oc:0:0
				return $this->backend_ip["correo-n0"];
			}
		}else
			return false;
		return false;
    }
    /* Author: Ricardo Carrillo
     * @date : mar mar 10 18:07:19 CST 2015
     * @goal : Get Mail Host from LDAP user.
     */
    function getMailHost($user) {
        $this->mailhost = $this->getAttr($user, "mailHost");
        if ($this->mailhost) {
            return $this->mailhost;
        } else {
            return false;
        }
    }

    /* Author: Ricardo Carrillo
     * @date : jue mar  5 10:25:42 CST 2015
     * @goal : give answer method to unknown account types.
     */
    function setUnknownMsg() {
        header("Auth-Status: Invalid account type");
        exit ;
    }
    /* Author: Ricardo Carrillo
     * @date : jue mar  5 10:25:42 CST 2015
     * @goal : give answer method to unknown account types.
     */
    function setSystemAccountMsg() {
        header("Auth-Status: System account");
        exit ;
    }

    /* Author: Ricardo Carrillo
     * @date : jue mar  5 10:25:42 CST 2015
     * @goal : Method used once used checkPassword and userExist methods.
     *  This will be used into auth login form.
     */
    function setStatusPass($server, $port, $user, $password) {
        header("Auth-Status: OK");
        header("Auth-Server: $server");
        header("Auth-User: $user");
        header("Auth-Pass: $password");
        header("Auth-Port: $port");
        exit ;
    }
    /* Author : Ricardo Carrillo
     * @date  : mar mar 10 17:57:40 CST 2015
     * @goal  : Method used to check availability of "SMTP" user server
     */
    function isEmailServerAvailable($smtpServer, $protocol, $timeout, $username, $password,$auth="") {
        if (isset($smtpServer) && isset($protocol) && isset($username)&& isset($password)&& isset($timeout)) {
            $smtpConnect = fsockopen($smtpServer, $protocol, $errno, $errstr, $timeout);
            if (isset($smtpConnect)) {
                return false;
            } else {
                $smtpResponse = fgets($smtpConnect, 515);
                if (isset($auth)) {
                    fputs($smtpConnect, "AUTH LOGIN " . $this->newLine);
                    $smtpResponse = fgets($smtpConnect, 515);
                    $output = $output . "$smtpResponse";
                    fputs($smtpConnect, base64_encode($username) . $this->newLine);
                    // we send email user
                    $smtpResponse = fgets($smtpConnect, 515);
                    $output = $output . "$smtpResponse";
                    fputs($smtpConnect, base64_encode($password) . $this->newLine);
                    //we send password
                    $smtpResponse = fgets($smtpConnect, 515);
                    $output = $output . "$smtpResponse";
                }
                fputs($smtpConnect, "HELO " . (!$smtpServer ? $smtpServer : $this->localhost) . " " . $this->newLine);
                //we say "HELO" to SMTP server
                $smtpResponse = fgets($smtpConnect, 515);
                $output = $output . "$smtpResponse";
                fputs($smtpConnect, "QUIT" . $this->newLine);
                //Just say bye to SMTP server
                $smtpResponse = fgets($smtpConnect, 515);
                $output = $output . "$smtpResponse";
                if ($output) {
                    return true;
                } else {
                    return false;
                }
            }
            fclose($smtpConnect);
        } else {
            return false;
        }
    }

    /* Author : Ricardo Carrillo
     * @date  : mar mar 10 18:16:00 CST 2015  
     * @goal  : This method is defined to give an answer if the server selected is not available
     */
    function isNotAvailable(){
        header("Auth-Status: Temporary server problem, try again later");
        header("Auth-Error-Code: 451 4.3.0");
        header("Auth-Wait: 3");
        exit;
    }
}
?>
