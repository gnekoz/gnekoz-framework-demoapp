{extends file="layout.tpl"} 
{block name="content"}
<h1>Elenco utenti</h1>
<a class="row-add" href="{web_root}/utente" title="Aggiungi nuovo utente">aggiungi nuovo utente</a>
<table>
	<thead>
		<tr>
			<th width="180">nominativo</td>
			<th width="150">ufficio</td>
			<th width="200">email</td>
			<th width="300">ruoli</td>
			<th width="28">&nbsp;</td>
			<th width="28">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		{foreach $users as $user}                    
                {if $user->flg_disabilitato == 1}
                    {assign "class" "disabled"}
                {else}
                    {assign "class" ""}
                {/if}
		<tr class="{cycle values='odd,even'} {$class}">
			<td>{$user->nominativo}</td>
			<td>{$user->ufficioDes}</td>
			<td><a class="row-icon-link send-email" href="mailto:{$user->email}" title="Invia email a {$user->nominativo}">{$user->email}</a></td>
			<td>{$user->rolesDes}</td>
			<td><a class="row-icon-link row-edit" href="{web_root}/utente/edit?id={$user->id}" title="Modifica"></a></td>
			<td><a class="row-icon-link row-delete" href="{web_root}/utente/delete?id={$user->id}" title="Elimina"></a></td>
		</tr>	
		{/foreach}
	</tbody>
</table>
{/block}