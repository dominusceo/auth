#!/usr/bin/php
<?php
include_once './class/connect-ldap.php';
require_once './class/auth-smtp.php';
require_once "./class/serverEmail.php";
$a=new AuthSMTP();
$e=new EmailConnect();
$elogin=@strtolower($argv[1]);
if (isset($elogin) && $elogin!=""){
	if($e->getMailServer($elogin)){
		print "Servidor correspondiente para $elogin es: ".$e->getMailServer($elogin)."\n";
	}else{
		print "Ocurrio algo, favor de verificar.\n";
		exit (0);
	}
}else{
	print "Debes colocar el login del usuario\n";
	exit (1);
}
?>