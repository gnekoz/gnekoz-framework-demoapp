<?php

namespace demo\db;

use demo\Auth;
use demo\App;

/**
 * Table Definition for utenti
 */
class DBUtenti extends DBObject
{
    const CIPHER_METHOD = "AES-128-CBC";
    const HASH_METHOD = "sha256";
    
    
    const ROLE_ADMIN = 2;
    const ROLE_BROKEROWNER = 4;
    const ROLE_BROKERMANAGER = 8;
    const ROLE_USER = 16;
    const ROLE_PHONEOPERATOR = 32;
    const ROLE_EXPENSEOPERATOR = 64;

    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'utenti';              // table name
    public $id;                              // int(4)  primary_key not_null
    public $username;                        // varchar(100)  unique_key not_null
    public $password;                        // varchar(100)   not_null
    public $password2;                       // varchar(200)
    public $email;                           // varchar(100)   not_null
    public $cellulare;                       // varchar(30)
    public $ruoli;                           // int(4)   not_null
    public $id_responsabile;                 // int(4)
    public $nominativo;                      // varchar(100)   not_null
    public $id_ufficio;                      // int(4)   not_null
    public $flg_disabilitato;                // int(4)

    /* Static get */

    function staticGet($k, $v = NULL)
    {
        return DB_DataObject::staticGet('Utenti', $k, $v);
    }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    private $ufficio = null;

    public function __construct()
    {
        $this->ufficio = null;
    }

    public function fetch()
    {
        /*
         * Necessario perchè DB_DataObject riutilizza la stessa istanza
         */
        $this->ufficio = null;
        return parent::fetch();
    }


    public function validate()
    {
        parent::validate();

        if ($this->nominativo == "") {
            $this->addError("E' necessario indicare il nominativo");
        }

        if ($this->username == "") {
            $this->addError("E' necessario indicare lo username");
        }

        if ($this->email == "") {
            $this->addError("E' necessario indicare l'indirizzo email");
        }

        if ($this->id_ufficio == null) {
            $this->addError("E' necessario indicare l'ufficio");
        }

        if ($this->ruoli == null) {
            $this->addError("E' necessario indicare almeno un ruolo");
        }

        if ($this->password == null && $this->password2 == null) {
            $this->addError("E' necessario indicare la password");
        }

        return !$this->hasErrors();
    }


    /**
     *
     * @return DBUffici
     */
    public function getUfficio()
    {
        if ($this->id_ufficio != null) {
            if ($this->ufficio != null) {
                return $this->ufficio;
            }

            $this->ufficio = new DBUffici();
            if ($this->ufficio->get($this->id_ufficio) != 1) {
                $this->addError("Impossibile leggere l'ufficio");
            }
        }
        return $this->ufficio;
    }

    /**
     *
     * @return
     */
    public function getBudget($year)
    {
        if ($this->id == null)
        {
            return array();
        }

        $result = new DBBudget();

        $budget = new DBBudget();
        $budget->id_utente = $this->id;
        $budget->anno = $year;
        $budget->orderBy('anno ASC');
        $budget->find();
        while ($budget->fetch())
        {
            $result = clone $budget;
        }
        $budget->free();

        return $result;
    }

    protected function fieldFromRequestCallback($name, $value)
    {
        if ($name == 'id_responsabile' && $value == null) {
            $this->id_responsabile = 'null';
        } else {
            parent::fieldFromRequestCallback($name, $value);
        }
    }

    public function hasRole($role)
    {
        $userRoles = $this->decodeRoles();

        return isset($userRoles[$role]) || isset($userRoles[self::ROLE_ADMIN]);
    }

    public static function getUser($userID)
    {
        $user = new DBUtenti();
        $ret = $user->get($userID);
        $user->free();
        if ($ret != 1) {
            $user = new DBUtenti();
        }

        return $user;
    }

    public static function getUsersMapByRole($role, $active = true)
    {
        $users = array();
        $user = new DBUtenti();
        if ($active) {
            $user->whereAdd('coalesce(flg_disabilitato, 0) != 1');
        }
        $user->orderBy("nominativo ASC");
        $user->find();
        while ($user->fetch()) {
            $roles = $user->decodeRoles();
            foreach ($roles as $roleID => $roleDes) {
                if ($roleID == $role)
                {
                    $users[$user->id] = $user->nominativo;
                }
            }
        }
        $user->free();

        return $users;
    }

