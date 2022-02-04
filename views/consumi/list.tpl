{extends file="layout.tpl"} 
{block name="content"}

<h1>Elenco consumi {if $utente->id == null}collaboratori{else}di {$utente->nominativo}{/if}</h1>

<form id="utenti-search-form" action="{web_root}/consumi" method="post">
<ul class="search-filters clearfix">
  <li>
    <label for="id_utente">utente
      <select id="id_utente" name="id_utente">
          <option value="">(tutti gli utenti)</option>
          {html_options options=$allUsers selected=$utente->id}      
      </select>
    </label>
  </li>
  
  <li>
    <label>&nbsp;
      <input class="icon-button search-form" type="submit" value="Cerca"/>
    </label>
  </li>
</ul>
</form>

<a class="row-add" href="{web_root}/consumo" title="Registra nuovo consumo">registra nuovo consumo</a>

<table id="consumi">
  <thead>
    <tr>
      <th width="90">data</td>
      <th width="200">utente</td>
      <th width="300">prodotto</td>
      <th width="90">quantità</td>
      <th width="120">importo</td>
      <th width="60">addebitato</td>
      <th width="28">&nbsp;</td>
      <th width="28">&nbsp;</td>
    </tr>
  </thead>
  <tbody>
    {foreach $consumi as $consumo}
    <tr class="{cycle values='odd,even'}">
      <td>{$consumo->data|date_format:"%d/%m/%Y"}</td>
      <td>{$consumo->utente}</td>
      <td>{$consumo->prodotto}</td>
      <td>{$consumo->quantita|number_format:2:",":"."}</td>
      <td>{$consumo->importo|number_format:2:",":"."}</td>
      <td><input class="consumo-flg-addebitato" consumo-id="{$consumo->id}" type="checkbox" {if $consumo->flg_addebitato == 1}checked="checked"{else}{/if}></td>
      <td><a class="row-icon-link row-edit" href="{web_root}/consumo/edit?id={$consumo->id}" title="Modifica"></a></td>
      <td><a class="row-icon-link row-delete" href="{web_root}/consumo/delete?id={$consumo->id}" title="Elimina"></a></td>
    </tr> 
    {/foreach}
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7">
        {if $page > 0}
        <a class="table-page-link prev-page" href="{web_root}/consumi?page={$page - 1}&id_utente={$utente->id}" title="Pagina precedente">pagina precedente</a>
        {/if}
        {if $morePages == true}
        <a class="table-page-link next-page" href="{web_root}/consumi?page={$page + 1}&id_utente={$utente->id}" title="Pagina successiva">pagina successiva</a>
        {/if}
      </td>
    </tr>
  </tfoot>
</table>
{/block}
