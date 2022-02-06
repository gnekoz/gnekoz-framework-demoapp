<?php
namespace demo;

use demo\db\DBUtenti;
use gnekoz\auth\Authenticator;

class Auth implements Authenticator {
		
	public function getLoginPage() {
		return "/login";
	}
	
	public function isAuthenticated() {
		return isset($_SESSION['user']);
	}
	
	
	public function getCurrentUser() {
		return isset($_SESSION['user']) ? $_SESSION['user'] : null;
	}
	
	
	public function logout() {
		session_destroy();
	}
	
	public function login($username, $password) 
	{
		if (preg_match('/^([0-9a-zA-Z]+)$/', $username) == 0) {
			return false;
		} 
				
		if (preg_match('/^([@#_0-9a-zA-Z\-\$\!]+)$/', $password) == 0) {
			return false;
		}
				
 		$user = new DBUtenti();
 		$user->username = $username; 		 	
                $user->whereAdd('coalesce(flg_disabilitato, 0) = 0');
 		if ($user->find() != 1) {
 			return false;
 		}                                
 		$user->fetch();               
                
                $passwordMatch = false;
                if ($user->password2 == null) {
                    $passwordMatch = $user->password === DBUtenti::encryptPasswordLegacy($password);
                } else {
                    $dbPassword = DBUtenti::decryptPassword($user->password2);
                    $passwordMatch = $password === $dbPassword;
                }
                
                if (!$passwordMatch) {
                    return false;
                }
		
 		$_SESSION['user'] = clone $user;
		
 		$user->free();
		
		return true;
	}        
}
