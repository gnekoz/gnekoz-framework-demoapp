{extends file="layout.tpl"} 
{block name="content"}
<h1>Elenco uffici</h1>
<a class="row-add" href="{web_root}/ufficio" title="Aggiungi nuovo ufficio">aggiungi nuovo ufficio</a>
<table>
	<thead>
		<tr>
			<th width="350">descrizione</td>
			<th width="28">&nbsp;</td>
			<th width="28">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		{foreach $uffici as $ufficio}
		<tr class="{cycle values='odd,even'}">
			<td>{$ufficio->des}</td>
			<td><a class="row-icon-link row-edit" href="{web_root}/ufficio/edit?id={$ufficio->id}" title="Modifica"></a></td>
			<td><a class="row-icon-link row-delete" href="{web_root}/ufficio/delete?id={$ufficio->id}" title="Elimina"></a></td>
		</tr>	
		{/foreach}
	</tbody>
	  {*
    <tfoot>
    	<tr>
      		<td colspan="6">
        		{if $page > 0}
        		<a class="table-page-link prev-page" href="{web_root}/uffici?page={$page - 1}" title="Pagina precedente">pagina precedente</a>
        		{/if}
        		{if $morePages == true}
        		<a class="table-page-link next-page" href="{web_root}/uffici?page={$page + 1}" title="Pagina successiva">pagina successiva</a>
        		{/if}
      		</td>
    	</tr>
  	</tfoot>
  	*}	
</table>
{/block}