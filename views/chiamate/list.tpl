{extends file="layout.tpl"} 
{block name="content"}
{*<h1>Elenco chiamate</h1>*}
{*<a class="row-add" href="{web_root}/chiamata" title="Registra nuova chiamata">registra nuova chiamata</a>*}
<table>
  <thead>
    <tr>
      <th width="28">&nbsp;</td>
      <th width="150">destinatario</td>
      <th width="90">data e ora</td>
      <th width="200">chiamante</td>
      <th width="120">telefono</td>
      <th width="120">immobile</td>
      <th width="28">&nbsp;</td>
      <th width="28">&nbsp;</td>
    </tr>
  </thead>
  <tbody>
    {foreach $chiamate as $chiamata}
    <tr class="{cycle values='odd,even'}">
      <td>
        <input type="hidden" name="id" value="{$chiamata->id}"/>
        {assign var="emailLinkClass" value="send-email"}
        {assign var="emailLinkText" value="Invia email al destinatario"}
        {if $chiamata->data_email_destinatario != null}
            {assign var="emailLinkClass" value="email-sent"}
            {assign "emailLinkText" "Email inviata al destinatario il "|cat:{$chiamata->data_email_destinatario|date_format:"%d/%m/%Y %H:%M"}}
        {/if}
        <a class="row-icon-link {$emailLinkClass} send-notification" title="{$emailLinkText}"></a>
      </td>
      <td>{$chiamata->nome_destinatario}</td>
      <td>{$chiamata->data|date_format:"%d/%m/%Y %H:%M"}</td>
      <td>{$chiamata->nominativo_chiamante}</td>
      <td>{$chiamata->telefono_chiamante}</td>
      <td>{$chiamata->immobile}</td>
      <td><a class="row-icon-link row-edit" href="{web_root}/chiamata/edit?id={$chiamata->id}" title="Modifica"></a></td>
      <td><a class="row-icon-link row-delete" href="{web_root}/chiamata/delete?id={$chiamata->id}" title="Elimina"></a></td>
    </tr> 
    {/foreach}
  </tbody>
  <tfoot>
    <tr>
      <td colspan="9">
        {if $page > 0}
        <a class="table-page-link prev-page" href="{web_root}/chiamate?page={$page - 1}" title="Pagina precedente">pagina precedente</a>
        {/if}        
        {if $morePages == true}
        <a class="table-page-link next-page" href="{web_root}/chiamate?page={$page + 1}" title="Pagina successiva">pagina successiva</a>
        {/if}
      </td>
    </tr>
  </tfoot>
</table>
{/block}