{extends file="layout.tpl"} 
{block name="content"}
    <h1>Report consuntivazione</h1>
    <form id="report-form" action="{web_root}/report/consuntivazione" method="post">
        <ol>		
            <li>
                {*
                <label for="mese">
                mese
                <select id="mese" name="mese">		          
                {html_options options=$months selected=$smarty.now|date_format:"%m"}
                </select>						        
                </label>
                *}

                <label for="anno">
                    anno
                    <input type="text" name="anno" value="{$smarty.now|date_format:"%Y"}" size="5" maxlength="4"/>
                </label>
            </li>		

            {if $users|@count > 1}
                <li>			
                    <label for="id_utente">
                        utente
                        <select id="id_utente" name="id_utente">
                            <option value="">-- tutti gli utenti --</option>
                            {html_options options=$users}
                        </select>						        
                    </label>
                </li>
            {/if}

            {if $brokerManagers|@count > 0}
                <li>			
                    <label for="id_broker_manager">
                        broker manager
                        <select id="id_broker_manager" name="id_broker_manager">
                            <option value="">-- tutti i broker manager --</option>
                            {html_options options=$brokerManagers}
                        </select>						        
                    </label>
                </li>
            {/if}        

            {if $offices|@count > 0}
                <li>
                    <label for="id_ufficio">ufficio      
                        <select id="id_ufficio" name="id_ufficio">
                            <option value="">-- tutti gli uffici --</option>             
                            {html_options options=$offices}
                        </select>
                    </label>
                </li>
            {/if}

            {* Se è presente più di un utente significa che è un broker 
            titolare o un broker manager quindi può stampare anche il
            riepilogo generale *}
            {if $users|@count > 1}
                <li>
                    {html_radios name="report_type" options=$reportTypes selected=$defaultReportType separator="</li><li>"}
                </li>
            {/if}                                

            <li>
                <button class="create-xls icon-button" value="consuntivazione" type="submit">crea report consuntivazione</button>	
            </li>
        </ol>
    </form>
{/block}