    public static function getUsersMap($groupByRole = false, $active = true)
    {
        $users = array();
        $user = new DBUtenti();
        if ($active) {
            $user->whereAdd('coalesce(flg_disabilitato, 0) != 1');
        }
        $user->orderBy("nominativo ASC");
        $user->find();
        while ($user->fetch()) {
            if ($groupByRole) {
                $roles = $user->decodeRoles();
                foreach ($roles as $roleID => $roleDes) {
                    if (!isset($users[$roleDes])) {
                        $users[$roleDes] = array();
                    }
                    $users[$roleDes][$user->id] = $user->nominativo;
                }
            } else {
                $users[$user->id] = $user->nominativo;
            }
        }
        $user->free();

        return $users;
    }

    public static function getRoles()
    {
        return array(
            self::ROLE_ADMIN => "Amministratore",
            self::ROLE_BROKEROWNER => "Broker titolare",
            self::ROLE_BROKERMANAGER => "Broker manager",
            self::ROLE_USER => "Utente normale",
            self::ROLE_PHONEOPERATOR => "Operatore centralino",
            self::ROLE_EXPENSEOPERATOR => "Operatore consumi");
    }

    public static function getRoleDesc($roleID)
    {
        $roles = self::getRoles();
        if (isset($roles[$roleID])) {
            return $roles[$roleID];
        }

        return "";
    }

    public function decodeRoles()
    {
        $result = array();
        $allRoles = self::getRoles();

        foreach ($allRoles as $id => $des) {
            $res = $this->ruoli & $id;
            if ($res > 1) {
                $result[$id] = $des;
            }
        }
        return $result;
    }

    public function encodeRoles($roles)
    {
        $this->ruoli = 0;
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole($role)
    {
        $this->ruoli += (int) $role;
    }

    public static function encryptPasswordLegacy($password)
    {
        $conf = App::getInstance()->getConfiguration();
        $salt = $conf->getProperty("/persistence/salt");

        return md5($salt . $password);
    }
    
    
    public static function encryptPassword($password)
    {
        $conf = App::getInstance()->getConfiguration();
        $key = $conf->getProperty("/persistence/key");
        
        $ivlen = \openssl_cipher_iv_length($cipher = self::CIPHER_METHOD);
        $iv = \openssl_random_pseudo_bytes($ivlen);      
        $ciphertext_raw = \openssl_encrypt($password, $cipher, $key, $options = \OPENSSL_RAW_DATA, $iv);
        $hmac = \hash_hmac(self::HASH_METHOD, $ciphertext_raw, $key, $as_binary = true);
        return \base64_encode( $iv.$hmac.$ciphertext_raw ); 
    }    
    
    
    public static function decryptPassword($encryptedPassword)
    {
        $conf = App::getInstance()->getConfiguration();
        $key = $conf->getProperty("/persistence/key");
        
        $c = \base64_decode($encryptedPassword);
        $ivlen = \openssl_cipher_iv_length($cipher = self::CIPHER_METHOD);
        $iv = \substr($c, 0, $ivlen);
        $hmac = \substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = \substr($c, $ivlen + $sha2len);
        $original_plaintext = \openssl_decrypt($ciphertext_raw, $cipher, $key, $options = \OPENSSL_RAW_DATA, $iv);
        $calcmac = \hash_hmac(self::HASH_METHOD, $ciphertext_raw, $key, $as_binary = true);
        if (\hash_equals($hmac, $calcmac)) {
            return $original_plaintext;
        }
        
        return false;
    }
    

    public function getChildrenMap($active = true)
    {
        $result = array();
        $auth = new Auth();
        $curUser = $auth->getCurrentUser();

        $model = new DBUtenti();
        if ($active) {
            $model->whereAdd('coalesce(flg_disabilitato, 0) != 1');
        }
        if (!$curUser->hasRole(self::ROLE_BROKEROWNER)) {
            $model->whereAdd("id_responsabile = {$curUser->id} or id = {$curUser->id}");
        }
        $model->orderBy("nominativo");
        $model->find();

        while ($model->fetch()) {
            $result[$model->id] = $model->nominativo;
        }
        $model->free();

        return $result;
    }


    public function getEmailAddresses()
    {
        preg_match_all('/(\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b)/i', $this->email, $result, PREG_PATTERN_ORDER);
        array_shift($result);
        return $result[0];
    }
}
