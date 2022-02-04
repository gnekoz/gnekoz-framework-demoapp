{extends file="layout.tpl"} 
{block name="content"}
<h1>Elenco prodotti</h1>
<a class="row-add" href="{web_root}/prodotto" title="Aggiungi nuovo prodotto">aggiungi nuovo prodotto</a>
<table>
	<thead>
		<tr>
			<th width="350">descrizione</td>
			<th width="120">prezzo unitario</td>
			<th width="28">&nbsp;</td>
			<th width="28">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		{foreach $products as $product}
		<tr class="{cycle values='odd,even'}">
			<td>{$product->des}</td>
			<td>{$product->prezzo|number_format:2:",":"."}</td>
			<td><a class="row-icon-link row-edit" href="{web_root}/prodotto/edit?id={$product->id}" title="Modifica"></a></td>
			<td><a class="row-icon-link row-delete" href="{web_root}/prodotto/delete?id={$product->id}" title="Elimina"></a></td>
		</tr>	
		{/foreach}
	</tbody>
    <tfoot>
    	<tr>
      		<td colspan="6">
        		{if $page > 0}
        		<a class="table-page-link prev-page" href="{web_root}/prodotti?page={$page - 1}" title="Pagina precedente">pagina precedente</a>
        		{/if}
        		{if $morePages == true}
        		<a class="table-page-link next-page" href="{web_root}/prodotti?page={$page + 1}" title="Pagina successiva">pagina successiva</a>
        		{/if}
      		</td>
    	</tr>
  	</tfoot>	
</table>
{/block}