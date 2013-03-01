<?php

Autoloader::namespaces(array(
	'Verify\Models'	=> __DIR__ . '/../verify/models'
));

Autoloader::map(array(

	'Verify_LDAP' 	=> __DIR__ . '/libraries/verify_ldap.php',
	'LDAPConnect' => __DIR__.DS.'libraries/ldapconnect.php',
	'Verify' 	=> __DIR__ . '/../verify/libraries/verify.php'

));

Auth::extend('verify_ldap', function() {

	return new Verify_LDAP;

});