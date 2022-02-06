<?php
namespace demo;

use \DB_DataObject;

class Users
{
	const ROLE_ADMIN = 2;
	
	const ROLE_BROKEROWNER = 4;
	
	const ROLE_BROKERMANAGER = 8;	
	
	const ROLE_USER = 16;
	
	const ROLE_PHONEOPERATOR = 32;
	
	public static function getUsersMap($groupByRole = false)
	{
		$users = array();
		$user = DB_DataObject::factory('utenti');
		$user->find();
		while ($user->fetch())
		{
			if ($groupByRole)
			{
				$roles = self::decodeRoles($user->ruoli);
				foreach ($roles as $roleID => $roleDes)
				{
					if (!isset($users[$roleDes])) 
					{
						$users[$roleDes] = array();
					}
					$users[$roleDes][$user->id] = $user->nominativo;
				}
			} 
			else
			{
				$users[$user->id] = $user->nominativo;
			}
		}
		$user->free();
	
		return $users;
	}	
	
	public static function getRoles()
	{
		return array(
				self::ROLE_ADMIN         => "Amministratore",
				self::ROLE_BROKEROWNER   => "Broker titolare",
				self::ROLE_BROKERMANAGER => "Broker manager",
				self::ROLE_USER          => "Utente normale",
				self::ROLE_PHONEOPERATOR => "Centralinista");
	}
	
	public static function getRoleDesc($roleID)
	{
		$roles = self::getRoles();
		if (isset($roles[$roleID]))
		{
			return $roles[$roleID];
		}
		
		return "";
	}

	public static function decodeRoles($userRoles)
	{
		$result = array();
		$roles = self::getRoles();
		foreach ($roles as $id => $des)
		{
			$res = $userRoles & $id;
			if ($res !== 0)
			{
				$result[$id] = $des;
			}
		}
		return $result;
	}
		
	
	public static function getUser($userID)
	{
		$result = null;
		$user = DB_DataObject::factory('utenti');
		if ($user->get($userID) == 1) {
			$result = clone $user;
		}
		$user->free();
		
		return $result;
	}
	
}
