<?php
namespace demo\db;

use DateTime;
use DB_DataObject;
use NumberFormatter;

/**
 * Table Definition for contatti
 */
class DBContatti extends DBObject
{
  ###START_AUTOCODE
  /* the code below is auto generated do not remove the above tag */
  public $__table = 'contatti';            // table name
  public $id;                              // int(4)  primary_key not_null
  public $data;                            // timestamp   not_null
  public $telefono_chiamante;              // varchar(100)   not_null
  public $nome_chiamante;                  // varchar(100)
  public $cognome_chiamante;               // varchar(100)
  public $titolo_chiamante;                // varchar(50)
  public $email_chiamante;                 // varchar
  public $note;                            // text
  public $id_utente_destinatario;          // int(4)  
  public $data_email_destinatario;         // timestamp
  public $data_wa_destinatario;            // timestamp
  public $id_fonte_pubblicita;             // int(4)
  public $id_tipo_immobile;                // int(4)
  public $id_motivo_richiesta;             // int(4)
  public $id_tipo_richiesta;               // int(4)
  public $id_tipo_contatto;                // int(4)
  public $id_camere;                       // int(4)
  public $id_maximizer;                    // varchar(100)
  public $comune;                          // varchar(100)
  public $zona;                            // varchar(100)
  public $superficie_min;                  // int(4)
  public $superficie_max;                  // int(4)
  public $prezzo;                          // numeric(15, 2)
  public $prezzo_min;                      // numeric(15, 2)
  public $prezzo_max;                      // numeric(15, 2)


  /* Static get */
  function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Contatti',$k,$v); }

  /* the code above is auto generated do not remove the tag below */
  ###END_AUTOCODE

  public function validate()
  {
    parent::validate();

    if ($this->data == null)
    {
      $this->addError("E' necessario indicare la data");
    }

    if (!$this->nome_chiamante && !$this->cognome_chiamante)
    {
      $this->addError("E necessario specificare almeno il nome o il cognome del chiamante");
    }

    if ($this->telefono_chiamante == "")
    {
      $this->addError("E necessario indicare il numero di telefono del chiamante");
    }
    
//    if ($this->email_chiamante == "")
//    {
//      $this->addError("E necessario indicare l'indirizzo email del chiamante");
//    }    
    
    if ($this->id_fonte_pubblicita == null)
    {
      $this->addError("E necessario indicare la fonte della pubblicità");
    }    
    
    if ($this->id_tipo_contatto == null)
    {
      $this->addError("E necessario indicare il tipo di contatto");
    }    
    
    if ($this->id_tipo_richiesta == null)
    {
      $this->addError("E necessario indicare il tipo di richiesta");
    }
    
    if ($this->id_motivo_richiesta == null)
    {
      $this->addError("E necessario indicare il motivo della richiesta");
    }
    
    if ($this->id_tipo_immobile == null)
    {
      $this->addError("E necessario indicare il tipo di immobile");
    }
    
    if ($this->comune == null)
    {
      $this->addError("E necessario indicare il comune");
    }
    
    return !$this->hasErrors();
  }
  
  protected function fieldFromRequestCallback($name, $value)
  {
    if ($name == 'id_utente_destinatario' && $value == null) 
    {
      $this->id_utente_destinatario = 'null';
    } 
    else if ($name == 'id_camere' && $value == null) 
    {
      $this->id_camere = 'null';
    }
    else if ($name == 'data') 
    {
        $dt = DateTime::createFromFormat("d/m/Y H:i", $value);
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
