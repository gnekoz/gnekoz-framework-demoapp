{extends file="consuntivazione/list.tpl"} 
{block name="messaggi"}
<ul class="error-list">
	{foreach $errors as $error}
		<li>{$error}</li>
	{/foreach}
</ul>
{/block}
