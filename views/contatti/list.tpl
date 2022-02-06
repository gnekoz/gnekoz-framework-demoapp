{extends file="layout.tpl"} 
{block name="content"}
{*<h1>Elenco contatti</h1>*}
<a class="row-add" href="{web_root}/contatto" title="Registra nuovo contatto">registra nuovo contatto</a>
<table>
  <thead>
    <tr>
      <th width="28">&nbsp;</td>
      <th width="28">&nbsp;</td>
      <th width="150">destinatario</td>
      <th width="90">data e ora</td>
      <th width="120">tipo contatto</td>
      <th width="120">tipo richiesta</td>
      <th width="200">nome chiamante</td>
      <th width="120">telefono chiamante</td>
      <th width="120">email chiamante</td>
      <th width="120">ID Maximizer</td>      
      <th width="28">&nbsp;</td>
      <th width="28">&nbsp;</td>
    </tr>
  </thead>
  <tbody>
    {foreach $contatti as $contatto}
    <tr class="{cycle values='odd,even'}">
      <td>
        <input type="hidden" name="id" value="{$contatto->id}"/>
        {assign var="emailLinkClass" value="send-email"}
        {assign var="emailLinkText" value="Invia email al destinatario"}
        {if $contatto->data_email_destinatario != null}
            {assign var="emailLinkClass" value="email-sent"}
            {assign "emailLinkText" "Email inviata al destinatario il "|cat:{$contatto->data_email_destinatario|date_format:"%d/%m/%Y %H:%M"}}
        {/if}
        <a class="row-icon-link {$emailLinkClass} send-notification" title="{$emailLinkText}"></a>        
      </td>
      <td>
          <input type="hidden" name="id" value="{$contatto->id}"/>
          <a class="row-icon-link send-whatsapp send-wa-notification" title="Invia notifica WhatsApp" href="" target="_blank"></a>
      </td>
      <td>{$contatto->nome_destinatario}</td>
      <td>{$contatto->data|date_format:"%d/%m/%Y %H:%M"}</td>
      <td>{$contatto->des_tipo_contatto}</td>
      <td>{$contatto->des_tipo_richiesta}</td>
      <td>{$contatto->titolo_chiamante} {$contatto->cognome_chiamante} {$contatto->nome_chiamante}</td>
      <td>{$contatto->telefono_chiamante}</td>
      <td>{$contatto->email_chiamante}</td>
      <td>{$contatto->id_maximizer}</td>
      <td><a class="row-icon-link row-edit" href="{web_root}/contatto/edit?id={$contatto->id}" title="Modifica"></a></td>
      <td><a class="row-icon-link row-delete" href="{web_root}/contatto/delete?id={$contatto->id}" title="Elimina"></a></td>
    </tr> 
    {/foreach}
  </tbody>
  <tfoot>
    <tr>
      <td colspan="12">
        {if $page > 0}
        <a class="table-page-link prev-page" href="{web_root}/contatti?page={$page - 1}" title="Pagina precedente">pagina precedente</a>
        {/if}        
        {if $morePages == true}
        <a class="table-page-link next-page" href="{web_root}/contatti?page={$page + 1}" title="Pagina successiva">pagina successiva</a>
        {/if}
      </td>
    </tr>
  </tfoot>
</table>
{/block}