<?php

Autoloader::map(array(
	'LDAP' => __DIR__.DS.'ldap.php',
	'KentLDAP' => __DIR__.DS.'kentldap.php',
));

Auth::extend('ldap', function()
{
    return new LDAP;
});