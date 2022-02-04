<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>ReMax :: Login </title> 
    <link rel="stylesheet" type="text/css" href="{web_root}/css/common.css" />
    <link rel="stylesheet" type="text/css" href="{web_root}/css/forms.css" />
    <link rel="stylesheet" type="text/css" href="{web_root}/css/login.css" />
    <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <script type="text/javascript" language="javascript">
    <!--
      $(document).ready(function() {
        $('#username').focus();
      });
    -->
    </script>
  </head>
  <body>
    <h1>Autenticazione</h1>
            
    <form id="login-form" method="post" action="{web_root}/login/check">
      <ol>
        <li>
          <img src="{web_root}/images/logo_big.png"/>
        </li>      
        <li>
          <label for="username">
            username
            <input name="username" id="username" type="text" value=""/>
          </label>
        </li>
        <li>
          <label for="password">
            password
            <input id="password" name="password" type="password" value=""/>
          </label>
        </li>
        <li>
          {block name="messaggi"}{/block}
        </li>
        <li>
          <input id="loginbtn" type="submit" value ="accedi"/>
        </li>
      </ol>
    </form>    
    
    <p id="footer"></p>
  </body>
</html>