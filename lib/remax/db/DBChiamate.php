<?php
namespace demo\db;

/**
 * Table Definition for chiamate
 */
class DBChiamate extends DBObject
{
  ###START_AUTOCODE
  /* the code below is auto generated do not remove the above tag */

  public $__table = 'chiamate';            // table name
  public $id;                              // int(4)  primary_key not_null
  public $data;                            // timestamp   not_null
  public $telefono_chiamante;              // varchar(100)   not_null
  public $nominativo_chiamante;            // varchar(100)   not_null
  public $email_chiamante;                 // varchar
  public $pubblicita;                      // varchar(100)
  public $note;                            // text
  public $immobile;                        // varchar(200)
  public $id_utente_destinatario;          // int(4)  
  public $data_email_destinatario;         // timestamp

  /* Static get */
  function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Chiamate',$k,$v); }

  /* the code above is auto generated do not remove the tag below */
  ###END_AUTOCODE

  public function validate()
  {
    parent::validate();

    if ($this->data == null)
    {
      $this->addError("E' necessario indicare la data");
    }


    if ($this->nominativo_chiamante == "")
    {
      $this->addError("E necessario indicare il nominativo del chiamante");
    }

    if ($this->telefono_chiamante == "")
    {
      $this->addError("E necessario indicare il numero di telefono del chiamante");
    }

    return !$this->hasErrors();
  }
  
  protected function fieldFromRequestCallback($name, $value)
  {
    if ($name == 'id_utente_destinatario' && $value == null) 
    {
      $this->id_utente_destinatario = 'null';
    } 
    else if ($name == 'data') 
    {
        $dt = \DateTime::createFromFormat("d/m/Y H:i", $value);
        if ($dt == false) {
            $this->addError("Formato della data non corretto");
        } else {
            $this->data = $dt->format("Y-m-d H:i");
        }
    } 
    else 
    {
      parent::fieldFromRequestCallback($name, $value);
    }
}
}
