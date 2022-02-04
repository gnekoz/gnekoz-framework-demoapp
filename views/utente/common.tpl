{extends file="layout.tpl"} 
{block name="content"}
    <h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="user-form" action="{web_root}/utente/save" method="post">
    <ol>
        <li>
            <label for="nominativo">nominativo
                <input id="nominativo" name="nominativo" type="text" value="{$user->nominativo}" size="40"/>
            </label>

            <label for="username">username
                <input id="username" name="username" type="text" value="{$user->username}" size="40"/>
            </label>

            <label for="email">indirizzo email
                <input id="email" name="email" type="text" value="{$user->email}" size="40"/>
            </label>								            

            <label for="cellulare">cellulare
                <input id="cellulare" name="cellulare" type="text" value="{$user->cellulare}" size="20"/>
            </label>            
            
        </li>

        <li>	
            <label for="pass">password
                <input id="pass" name="pass" type="password" value="" size="40"/>
            </label>

            <label for="repass">conferma password
                <input id="repass" name="repass" type="password" value="" size="40"/>
            </label>			
            
            <label for="flg_disabilitato">stato
                <select id="flg_disabilitato" name="flg_disabilitato"">
                    <option value="0">attivo</option>
                    <option value="1" {if $user->flg_disabilitato == 1} selected="selected"{/if}>disabilitato</option>
                </select>	
            </label>								                        
        </li>

        <li>  
            <label for="id_ufficio">ufficio      
                <select id="id_ufficio" name="id_ufficio">
                    <option value="">-- scegliere un ufficio --</option>             
                    {html_options options=$uffici selected=$user->id_ufficio}
                </select>
            </label>

            <label for="id_responsabile">responsabile      
                <select id="id_responsabile" name="id_responsabile">
                    <option value="">-- scegliere un responsabile --</option>
                    {foreach from=$allUsers key=ruolo item=utenti}
                        <optgroup label="{$ruolo}">             
                            {html_options options=$utenti selected=$user->id_responsabile}
                        </optgroup>
                    {/foreach}
                </select>
            </label>
        </li>		
        <li>	
            <label for="user-roles">ruoli assegnati all'utente			
                <select id="user-roles" name="user-roles[]" multiple="multiple" size="{$allRoles|@count}">
                    {html_options options=$userRoles}
                </select>	
            </label>

            <label for="all-roles">ruoli assegnabili
                <select id="all-roles" multiple="multiple" size="{$allRoles|@count}">
                    {html_options options=$allRoles}
                </select>
            </label>
        </li>

        <li>
            <label for="budget_importo">budget per l'anno in corso<br/>
                <input type="hidden" name="budget_id" value="{$budget->id}"/>
                <input id="budget_importo" name="budget_importo" type="text" value="{$budget->importo|number_format:2:",":"."}" size="10"/>
            </label>            
        </li>

        <li>	
            <input id="id" name="id" type="hidden" value="{$user->id}"/>	
            <input class="icon-button save-form" type="submit" value="Salva le modifiche"/>
        </li>
    </ol>	

</form>
{/block}