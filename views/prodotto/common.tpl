{extends file="layout.tpl"} 
{block name="content"}
<h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="product-form" action="{web_root}/prodotto/save" method="post">
	<ol>
		<li>
			<label for="des">descrizione
        <input id="des" name="des" type="text" value="{$product->des}" size="40"/>
			</label>
		</li>
		<li>	
			<label for="prezzo">prezzo unitario
			  <input id="prezzo" name="prezzo" type="text" value="{$product->prezzo|number_format:2:",":"."}" size="10"/>
			</label>	
		</li>
		<li>	
			<input id="id" name="id" type="hidden" value="{$product->id}"/>	
			<input class="icon-button save-form" type="submit" value="Salva le modifiche"/>
		</li>
	</ol>	
	
</form>
{/block}