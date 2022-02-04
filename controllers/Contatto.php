<?php

namespace demo\controllers;

use gnekoz\rendering\JsonRenderer;
use PHPMailer;
use demo\App;
use demo\Controller;
use demo\db\DBClassificazioni;
use demo\db\DBContatti;
use demo\db\DBUtenti;
use demo\SmartyRenderer;

/**
 * @author gneko
 *
 */
class Contatto extends Controller 
{
    const MSG_TYPE_EMAIL = 1;    
    const MSG_TYPE_WHATSAPP = 2;
    
    public function init() 
    {
        parent::init();
        $this->setRequiredRole(DBUtenti::ROLE_PHONEOPERATOR);
    }

    
    public function index() 
    {
        $contatto = new DBContatti();
        $contatto->data = strtotime(date("Y-m-d H:i"));

        $data = array(
            "contatto" => $contatto,
            "utenti" => DBUtenti::getUsersMap(),
            "tipiContatto" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_CONTATTO),
            "tipiRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_RICHIESTA),
            "tipiImmobile" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_IMMOBILE),
            "fontiPubblicita" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_FONTE_PUBBLICITA),
            "camere" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_CAMERE),
            "motiviRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_MOTIVO_RICHIESTA),
        );
        

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("contatto/new.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function edit() 
    {
        $contatto = new DBContatti();
        $contatto->setFromRequest($this->getRequest());      
        
        $data = array(
            "contatto" => $contatto,
            "utenti" => DBUtenti::getUsersMap(),
            "tipiContatto" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_CONTATTO),
            "tipiRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_RICHIESTA),
            "tipiImmobile" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_IMMOBILE),
            "fontiPubblicita" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_FONTE_PUBBLICITA),
            "camere" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_CAMERE),
            "motiviRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_MOTIVO_RICHIESTA),
        );

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("contatto/edit.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function save()
    {
        $waLink = null;
        $contatto = new DBContatti();
        $contatto->setFromRequest($this->getRequest());                

        if ($contatto->validate()) {
            $contatto->save();
            if (!$contatto->hasErrors()) {
                if ($this->getRequest()->getParameter("save_new") != null) {
                    $this->getResponse()->sendRedirect("/contatto");
                    return;
                }
            }

            if ($this->getRequest()->getParameter("save_send_email") != null) {
                $result = array();
                $ret = $this->sendMessageInternal($contatto, $result, self::MSG_TYPE_EMAIL);
                if (!$ret) {
                    $contatto->addError($result["message"]);
                } else {
                    $this->getResponse()->sendRedirect("/contatto");
                    return;
                }
            }
            
            if ($this->getRequest()->getParameter("save_send_whatsapp") != null) {
                $result = array();
                $ret = $this->sendMessageInternal($contatto, $result, self::MSG_TYPE_WHATSAPP);                
                //var_dump($ret); exit();
                if (!$ret) {
                    $contatto->addError($result["message"]);                    
                } else {
                    $waLink = $result["link"];
                }
            }
        }


        $data = array(
            "contatto" => $contatto,
            "utenti" => DBUtenti::getUsersMap(),
            "tipiContatto" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_CONTATTO),
            "tipiRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_RICHIESTA),
            "tipiImmobile" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_TIPO_IMMOBILE),
            "fontiPubblicita" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_FONTE_PUBBLICITA),
            "camere" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_CAMERE),
            "motiviRichiesta" => DBClassificazioni::getClassificazioni(DBClassificazioni::TIPO_MOTIVO_RICHIESTA),
            "errors" => $contatto->getErrors(),
            "waLink" => $waLink);

        $renderer = new SmartyRenderer(App::getInstance());
        $renderer->setTemplate("contatto/save.tpl");
        $this->getResponse()->addOutput($renderer->render($data));
    }

    
    public function delete() 
    {
        $contatto = new DBContatti();
        $contatto->setFromRequest($this->getRequest());

        if ($contatto->id != null) {
            $contatto->delete();

            // FIXME
            $this->getResponse()->sendRedirect("/contatti");
        }
    }


    public function inviaMail() 
    {
        $result = array();
        $contatto = new DBContatti();
        $contatto->setFromRequest($this->getRequest());

        $this->sendMessageInternal($contatto, $result, self::MSG_TYPE_EMAIL);

        $renderer = new JsonRenderer();
        $this->getResponse()->addOutput($renderer->render($result["message"]));
    }
    
    
    public function inviaWhatsApp() 
    {
        $result = array();
        $contatto = new DBContatti();
        $contatto->setFromRequest($this->getRequest());

        $this->sendMessageInternal($contatto, $result, self::MSG_TYPE_WHATSAPP);

        $renderer = new JsonRenderer();
        $this->getResponse()->addOutput($renderer->render($result));
    }    

    
    private function sendMessageInternal($contatto, &$result, $type)
    {
        $sender = $this->getAuth()->getCurrentUser();
        $result = array(
            "message" => null,
            "link" => null,
        );

        if ($contatto->id == null) {
            $result["message"] = "Errore: impossibile ottenere informazioni sulla chiamata";
            return false;
        } else if ($contatto->id_utente_destinatario == null) {
            $result["message"] = "Errore: manca il destinatario";
            return false;
        } else {
            $tipoContatto = DBClassificazioni::getDes($contatto->id_tipo_contatto);
            $tipoRichiesta = DBClassificazioni::getDes($contatto->id_tipo_richiesta);
            $tipoImmobile = DBClassificazioni::getDes($contatto->id_tipo_immobile);
            $fontePubblicita = DBClassificazioni::getDes($contatto->id_fonte_pubblicita);
            $camere = DBClassificazioni::getDes($contatto->id_camere);
            $motivoRichiesta = DBClassificazioni::getDes($contatto->id_motivo_richiesta);
            setlocale(LC_MONETARY, 'it_IT');
            $prezzo = money_format('%.2n', $contatto->prezzo);
            $prezzoMin = money_format('%.2n', $contatto->prezzo_min);
            $prezzoMax = money_format('%.2n', $contatto->prezzo_max);
                    
                      
            $conf = App::getInstance()->getConfiguration();
            $user = DBUtenti::getUser($contatto->id_utente_destinatario);
            $ts = strtotime($contatto->data);
            $data = date("d/m/Y", $ts);
            $nominativo = strtoupper("{$contatto->titolo_chiamante} {$contatto->cognome_chiamante} {$contatto->nome_chiamante}");
            $ora = date("H:i", $ts);
            $msg = <<<EOF
Il giorno $data alle ore $ora ti ha cercato $nominativo telefono {$contatto->telefono_chiamante} {$contatto->email_chiamante}
EOF;
            if ($contatto->id_maximizer != null) {
                if (preg_match('/^\d+/', $contatto->id_maximizer)) {
                    $prefix = 'www.demo.it/';
                }
                $msg .= " per l'immobile $prefix{$contatto->id_maximizer}";
            }
            
            $msg .= <<<EOF
\n
Tipo contatto: {$tipoContatto}
Tipo richiesta: {$tipoRichiesta}
Motivo richiesta: {$motivoRichiesta}
Tipo immobile: {$tipoImmobile}
Comune: {$contatto->comune}
Zona o frazione: {$contatto->zona}
Camere o altro: {$camere}
Prezzo: {$prezzo}
Superficie min: {$contatto->superficie_min}
Superficie max: {$contatto->superficie_max}
Prezzo_min: {$prezzoMin}
Prezzo_max: {$prezzoMax}
Fonte pubblicità: {$fontePubblicita}
Note: {$contatto->note}
EOF;


            if ($type == self::MSG_TYPE_EMAIL) {
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
                $mailer->Subject = "Segreteria";
                $mailer->IsHTML(false);
                $mailer->WordWrap = 80;
                $mailer->Body = $msg;

                //var_dump($mailer->Body); exit();

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
                    $result["message"] = "Errore: {$mailer->ErrorInfo}";
                    return false;
                } else {
                    $result["message"] = "Email inviata con successo";
                    //$chiamata->debugLevel(5);
                    $contatto->data_email_destinatario = date("Y-m-d H:i");
                    $contatto->save();
                    return true;
                }
            } else if ($type == self::MSG_TYPE_WHATSAPP) {
                if (!$user->cellulare) {
                    $result["message"] = "Per l'utente destinatario non è stato impostato il numero di cellulare";
                    return false;
                }
                
                $number = preg_replace('/[^\d]/', '', $user->cellulare);
                $text = rawurlencode($msg);
                //$result = "https://wa.me/$number?text=$text";
                $result["link"] = "https://web.whatsapp.com/send?phone=$number&text=$text";
                
                $contatto->data_wa_destinatario = date("Y-m-d H:i");
                $contatto->save();
                
                return true;
            }
        }

        return false;
    }

}
