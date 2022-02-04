{extends file="layout.tpl"} 
{block name="content"}
<h1>{block name="titolo"}Profilo utente {$user->nominativo}{/block}</h1>
{if count($errors) > 0}
<ul class="error-list">
	{foreach $errors as $error}
		<li>{$error}</li>
	{/foreach}
</ul>    
{/if}
<form id="profile-form" action="{web_root}/profilo" method="post">
	<dl class="clearfix">
        <dt>email</dt>
        <dd>{$user->email}</dd>        
        
        <dt>ufficio</dt>
        <dd>{$ufficio->des}</dd>        
        
        <dt>ruoli</dt>
        <dd>{$roles}</dd>                        
        
        <dt>username</dt>
        <dd>{$user->username}</dd>        
        
        <dt>password</dt>
        <dd><input id="password" name="password" type="password" value="" size="40"/></dd>                
        
        <dt>conferma password</dt>
        <dd><input id="repassword" name="repassword" type="password" value="" size="40"/></dd>        
    </dl>
    <input class="icon-button save-form" type="submit" value="Salva le modifiche"/>
	
</form>
{/block}