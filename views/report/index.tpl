{extends file="layout.tpl"}
{block name="content"}
<h1>Elenco report</h1>
<ul class="report-list">
        <li><a href="{web_root}/report/chiamate" class="report-link create-xls">Report chiamate</a></li>
        <li><a href="{web_root}/report/contatti" class="report-link create-xls">Report contatti</a></li>
	<li><a href="{web_root}/report/consuntivazione" class="report-link create-xls">Report consuntivazione</a></li>
	<li><a href="{web_root}/report/consumi" class="report-link create-xls">Report consumi</a></li>
</ul>
{/block}