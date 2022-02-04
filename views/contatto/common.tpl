{extends file="layout.tpl"} 
{block name="content"}
    <h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="contatto-form" action="{web_root}/contatto/save" method="post">
    <ol>
        <li>
            <label for="data">data
                <input id="data" name="data" type="text" value="{$contatto->data|date_format:"%d/%m/%Y %H:%M"}"/>
            </label>

            <label for="id_utente_destinatario">destinatario
                <select id="id_utente_destinatario" name="id_utente_destinatario">
                    <option value="">-- scegliere un destinatario --</option>
                    {html_options options=$utenti selected=$contatto->id_utente_destinatario}
                </select>
            </label>

            <label for="id_tipo_contatto">tipo contatto
                <select id="id_tipo_contatto" name="id_tipo_contatto">
                    <option value="">-- scegliere il tipo di contatto --</option>
                    {html_options options=$tipiContatto selected=$contatto->id_tipo_contatto}                          
                </select>
            </label>

            <label for="id_tipo_richiesta">tipo richiesta
                <select id="id_tipo_richiesta" name="id_tipo_richiesta">
                    <option value="">-- scegliere il tipo di richiesta--</option>
                    {html_options options=$tipiRichiesta selected=$contatto->id_tipo_richiesta}                          
                </select>
            </label>

            <label for="id_motivo_richiesta">motivo richiesta
                <select id="id_motivo_richiesta" name="id_motivo_richiesta">
                    <option value="">-- scegliere il motivo di richiesta--</option>
                    {html_options options=$motiviRichiesta selected=$contatto->id_motivo_richiesta}                          
                </select>
            </label>
            <label for="id_maximizer">ID Maximizer
                <input id="id_maximizer" name="id_maximizer" type="text" value="{$contatto->id_maximizer}"/>
            </label>
        </li>

        <li>                        
            <label for="id_tipo_immobile">tipo immobile
                <select id="id_tipo_immobile" name="id_tipo_immobile">
                    <option value="">-- scegliere il tipo di immobile--</option>
                    {html_options options=$tipiImmobile selected=$contatto->id_tipo_immobile}
                </select>
            </label>
            <label for="comune">comune
                <input id="comune" name="comune" type="text" value="{$contatto->comune}"/>
            </label>
            <label for="zona">zona o frazione
                <input id="zona" name="zona" type="text" value="{$contatto->zona}"/>
            </label>            
            <label for="id_camere">camere o altro
                <select id="id_camere" name="id_camere">
                    <option value=""></option>
                    {html_options options=$camere selected=$contatto->id_camere}
                </select>
            </label>
            <label for="prezzo">prezzo immobile
                <input id="prezzo" name="prezzo" type="number" value="{$contatto->prezzo}" size="10" step="0.01"/>
            </label>
        </li>                

        <li>
            <label for="superficie_min">superficie min (mq)
                <input id="superficie_min" name="superficie_min" type="number" value="{$contatto->superficie_min}" size="5"/>
            </label>
            <label for="superficie_max">superficie max (mq)
                <input id="superficie_max" name="superficie_max" type="number" value="{$contatto->superficie_max}" size="5"/>
            </label>
            <label for="prezzo_min">prezzo min
                <input id="prezzo_min" name="prezzo_min" type="number" value="{$contatto->prezzo_min}" size="10" step="0.01"/>
            </label>
            <label for="prezzo_max">prezzo max
                <input id="prezzo_max" name="prezzo_max" type="number" value="{$contatto->prezzo_max}" size="10" step="0.01"/>
            </label>
        </li>                

        <li>
            <label for="titolo_chiamante">titolo chiamante
                <select id="titolo_chiamante" name="titolo_chiamante">
                    <option value=""></option>
                    <option value="Sig.">Sig.</option>
                    <option value="Sig.ra">Sig.ra</option>
                </select>                          
            </label>	

            <label for="cognome_chiamante">cognome chiamante
                <input id="cognome_chiamante" name="cognome_chiamante" type="text" value="{$contatto->cognome_chiamante}"/>
            </label>

            <label for="nome_chiamante">nome chiamante
                <input id="nome_chiamante" name="nome_chiamante" type="text" value="{$contatto->nome_chiamante}"/>
            </label>
            <label for="telefono_chiamante">telefono chiamante
                <input id="telefono_chiamante" name="telefono_chiamante" type="text" value="{$contatto->telefono_chiamante}"/>
            </label>			

            <label for="email_chiamante">email chiamante
                <input id="email_chiamante" name="email_chiamante" type="text" value="{$contatto->email_chiamante}"/>
            </label>			
        </li>        

        <li>
            <label for="id_fonte_pubblicita">fonte pubblicità
                <select id="id_fonte_pubblicita" name="id_fonte_pubblicita">
                    <option value="">-- scegliere la fonte pubblicita--</option>
                    {html_options options=$fontiPubblicita selected=$contatto->id_fonte_pubblicita}
                </select>
            </label>
            <label for="note">note
                <textarea cols="160" id="note" name="note">{$contatto->note}</textarea>
            </label>		
        </li>						
        <li>	
            <input id="id" name="id" type="hidden" value="{$contatto->id}"/>	
            <label>
                <input name="save_new" class="icon-button save-form" type="submit" value="Salva"/>
            </label>
            <label>
                <input name="save_send_email" class="icon-button save-form" type="submit" value="Salva e invia email"/>
            </label>
            <label>
                <input name="save_send_whatsapp" class="icon-button save-form" type="submit" value="Salva e invia whatsapp"/>
            </label>                
        </li>
    </ol>
    <input id="wa-link" type="hidden" value="{$waLink}"/>
</form>
{/block}