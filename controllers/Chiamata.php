<?php

namespace demo\controllers;

use gnekoz\rendering\JsonRenderer;
use demo\db\DBUtenti;
use demo\db\DBChiamate;
use \PHPMailer;
use demo\Controller;
use demo\SmartyRenderer;
use demo\App;

/**
 * @author gneko
 *
 */
class Chiamata extends Controller
{

    public function init()
    {
        parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_PHONEOPERATOR);
    }

    public function index()
    {
        $chiamata = new DBChiamate();
        $chiamata->data = strtotime(date("Y-m-d H:i"));

        $data = array("chiamata" => $chiamata,
            "utenti" => DBUtenti::getUsersMap());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("chiamata/new.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function edit()
    {
        $chiamata = new DBChiamate();
        $chiamata->setFromRequest($this->getRequest());

        $data = array("chiamata" => $chiamata,
            "utenti" => DBUtenti::getUsersMap());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("chiamata/edit.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function save()
    {
        $chiamata = new DBChiamate();
        $chiamata->setFromRequest($this->getRequest());

        if ($chiamata->validate()) {
            $chiamata->save();
            if (!$chiamata->hasErrors()) {
                if ($this->getRequest()->getParameter("save_new") != null)
                {
                    $this->getResponse()->sendRedirect("/chiamata");
                    return;
                }
            }
            
            if ($this->getRequest()->getParameter("save_send") != null)
            {
                $msg = "";
                $ret = $this->sendMailInternal($chiamata, $msg);
                if (!$ret) {
                    $chiamata->addError($msg);
                } else {
                    $this->getResponse()->sendRedirect("/chiamata");
                    return;
                }
            }
        }


        $data = array("chiamata" => $chiamata,
            "utenti" => DBUtenti::getUsersMap(),
            "errors" => $chiamata->getErrors());

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("chiamata/save.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    public function delete()
    {
        $chiamata = new DBChiamate();
        $chiamata->setFromRequest($this->getRequest());

        if ($chiamata->id != null) {
            $chiamata->delete();

            // FIXME

            $this->getResponse()->sendRedirect("/chiamata");
        }
    }

    public function elencoTipoPubblicita()
    {
        $result = array();
        $criteria = $this->getRequest()->getParameter("term");
        $chiamata = new DBChiamate();

        $query = <<< EOT
select distinct lower(pubblicita) as des
from chiamate
where pubblicita is not null
	and trim(pubblicita) != ''
	and lower(pubblicita) like '%$criteria%'
EOT;

        $chiamata->query($query);
        $i = 0;
        while ($chiamata->fetch()) {
            $result[$i] = array();
            $result[$i]['id'] = $chiamata->des;
            $result[$i]['value'] = $chiamata->des;
            $i++;
        }
        $chiamata->free();

        $renderer = new JsonRenderer();
        $this->getResponse()->addOutput($renderer->render($result));
    }

    public function inviaMail()
    {
        $msg = "";
        $chiamata = new DBChiamate();
        $chiamata->setFromRequest($this->getRequest());
        
        $this->sendMailInternal($chiamata, $msg);
        
        $renderer = new JsonRenderer();
        $this->getResponse()->addOutput($renderer->render($msg));
    }
    
    
    private function sendMailInternal($chiamata, &$msg)
    {
        $sender = $this->getAuth()->getCurrentUser();
        
        if ($chiamata->id == null) {
            $msg = "Errore: impossibile ottenere informazioni sulla chiamata";
            return false;
        } else if ($chiamata->id_utente_destinatario == null) {
            $msg = "Errore: manca il destinatario";
            return false;
        } else {
            $conf = App::getInstance()->getConfiguration();
            $user = DBUtenti::getUser($chiamata->id_utente_destinatario);
            $ts = strtotime($chiamata->data);
            $data = date("d/m/Y", $ts);
            $nominativo = strtoupper($chiamata->nominativo_chiamante);
            $ora = date("H:i", $ts);


            $mailer = new PHPMailer();
            $mailer->Mailer = "smtp";
            $mailer->SMTPKeepAlive = false;
            $mailer->Timeout = $conf->getProperty("/mail/timeout");
            $mailer->Sender = $sender->email;
            $mailer->Host = $conf->getProperty("/mail/host");
            $mailer->Port = $conf->getProperty("/mail/port");
            $mailer->SMTPAuth = (bool) $conf->getProperty("/mail/authentication");
            $mailer->AuthType = $conf->getProperty("/mail/auth-type");
            $mailer->Username = $sender->email;
            $mailer->Password = DBUtenti::decryptPassword($sender->password2);
            $mailer->SMTPSecure = $conf->getProperty("/mail/encryption");
            $mailer->From = $sender->email;
            $mailer->FromName = $sender->nominativo;
            $mailer->Subject = "Telefonata";
            $mailer->IsHTML(false);
            $mailer->WordWrap = 80;
            $mailer->Body = <<<EOF
Il giorno $data alle ore $ora ti ha cercato $nominativo telefono {$chiamata->telefono_chiamante} {$chiamata->email_chiamante}
EOF;
            if ($chiamata->immobile != null) {
                $mailer->Body .= " per l'immobile '{$chiamata->immobile}'";
            }

            $mailer->Body .= "\n\nNote: {$chiamata->note}";

            // Aggiunta indirizzi destinatari
            $recipients = $user->getEmailAddresses();
//            var_dump($recipients); exit();
            if (count($recipients) == 1) {
                $mailer->AddAddress($recipients[0], $user->nominativo);
            } else {
                foreach ($recipients as $recipient) {
                    $mailer->AddAddress($recipient);
                }
            }

            $mailer->SMTPDebug = false;
            $mailer->Debugoutput = "error_log";

            if (!$mailer->Send()) {
                $msg = "Errore: {$mailer->ErrorInfo}";
                return false;
            } else {
                $msg = "Email inviata con successo";
                //$chiamata->debugLevel(5);
                $chiamata->data_email_destinatario = date("Y-m-d H:i");
                $chiamata->save();
                return true;
            }
        }
        
        return false;
    }
}
