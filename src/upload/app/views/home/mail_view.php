<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>{game_name}</title>
        <link rel="stylesheet" type="text/css" href="{css_path}reset.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="{css_path}recover.css" media="screen" />

    </head>

    <body id="login">

        <form method="post" name="recoverpassword">
            <h1><span>{game_name}</span></h1>
            <div id="error" style="{display}">
                <p>{error_msg}</p>
            </div>
            <div id="loginwrapper">
                <h2>{ma_send_pwd_title}</h2>
                <div class="textLeft wrap-inner">
                    <label for="login">{ma_label}</label>
                    <input type="text" name="email" id="login" tabindex="1" class="input" />
                    <input type="submit" value="{ma_value}" tabindex="2" class="start" />
                </div>
                <div id="advice">
                    <p>{ma_advice}</p>
                </div>
                <br class="clear" />
            </div>
        </form>

    </body>

</html>
