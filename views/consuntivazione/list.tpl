{extends file="layout.tpl"}
{block name="content"}
<h1>Consuntivazione collaboratori</h1>

<form id="consuntivazioni-form" method="post" action="{web_root}/consuntivazione">
<ul class="search-filters clearfix">
  {if count($users) > 1}
  <li>
    <label for="search-user">utente
      <select id="search-user" name="search-user">
          {html_options options=$users selected=$user->id}
      </select>
    </label>
  </li>
  {else}
  <input type="hidden" name="search-user" value="{$user->id}"/>
  {/if}

  <li>
  	<label for="search-date">
  		data
  		<input type="hidden" name="search-date" value="{$date}"/>
  		<input type="text" class="date-picker" value="{$date|date_format:"%d/%m/%Y"}"/>
  	</label>
  </li>

  <li>
    <label>&nbsp;
      <input class="icon-button search-form" type="submit" value="Cerca"/>
    </label>
  </li>

  <li>
    <label>&nbsp;
      <a href="{web_root}/report/consuntivazione?anno={$date|date_format:"%Y"}&id_utente={$user->id}&report_type={$reportType}" class="generic-button button-link icon-button create-xls">Report utente</a>
    </label>
  </li>

  <li>
      <label>&nbsp;
          <input class="icon-button save-form" type="submit" name="save-action" value="Salva le modifiche"/>
      </label>
  </li>

</ul>


{block name="messaggi"}{/block}

<table id="consuntivazioni-table">
  <colgroup>
    <col class="group1" span="1"/>
    <col class="group2" span="4"/>
    <col class="group3" span="3"/>
    <col class="group4" span="4"/>
    <col class="group5" span="2"/>
  </colgroup>
<thead>
	<tr>
		<th rowspan="2">Giorno</td>
                <th colspan="4">&nbsp;</td>

		<th colspan="3">Appuntamenti</td>

		<th colspan="4">Proposte</td>
		<th colspan="2">Transazioni</td>
	</tr>
	<tr>
		<th>Nuovi contatti</th>
		<th>Notizie</th>
		<th>Richieste specifiche</th>
		<th>Incarichi</th>

		<th>Vendita</th>
		<th>Affitto</th>
		<th>Acquisizione</th>

		<th>Acquis.</th>
                <th>Acq. collab.</th>
                <th>Locazione</th>
                <th>Loc. collab.</th>

                <th>Vendita</th>
                <th>Affitto</th>

	</tr>
</thead>
<tbody>
	{foreach from=$list item=cons}
	<tr>
	{include 'consuntivazione/row.tpl' cons=$cons}
	</tr>
	{/foreach}

</tbody>
<tfoot>
	<tr>
		<td colspan="15">
        	<a class="table-page-link prev-page" rel="{$prevDate}" href="javascript://" title="Settimana precedente">settimana precedente</a>
        	<a class="table-page-link next-page" rel="{$nextDate}" href="javascript://" title="Settimana successiva">settimana successiva</a>
		</td>
	</tr>
</tfoot>
</table>
<input type="hidden" name="display-user" value="{$user->id}"/>
<input type="hidden" name="display-date" value="{$date}"/>
{*
<input type="hidden" name="display-office" value="{$office}"/>
<input type="hidden" name="display-month" value="{$month}"/>
<input type="hidden" name="display-year" value="{$year}"/>
<input type="hidden" name="display-people-criteria" value="{$peopleCriteria}"/>
<input type="hidden" name="display-date-criteria" value="{$dateCriteria}"/>
*}
</form>
{/block}