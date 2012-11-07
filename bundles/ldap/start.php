<?php
/**
 * A very simple and generic LDAP Auth method for Laravel.
 */
Autoloader::map(array(
    'LDAP' => __DIR__.DS.'ldap.php',
    'LDAPConnect' => __DIR__.DS.'ldapconnect.php',
    'LDAPUser' => __DIR__.DS.'ldapuser.php'
));

Auth::extend('ldap', function() {
    return new LDAP;
});
