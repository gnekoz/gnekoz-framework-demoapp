{extends file="layout.tpl"} 
{block name="content"}
    <h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="consumo-form" action="{web_root}/consumo/save" method="post">
    <ol>
        <li>
            <label for="data">data
                <input id="data" name="data" type="text" value="{$consumo->data|date_format:"%d/%m/%Y"}"/>
            </label>

            <label for="id_utente">utente
                <select id="id_utente" name="id_utente">
                    <option value="">-- scegliere un utente --</option>
                    {html_options options=$allUsers selected=$consumo->id_utente}      
                </select>
            </label>			

        </li>
        <li>	

            <label for="id_prodotto">prodotto      
                <select id="id_prodotto" name="id_prodotto">
                    <option value="">-- scegliere un prodotto --</option>
                    {html_options options=$prodotti selected=$consumo->id_prodotto}
                </select>
            </label>      

            <label for=note>descrizione
                <input id="note" name="note" type="text" value="{$consumo->note}" maxlength="300" size="40"/>
            </label>

        </li>
        <li>	


            <label for="prezzo_unitario">prezzo unitario
                <input id="prezzo_unitario" name="prezzo_unitario" type="text" value="{$consumo->prezzo_unitario|number_format:2:",":"."}"/>
            </label>

            <label for="quantita">quantita
                <input id="quantita" name="quantita" type="text" value="{$consumo->quantita|number_format:2:",":"."}"/>
            </label>	


            <label for="importo">importo
                <input id="importo" name="importo" type="text" value="{$consumo->importo|number_format:2:",":"."}" readonly="readonly"/>
            </label>      
        </li>

        <li>	
            <label for="flg_addebitato">addebitato
                <input type="checkbox" id="flg_addebitato" name="flg_addebitato" value="1" {if $consumo->flg_addebitato == 1}checked="checked"{else}{/if}/>
                <input id="hidden_flg_addebitato" type="hidden" value="0" name="flg_addebitato"/>
            </label>
        </li>                

        <li>	
            <input id="id" name="id" type="hidden" value="{$consumo->id}"/>	
            <label>&nbsp;            
                <input name="save" class="icon-button save-form" type="submit" value="Salva"/>
            </label>
            <label>&nbsp;
                <input name="save_new" class="icon-button save-form" type="submit" value="Salva e registra nuovo consumo"/>
            </label>
        </li>
    </ol>	

</form>
{/block}
