{extends file="layout.tpl"} 
{block name="content"}
<h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="chiamata-form" action="{web_root}/chiamata/save" method="post">
	<ol>
		<li>
			<label for="data">data
        		<input id="data" name="data" type="text" value="{$chiamata->data|date_format:"%d/%m/%Y %H:%M"}"/>
			</label>
			
    		<label for="id_utente_destinatario">destinatario
      		<select id="id_utente_destinatario" name="id_utente_destinatario">
        			<option value="">-- scegliere un destinatario --</option>
        			{html_options options=$utenti selected=$chiamata->id_utente_destinatario}
      		</select>
    		</label>
		</li>
		<li>	
			<label for="nominativo_chiamante">nominativo chiamante
			  <input id="nominativo_chiamante" name="nominativo_chiamante" type="text" value="{$chiamata->nominativo_chiamante}"/>
			</label>	
			
			<label for="telefono_chiamante">telefono chiamante
			  <input id="telefono_chiamante" name="telefono_chiamante" type="text" value="{$chiamata->telefono_chiamante}"/>
			</label>			
			
			<label for="email_chiamante">email chiamante
			  <input id="email_chiamante" name="email_chiamante" type="text" value="{$chiamata->email_chiamante}"/>
			</label>			
		</li>
		<li>	
			<label for="immobile">immobile
			  <input id="immobile" name="immobile" type="text" value="{$chiamata->immobile}"/>
			</label>	
			
			<label for="pubblicita">pubblicità
			  <input id="pubblicita" name="pubblicita" type="text" value="{$chiamata->pubblicita}"/>
			</label>						
		</li>
		<li>
			<label for="note">note
			  <textarea id="note" name="note">{$chiamata->note}</textarea>
			</label>		
		</li>						
		<li>	
			<input id="id" name="id" type="hidden" value="{$chiamata->id}"/>	
            <label>
            <input name="save_new" class="icon-button save-form" type="submit" value="Salva"/>
            </label>
            <label>
            <input name="save_send" class="icon-button save-form" type="submit" value="Salva e invia email"/>
            <label>                
		</li>
	</ol>	
	
</form>
{/block}