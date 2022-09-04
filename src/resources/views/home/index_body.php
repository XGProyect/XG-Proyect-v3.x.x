<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="language" content="es">
    <meta name="author" content="XG Proyect">
    <meta name="publisher" content="XG Proyect">
    <meta name="copyright" content="XG Proyect">
    <meta name="audience" content="all">
    <meta name="Expires" content="never">
    <meta name="Keywords" content="{hm_keywords}">
    <meta name="Description" content="{hm_description}">
    <meta name="robots" content="index,follow">
    <meta name="Revisit" content="After 14 days">
    <title>{servername}</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" type="text/css" href="{css_path}reset.css">
    <link rel="stylesheet" type="text/css" href="{css_path}forms.css">

    <link rel="stylesheet" type="text/css" href="{css_path}all.css">
    <script type="text/javascript" src="{js_path}jquery.tools.min.js"></script><style type="text/css"></style>
    <script type="text/javascript" src="{js_path}jquery.easing-1.3.pack.js"></script>
    <script type="text/javascript" src="{js_path}jquery.jparallax.js"></script>
    <script type="text/javascript" src="{js_path}jquery.fancybox-1.3.1.pack.js"></script>
    <script type="text/javascript" src="{js_path}jquery.validationEngine.modified.js"></script>
    <script type="text/javascript" src="{js_path}xgproyect.js"></script>
    <script type="text/javascript" src="{js_path}test.js"></script>
    <script type="text/javascript">
        // <![CDATA[
    (function($) {
        $.fn.validationEngineLanguage = function() { };
        $.validationEngineLanguage = {
            newLang: function() {
                $.validationEngineLanguage.allRules = 	{
                        "required":{    			// Add your regex rules here, you can take telephone as an example
                            "regex":"none",
                            "alertText":"{hm_field_required}",
                            "alertTextCheckboxMultiple":"Toma una decisi\u00f3n",
                            "alertTextCheckboxe":"{hm_must_accept_tandc}"},
                        "length":{
                            "regex":"none",
                            "alertText":"{hm_username_length}"},
                        "pwLength":{
                            "regex":"none",
                            "alertText":"{hm_password_length}"},
                        "maxCheckbox":{
                            "regex":"none",
                            "alertText":"* Checks allowed Exceeded"},
                        "minCheckbox":{
                            "regex":"none",
                            "alertText":"* Bitte wähle ",
                            "alertText2":" Optionen"},
                        "confirm":{
                            "regex":"none",
                            "alertText":"* Diese Felder passen nicht zusammen"},
                        "telephone":{
                            "regex":"/^[0-9\-\(\)\ ]+$/",
                            "alertText":"* Unzulässige Telefonnummer"},
                        "email":{
                            "regex":"/^[a-zA-Z0-9_\\.\\-]+\\@([a-zA-Z0-9\\-]+\\.)+[a-zA-Z0-9]{2,4}$/",
                            "alertText":"{hm_valid_email_address}"},
                        "date":{
                             "regex":"/^[0-9]{4}\-\[0-9]{1,2}\-\[0-9]{1,2}$/",
                             "alertText":"* Invalid date, must be in YYYY-MM-DD format"},
                        "onlyNumber":{
                            "regex":"/^[0-9\ ]+$/",
                            "alertText":"* Bitte nur Nummern"},
                        "noSpecialCharacters":{
                            "regex":"/^[a-zA-Z0-9\\s_\\-]+$/",
                            "alertText":"{hm_not_valid_characters}"},
                        "noBeginOrEndUnderscore":{
                            "regex":/^([^_]+(.*[^_])?)?$/,
                            "alertText":"{hm_username_underscore}"},
                        "noBeginOrEndHyphen":{
                            "regex":/^([^\-]+(.*[^\-])?)?$/,
                            "alertText":""},
                        "noBeginOrEndWhitespace":{
                            "regex":/^([^\s]+(.*[^\s])?)?$/,
                            "alertText":"{hm_username_space}"},
                        "notMoreThanThreeUnderscores":{
                            "regex":/^[^_]*(_[^_]*){0,3}$/,
                            "alertText":"{hm_username_many_underscore}"},
                        "notMoreThanThreeHyphen":{
                            "regex":/^[^\-]*(\-[^\-]*){0,3}$/,
                            "alertText":""},
                        "notMoreThanThreeWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]*){0,3}$/,
                            "alertText":"{hm_username_many_spaces}"},
                        "noCollocateUnderscores":{
                            "regex":/^[^_]*(_[^_]+)*_?$/,
                            "alertText":"{hm_username_underscore_continued}"},
                        "noCollocateHyphen":{
                            "regex":/^[^\-]*(\-[^\-]+)*-?$/,
                            "alertText":""},
                        "noCollocateWhitespaces":{
                            "regex":/^[^\s]*(\s[^\s]+)*\s?$/,
                            "alertText":"{hm_username_spaces_continued}"},
                        "ajaxUser":{
                            "file":"../validateUser.php",
                            "alertTextOk":"{hm_username_available}",
                            "alertTextLoad":"{hm_username_loading}",
                            "alertText":"{hm_username_not_available}"},
                        "ajaxName":{
                            "file":"../validateUser.php",
                            "alertTextOk":"{hm_username_available}",
                            "alertTextLoad":"{hm_username_available}"},
                            "alertText":"{hm_username_not_available}",
                        "onlyLetter":{
                            "regex":"/^[a-zA-Z\ \']+$/",
                            "alertText":"{hm_only_characters}"}
                        }
            }
        }
    })(jQuery);
    var universeDistinctions = [];

    $(document).ready(function() {
        $(".zebra tr:odd").addClass("alt");
        $.validationEngineLanguage.newLang()
        $.validationEngine.buildPrompt("{div_id}", "{message}", "error");
                    });
    // ]]>

    </script>

