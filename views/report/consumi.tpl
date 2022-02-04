{extends file="layout.tpl"} 
{block name="content"}
<h1>Report consumi</h1>
<form id="report-consumi-form" action="{web_root}/report/consumi" method="post">
	<ol>
		<li>			
			<label for="id_utente">
				utente
		        <select id="id_utente" name="id_utente">
		          <option value="">-- tutti gli utenti --</option>
		          {html_options options=$users}
		        </select>						        
			</label>
		</li>
        
        <li>
            <label for="from">
                data iniziale
                <input type="hidden" name="from-date" value="{$smarty.now|date_format:"%Y-%m-01"}"/>
                <input id="from" type="text" class="date-picker" value="{$smarty.now|date_format:"01/%m/%Y"}"/>
            </label>            
            <label for="to">
                data finale
                <input type="hidden" name="to-date" value="{$smarty.now|date_format:"%Y-%m-%d"}"/>
                <input id="to" type="text" class="date-picker" value="{$smarty.now|date_format:"%d/%m/%Y"}"/>
            </label>                        
        </li>

		<li>			
			<label for="flg_addebitati">
				stato addebito
		        <select id="flg_addebitati" name="flg_addebitati">
		          {html_options options=$tipiAddebito}
		        </select>						        
			</label>
		</li>
        
		<li>
			<button class="create-xls icon-button" value="consumi" type="submit">crea report consumi</button>	
		</li>
	</ol>		
</form>
{/block}