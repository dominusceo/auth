#!/usr/bin/php
<?php
$elogin=@strtolower($argv[1]);
if (isset($elogin) && $elogin!=""){
	$backend_ip["correo-n1"]="10.0.48.42";
	$backend_ip["correo-n2"]="10.0.48.43";
	$backend_ip["correo-n3"]="10.0.48.44";
	$backend_ip["correo-n4"]="10.0.48.45";
	$backend_ip["correo-n5"]="10.0.48.46";
	if (preg_match('/[a-zA-Z]{2,15}\.[a-b][a-zA-Z]{2,15}$/', $elogin, $lmatches)) {
		print $backend_ip["correo-n1"]."\n";
	} elseif (preg_match('/[a-zA-Z]{2,15}\.[c-f][a-zA-Z]{2,15}$/', $elogin, $lmatches)) {
		#return $this->backend_ip["correo-n2"];
		print $backend_ip["correo-n2"]."\n";
	} elseif (preg_match('/[a-zA-Z]{2,15}\.[g-z][a-zA-Z]{2,15}$/', $elogin, $lmatches)) {
		print $backend_ip["correo-n3"]."\n";
	}else{
		print "El parametro provisto ($elogin), no corresponde a un nombre de usuario\n";
		exit (1);
	}
}else{
	print "Debes colocar el login del usuario\n";
	exit (1);
}
?>