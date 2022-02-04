{extends file="layout.tpl"} 
{block name="content"}
    <h1>{block name="titolo"}titolo{/block}</h1>
{block name="messaggi"}{/block}
<form id="classificazione-form" action="{web_root}/classificazione/save" method="post">
    <ol>
        <li>
            <label for="des">descrizione
                <input id="des" name="des" type="text" value="{$classificazione->des}" size="40"/>
            </label>
        </li>
        <li>	
            <input id="id" name="id" type="hidden" value="{$classificazione->id}"/>
            <input id="tipo" name="tipo" type="hidden" value="{$classificazione->tipo}"/>
            <input class="icon-button save-form" type="submit" value="Salva le modifiche"/>
        </li>
    </ol>	

</form>
{/block}