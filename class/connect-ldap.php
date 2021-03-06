<?php
	/**
	 * Autor : Ricardo Carrillo
	 * @date : vie mar  6 10:17:07 CST 2015
	 * @goal : This class define the initial values for connection to ldap server and database server
	 * 
	 */
	class ConnectLdap  {
		/*
		var $ldapserver  ="ldapemail.example.com";
		var $ldapport    ="389";
		var $ldapports   ="636";
		var $ldapbind	 ="";
		var $baseDn      ="dc=ife.org.mx";
    		var $puser       ="ou=people,dc=example,dc=com";
    		var $guser       ="ou=generica,dc=example,dc=com";
	    	var $euser       ="ou=externo,ou=people,dc=example,dc=com";
    		var $ldapUser    ="cn=Manager,dc=example,dc=com";
    		var $ldappass    ="";
		var $conn        ="";
		var $result      ="";
		var $dbserver    ="bd.example.com";
		var $dbpasswd    ="setmefree123";
		var $dbuser      ="anonymous";
		*/
		var $ldapserver  ="ldap.example.com";
		var $ldapport    ="389";
		var $ldapports   ="636";
		var $ldapbind	 ="";
		var $baseDn      =",dc=example,dc=com";
    	        var $puser       ="ou=people,,dc=example,dc=com";
    	        var $guser       ="ou=generica,dc=example,dc=com";
	        var $euser       ="ou=externo,ou=people,dc=example,dc=com";
    	  	var $ldapUser    ="cn=Manager,dc=example,dc=com";
    		var $ldappass    ="redhat";
		var $conn        ="";
		var $result      ="";
		var $dbserver    ="bd.example.com";
		var $dbpasswd    ="setmefree123";
		var $dbuser      ="anonymous";		
		function __construct($secure=false,$typedb="ldap") {
			if($typedb=="ldap"){
				if($secure==false){
					$this->conn=ldap_connect($this->ldapserver,$this->ldapport);
                                        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);	
					if($this->conn){
						return $this->conn;	
					}else{
						return FALSE;
					}
				}elseif($secure==TRUE){
					$this->conn=ldap_connect($this->ldapserver,$this->ldapports); 
                                        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
					if($this->conn){
						return $this->conn;
					}else{
						return FALSE;
					}
				}
			}elseif($typedb=="postgresql"){
				print "postgres support not available";
				return FALSE; // at the moment this feature is unssuported.
			}
			return FALSE;
		}
		function __destruct() {	
			if($this->conn){
    			ldap_close($this->conn);
    		}else{
				return false;	
			} 
		}
	}
?>
