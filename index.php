<?php
/* Author: Carrillo Sanchez Ricardo David
* @Goal: Define auht logic module for authentication users from LDAP server
* @date: lun mar 30 20:52:10 CDT 2015
*/
include_once './class/connect-ldap.php';
require_once './class/auth-smtp.php';
require_once "./class/serverEmail.php";
$a=new AuthSMTP();
$e=new EmailConnect();
// same as the examples provided on ngnix wiki
// @_http://wiki.nginx.org/ImapAuthenticateWithApachePhpScript
/*
$_SERVER["HTTP_HOST"]="192.168.122.170";
$_SERVER["HTTP_AUTH_METHOD"]= "plain";
$_SERVER["HTTP_AUTH_USER"]="ricardo.carrillo";
$_SERVER["HTTP_AUTH_PASS"]="r3dh4t";
$_SERVER["HTTP_AUTH_PROTOCOL"]="pop3";
$_SERVER["HTTP_AUTH_LOGIN_ATTEMPT"]="1";
$_SERVER["HTTP_CLIENT_IP"]="192.168.122.1";
$_SERVER["HTTP_X_AUTH_PORT"]="995";
$_SERVER["HTTP_USER_AGENT"]="Nginx POP3/IMAP4 proxy";
*/
$user=$_SERVER["HTTP_AUTH_USER"];
$password=$_SERVER["HTTP_AUTH_PASS"];
$protocol=$_SERVER["HTTP_AUTH_PROTOCOL"];     // This is the protocol being proxied
$auth=$_SERVER['HTTP_AUTH_METHOD'];         // The authentication mechanism
//$salt=$_SERVER['HTTP_AUTH_SALT'];             // Need the salt to encrypt the cleartext password, used for some authentication mechanisms
$attempt=$_SERVER['HTTP_AUTH_LOGIN_ATTEMPT']; // The number of attempts needs to be an integer
$ipclient=$_SERVER['HTTP_CLIENT_IP'];         // It's the IP number from users client.
//$hostname=$_SERVER['HTTP_CLIENT_HOST'];       // It's the hostname from users client.
$maxattempts=3;
#$port=$_SERVER["HTTP_X_AUTH_PORT"] ;
if (isset($user) || isset($password)) {
	if(!$a->authUser($user,$password)){
		// set message just in case if the provided password or user are wrong.
		$a->setFail();
	}else{
		// set the server configuration and redireting to it.
		$getMailHost = $e->getMailHost($user);
		$getPort = $e->getPortbyProtocol($protocol);
		$getMailServ = $e->getMailServer($user);
		#if($e->isEmailServerAvailable($getMailServ,$getProtocol,3,$user,$password,$auth)){
			$e->setStatusPass($getMailServ,$getPort,$user,$password);
        	#}else{
            	// the server is not available
			#$e->isNotAvailable();
        	#}
	}
}else{
	// set message just in case if the provided password or login are wrong.
	$a->setFail();
}
?>
