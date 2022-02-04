{extends file="layout.tpl"}
{block name="content"}
<h1>Report chiamate</h1>
<form id="report-chiamate-form" action="{web_root}/report/chiamate" method="post">
	<ol>
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
                    <button class="create-xls icon-button" value="chiamate" type="submit">crea report chiamate</button>
		</li>
	</ol>
</form>
{/block}