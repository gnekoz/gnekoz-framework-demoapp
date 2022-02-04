{extends file="layout.tpl"} 
{block name="content"}
<h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="ufficio-form" action="{web_root}/ufficio/save" method="post">
	<ol>
		<li>
			<label for="des">descrizione
        <input id="des" name="des" type="text" value="{$ufficio->des}" size="40"/>
			</label>
		</li>
		<li>	
			<input id="id" name="id" type="hidden" value="{$ufficio->id}"/>	
			<input class="icon-button save-form" type="submit" value="Salva le modifiche"/>
		</li>
	</ol>	
	
</form>
{/block}