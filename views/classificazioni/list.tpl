{extends file="layout.tpl"} 
{block name="content"}
<h1>Elenco {$desTipo}</h1>
<a class="row-add" href="{web_root}/classificazione?tipo={$tipo}" title="Aggiungi {$desTipo}">aggiungi nuovo {$desTipo}</a>
<table>
	<thead>
		<tr>
			<th width="350">descrizione</td>
			<th width="28">&nbsp;</td>
			<th width="28">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		{foreach $rows as $row}
		<tr class="{cycle values='odd,even'}">
			<td>{$row->des}</td>
			<td><a class="row-icon-link row-edit" href="{web_root}/classificazione/edit?id={$row->id}" title="Modifica"></a></td>
			<td><a class="row-icon-link row-delete" href="{web_root}/classificazione/delete?id={$row->id}" title="Elimina"></a></td>
		</tr>	
		{/foreach}
	</tbody>
    <tfoot>
    	<tr>
      		<td colspan="6">
        		{if $page > 0}
        		<a class="table-page-link prev-page" href="{web_root}/classificazioni?tipo={$tipo}&page={$page - 1}" title="Pagina precedente">pagina precedente</a>
        		{/if}
        		{if $morePages == true}
        		<a class="table-page-link next-page" href="{web_root}/classificazioni?tipo={$tipo}&page={$page + 1}" title="Pagina successiva">pagina successiva</a>
        		{/if}
      		</td>
    	</tr>
  	</tfoot>	
</table>
{/block}