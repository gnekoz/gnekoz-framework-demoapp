<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>ReMax</title>
        <base href="{web_root}" target="_self"/>
        <meta charset="utf-8"/>
        <link rel="stylesheet" type="text/css" href="{web_root}/css/main.css" />
        <link rel="stylesheet" type="text/css" href="{web_root}/css/tables.css" />
        <link rel="stylesheet" type="text/css" href="{web_root}/css/forms.css" />
        <link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css"/>
        <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
        <script src="http://jquery-ui.googlecode.com/svn/tags/latest/ui/i18n/jquery.ui.datepicker-it.js"></script>
        <script src="{web_root}/js/consuntivazioni.js"></script>
        <script src="{web_root}/js/utenti.js"></script>
        <script src="{web_root}/js/consumi-1.1.js"></script>
        <script src="{web_root}/js/chiamate.js"></script>
        <script src="{web_root}/js/contatti-1.2.js"></script>
        <script src="{web_root}/js/report-1.2.js"></script>
        <script src="{web_root}/js/tables.js"></script>
    </head>
    <body>
        <div id="header">
            <nav id="top-menu">
                Utente <strong>{$currentUser->nominativo}</strong>
                <ul class="clearfix">
                    <li><a href="{web_root}/profilo" title="Il tuo profilo">profilo utente</a></li>
                    <li><a href="{web_root}/logout" title="Esci dal programma">logout</a></li>
                </ul>
            </nav>

            <a href="{web_root}/home" title="Menù iniziale">
                <img src="{web_root}/images/logo.png"/>
            </a>
        </div>

        {block name="main-menu"}
            <nav id="main-menu">
                <ul class="clearfix">
                    <li>
                        <a href="javascript://" title="Amministrazione">amministrazione</a>
                        <ul>
                            <li><a class="menu-item uffici" href="{web_root}/uffici" title="Uffici">uffici</a></li>
                            <li><a class="menu-item utenti" href="{web_root}/utenti" title="Utenti">utenti</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript://" title="Consumi">consumi</a>
                        <ul>
                            <li><a class="menu-item prodotti" href="{web_root}/prodotti" title="Prodotti">gestione prodotti</a></li>
                            <li><a class="menu-item consumi" href="{web_root}/consumi" title="Consumi">registrazione consumi</a></li>
                        </ul>
                    </li>

                    <li><a class="menu-item chiamate" href="{web_root}/chiamate" title="Chiamate">chiamate</a></li>
                    
                    <li>
                        <a class="menu-item contatti" href="{web_root}/contatti" title="Contatti">contatti</a>
                        <ul>                            
                            <li><a class="menu-item contatti" href="{web_root}/contatti" title="Contatti">contatti</a></li>
                            <li>
                                <a href="javascript://" title="Configurazione">configurazione</a>
                                <ul>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=0" title="Tipi contatto">tipi contatto</a></li>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=1" title="Tipi richiesta">tipi richiesta</a></li>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=2" title="Motivo richiesta">motivo richiesta</a></li>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=3" title="Tipi immobile">tipi immobile</a></li>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=4" title="Fonti pubblicità">fonti pubblicità</a></li>
                                    <li><a class="menu-item" href="{web_root}/classificazioni?tipo=5" title="Camere o altro">camere o altro</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li><a class="menu-item consuntivazione" href="{web_root}/consuntivazione" title="Consuntivazione">consuntivazione</a></li>
                    <li><a class="menu-item report" href="{web_root}/report" title="Report">report</a></li>
                </ul>
            </nav>
        {/block}
        <div id="content">
            {block name="content"}content{/block}
        </div>

        <div id="footer"></div>
    </body>