<style type="text/css"> body {margin:0; padding:0;}</style></head>

<body>
	<div id="start">
	    <div id="header">
                <h1>
                	<img src="{game_logo}" width="200px" alt=""/>
                    <a href="./" title="{hm_hidden_title}">
                        {hm_hidden_title}                    </a>
                </h1>

                                                    <a id="loginBtn" href="javascript:void(0)" title="Login">
                        {hm_login_button}                    </a>

                		<div id="login">
		    <form id="loginForm" name="loginForm" method="post" action="index.php">
                <input type="hidden" name="kid" value="">
			<div class="input-wrap">
			    <label for="serverLogin">
                                {hm_universe}                            </label>

			    <div class="black-border">
                                <select class="js_uniUrl" id="serverLogin" name="uni">
                                    <option value="0">{hm_universe_name}</option>
                                </select>
                            </div>
			</div>
		<div class="input-wrap">
						<label for="usernameLogin">{hm_username_mail}</label>
						<div class="black-border">
                            <input class="js_userName" type="text" onkeydown="hideLoginErrorBox();" id="usernameLogin" name="login" value="">
						</div>
					</div>
					<div class="input-wrap">
                        <label for="passwordLogin">{hm_password}</label>
						<div class="black-border">
                            <input type="password" onkeydown="hideLoginErrorBox();" id="passwordLogin" name="pass" maxlength="20">
						</div>
					</div>

                    <input type="submit" id="loginSubmit" value="{hm_login_button}">
					<a href="#" id="pwLost" target="_blank" title="{hm_password_forgot}">{hm_password_forgot}</a>

                    <p id="TermsAndConditionsAcceptWithLogin">
                        {hm_terms_accept} <a class="" href="index.php?page=terms" target="_blank" title="{hm_terms}">{hm_terms}</a>                    </p>
                </form>

			</div>
		</div>

		<div id="content" class="clearfix">
			<div id="subscribe">
                <form id="subscribeForm" name="subscribeForm" method="POST" onsubmit="changeAction(&#39;register&#39;,&#39;subscribeForm&#39;);" action="">
                    <input type="hidden" name="v" value="3">
                    <input type="hidden" name="step" value="validate">
                    <input type="hidden" name="kid" value="">
                    <input type="hidden" name="errorCodeOn" value="1">
                    <input type="hidden" name="is_utf8" value="1">

                                                            <h2>{hm_play_for_free}</h2>
                    <div class="input-wrap first">
                        <label for="server">{hm_universe}</label>


                    <div id="server" style="position:relative;">
        <table cellspacing="0" cellpadding="0" onclick="switch_uni_selection()" onmouseover="this.style.cursor=&#39;pointer&#39;" class="server_table" style="cursor: pointer;">
        <tbody><tr>
            <td id="uni_select_box" class="select" style="height:19px;overflow:hidden;">
                <span id="uni_name" class="">{hm_universe_name}</span>
            </td>
            <td style="width:18px; background: url('{img_path}dropdownmenu_arrow.png') no-repeat scroll 0 0 #8D9AA7;"></td>
        </tr>
        </tbody></table>
        <input class="js_uniUrl" type="hidden" name="uni_url" id="uni_domain" value="">
        <div id="uni_selection" style="display: none;">
                                    <script type="text/javascript">
                        <!--
                            select_uni('{base_path}'.replace('http://', '').replace('https://', ''), '{hm_universe_name}','');
                        //-->
                        </script>

            <div id="row-0" class="server-row " title="" onclick="select_uni('{base_path}','{hm_universe_name}');" onmouseover="highlightRow(&#39;row-0&#39;);this.style.cursor=&#39;pointer&#39;" onmouseout="unHighlightRow(&#39;row-0&#39;);">
                <span class="uni_span ">{hm_universe_name}</span>
            </div>
                    </div>
    </div>
					</div>
					<div class="input-wrap">
                        <label for="username">{hm_username}</label>
						<div class="black-border">

                            <!-- validate options dürfen nicht umgebrochen werden, da das plugin sonst nicht mehr funktioniert  -->
                            <input id="username" class="js_userName validate[required,custom[noSpecialCharacters],custom[noBeginOrEndUnderscore],custom[noBeginOrEndWhitespace],custom[noBeginOrEndHyphen],custom[notMoreThanThreeUnderscores],custom[notMoreThanThreeWhitespaces],custom[notMoreThanThreeHyphen],custom[noCollocateUnderscores],custom[noCollocateWhitespaces],custom[noCollocateHyphen],length[3,20]]" type="text" name="character" value="{user_name}">
						</div>
					</div>
                    <div class="input-wrap">
                        <label for="password">{hm_password}</label>
                        <div class="black-border">
                            <input class="validate[required,pwLength[8,20]]" type="password" id="password" name="password" value="" maxlength="20">
                        </div>
                    </div>
					<div class="input-wrap">
                        <label for="email">{hm_mail_address}</label>
						<div class="black-border">
                            <input class="validate[required,custom[email],length[0,255]]" type="text" id="email" name="email" value="{user_email}">
						</div>
					</div>
					<div class="input-wrap">
                        <div id="securePwd">
							<p>{hm_password_level}</p>
							<div class="valid-icon invalid"></div>
							<div class="securePwdBarBox">
								<div id="securePwdBar"></div>
							</div>
							<br class="clearfloat">
						</div>
					</div>
					<div id="submitWrap">
						<input class="validate[required]" type="checkbox" id="agb" name="agb"/>
                        <label for="agb">
                            <span>{hm_accept} <a class="" target="_blank" href="index.php?page=terms" title="{hm_terms}">{hm_terms}</a> {hm_and} <a class="" target="_blank" href="index.php?page=policy" title="{hm_policy}">{hm_policy}</a></span>
						</label>
                        <div onclick="if($.validationEngine.submitValidation(&#39;subscribeForm&#39;)) {document.forms[&#39;subscribeForm&#39;].submit();}">
                            <input type="submit" onclick="setServerCookie(&#39;subscribeForm&#39;);setUserNameCookie(&#39;subscribeForm&#39;);" id="regSubmit" value="{hm_register}">
                        </div>
					</div>
				</form>
                			</div>
			<div id="contentWrap">
				<div id="menu" style="background-position: 15px -33px;">
					<ul id="tabs">
						<li><a id="tab1" href="ajax.php?content=home" class="current">{hm_home}</a></li>
                        <li><a id="tab2" href="ajax.php?content=info">{hm_about}</a></li>
                        <!--<li><a id="tab3" href="ajax.php?content=media">{hm_media}</a></li>-->
					</ul>
                                            <a id="tab4" href="{forum_url}" target="_blank">{hm_forum}</a>
                                        					<br class="clearfloat">
				</div>
				<div id="tabContent">
					<div id="ajaxContent">
