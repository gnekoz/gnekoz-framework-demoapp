<?php
namespace demo\db;

/**
 * @author gneko
 *
 */
abstract class DBObject extends \DB_DataObject
{
  private $errorMsgs = array();

  public function clearErrors()
  {
    $this->errorMsgs = array();
  }

  public function addError($msg)
  {
    $this->errorMsgs[] = $msg;
  }


  public function addErrors($msgs)
  {
    $this->errorMsgs += $msgs;
  }

  public function hasErrors()
  {
    return count($this->errorMsgs) > 0;
  }


  public function getErrors()
  {
    return $this->errorMsgs;
  }

  public function validate()
  {
    return true;
  }


  public function insert()
  {
    $ret = parent::insert();
    if (!$ret)
    {
      $this->addError($this->_lastError->getMessage());
    }
    return $ret;
  }


  /**
   * @param string $dataObject
   * @return object
   */
  public function update($dataObject = false)
  {
    $ret = parent::update($dataObject);
    if (!$ret)
    {
      $this->addError($this->_lastError->getMessage());
    }
    return $ret;
  }



  /**
   * @param string $useWhere
   * @return boolean
   */
  public function delete($useWhere = false)
  {
    $ret = parent::delete($useWhere);
    if (!$ret)
    {
      $this->addError($this->_lastError->getMessage());
    }
    return $ret;
  }


  public function save()
  {
    if ($this->areKeysSet())
    {
    	return $this->update();
    } else {
    	return $this->insert();
      
    }
  }

  public function areKeysSet()
  {    
    foreach ($this->keys() as $key)
    {
      if ($this->$key == null)
        return false;
    }
    return true;
  }


  /**
   * Valorizza l'oggetto dai parametri di una richiesta http
   * @param unknown $request
   */
  public function setFromRequest($request)
  {
    // Lettura chiavi primarie
    $keys = $this->keys();
    $keysSet = true;
    foreach ($keys as $key)
    {
      $this->fieldFromRequestCallback($key, $request->getParameter($key));
      if ($this->$key == null)
      {
        $keysSet = false;
        break;
      }
    }

    if ($keysSet)
    {
      if (!$this->find())
      {
        $this->addError($this->_lastError->getMessage());
      }
      $this->fetch();
    }

    foreach ($this->table() as $colName => $colType)
    {
//      var_dump($colName);
      if ($request->hasParameter($colName))
      	$this->fieldFromRequestCallback($colName, $request->getParameter($colName));
    }
//exit;
  }
  
  
  /**
   * 
   * @param string $name
   * @param mixed $value
   */
  protected function fieldFromRequestCallback($name, $value)
  {
  	$this->$name = $value;
  }
}