</div>
				</div>
				<div id="contentFooter"></div>
			</div>
		</div>
		<div id="push"></div>
	</div>
	<div id="footer">
	    <div id="footerContent">
            <p id="copyright">Powered by <a href="https://xgproyect.org/" target="_blank" title="XG Proyect {version}">XG Proyect</a> © 2008 - {year}.</p>
        </div>
	</div>

    	<!-- OVERLAY DIVISION -->

<script type="text/javascript">

    JSLoca = new Array('{hm_login_button}', '{hm_close_button}');

    </script>

<script type="text/javascript" src="{js_path}xgproyect.js"></script>
<script type="text/javascript" src="{js_path}xgproyect.start.js"></script>



<div id="fancybox-tmp"></div><div id="fancybox-loading"><div></div></div><div id="fancybox-overlay"></div><div id="fancybox-wrap"><div id="fancybox-outer"><div class="fancy-bg" id="fancy-bg-n"></div><div class="fancy-bg" id="fancy-bg-ne"></div><div class="fancy-bg" id="fancy-bg-e"></div><div class="fancy-bg" id="fancy-bg-se"></div><div class="fancy-bg" id="fancy-bg-s"></div><div class="fancy-bg" id="fancy-bg-sw"></div><div class="fancy-bg" id="fancy-bg-w"></div><div class="fancy-bg" id="fancy-bg-nw"></div><div id="fancybox-inner"></div><a id="fancybox-close"></a><a href="javascript:;" id="fancybox-left"><span class="fancy-ico" id="fancybox-left-ico"></span></a><a href="javascript:;" id="fancybox-right"><span class="fancy-ico" id="fancybox-right-ico"></span></a></div></div></body></html>
