$(document).ready(function(){
	var activeTab = {};

	//initial exists
	jQuery.fn.exists = function(){return jQuery(this).length>0;};

	//TABS
	//inital JQUERY TOOLS Tabs
	$('ul#tabs').tabs('#tabContent > div#ajaxContent', {effect: 'ajax'});
	//set Tab on reload
	activeTab = $('ul#tabs a.current').attr('id');
	setTab();
	//set Tab on click
	$('ul#tabs a').click(function(){
		$.validationEngine.closePrompt('.formError',true)
		$('#passwordLost, #resendLink').hide();
		$('#ajaxContent').fadeIn();
		activeTab = $(this).attr('id');
		setTab();
	});

	//Change Tab-Position
	function setTab () {
		switch (activeTab) {
			case 'tab1':
				$('#menu').css('background-position','15px -33px');
				break;
			case 'tab2':
				$('#menu').css('background-position','15px -66px');
				break;
			case 'tab3':
				$('#menu').css('background-position','15px -99px');
				break;
			case 'tab4':
				$('#menu').css('background-position','15px -132px');
				break;
			default:
			break;
		}
	}
    function loginFadeIn() {
        $("#loginBtn").addClass('open').text(JSLoca[1]);
        $('#login').fadeIn('fast', function(){$("#usernameLogin").focus();});
    }

    function loginfadeOut() {
        $("#loginBtn").removeClass('open').text(JSLoca[0]);
		$('#login').fadeOut();
    }

	//LOGIN BUTTON MASK-SLIDER
	$("#loginBtn").click(function () {
		$.validationEngine.closePrompt('.formError',true);
		if ($(this).hasClass('open')) {
			loginfadeOut();
		} else {
			loginFadeIn();
		}
    });

    /*if($.cookie('OG_lastServer') != null) {
        loginFadeIn();
    }*/


	/*$('#parallax img').parallax({
        mouseport: jQuery('body'),
		xparallax: true,
		yparallax: false
  	});*/

	//PASSWORDLOST generate
	$('#pwLost').click(function(){
        //$(this).attr("href","http://" + $("#serverLogin").val() + "/game/reg/mail.php");
        $(this).attr("href",$(location).attr('href') + "index.php?page=mail");
	});
	//RESENDLINK generate
	$('#resendAct').click(function(){
		$.validationEngine.closePrompt('.formError',true);
		$('#tabs .current').removeClass('current');
		$('#menu').css('background-position','15px 0');
		$('#ajaxContent, #passwordLost, #login').hide();
		$('#resendLink').fadeIn();
		$('#loginBtn').removeClass('open').text('Login');
	});

	//LANGUAGE BOX-FADER
	$("#trigger").toggle(
		function () {
			$('#selected, #language').fadeIn();
		},
		function () {
			$('#selected, #language').fadeOut();
    });
	//LANGUAGE SELECT-CHANGE TXT
	$('#langHead a').click(function(){
		var landSelect = $(this).attr('title');
		$('#selected, #displayLang').text(landSelect);
	});

	//VALDATION

	//INITIAL FORM VALIDATION
	$("#loginForm").validationEngine({
	    validationEventTriggers:"blur",
        promptPosition: "centerRight",
		inlineValidation: true
	});
	$("#pwLostForm").validationEngine();
	$("#resendLinkForm").validationEngine();
	$("#subscribeForm").validationEngine({
		validationEventTriggers:"keyup blur",
        promptPosition: "centerRight",
		inlineValidation: true
	});
	//PROOF VALIDATION ON SUBMIT
	$('#regSubmit').click(function(){
		var success = $('#subscribeForm').validationEngine({returnIsValid:true});
		if (success == true ) {		}
	});


	/*$('input[type=password], input[type=text], textarea').each(function() {
		var default_value = this.value;
		$(this).focus(function() {
			if(this.value == default_value) {
				this.value = '';
			}
		});
		$(this).blur(function() {
			if(this.value == '') {
				this.value = default_value;
			}
		});
	});*/

	// agb in overlay
	 $('#submitWrap a.overlay').fancybox({
		'onStart':	function() {
	    				$.validationEngine.closePrompt('.formError',true);
					},
		'hideOnContentClick': true,
		width: '520px',
		height: '500px'
	});

    // agb auch außerhalb des submitWraps
    $('a.overlay').fancybox({
		'onStart':	function() {
	    				$.validationEngine.closePrompt('.formError',true);
					},
		'hideOnContentClick': true
	});



    // Trailer-Klick-Error-Ausblendung.
    $('#trailer').click(function(){
        $.validationEngine.closePrompt('.formError',true);
    });


   // check pws for valid characters
	function hasValidChar(strPass) {
		// check if str contains special characters
		var only_this = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.$!;:-_#';
		for (var i=0;i<strPass.length;i++) {
			//if (only_this.indexOf(strPass.charAt(i))<0) return false;
		}
		return true;
	}

	// check for secure pwd
	function checkPass(strPass,minLen) {
		var sec = 0;
		var check = 100;
		var steps = 7;
		var checkByStep = check / steps;

		var strToCheck = '0123456789'; // check if numbers
		if (contains(strPass, strToCheck)) { sec++ }
		strToCheck = 'abcdefghijklmnopqrstuvwxyz'; // check if lowercase letters
		if (contains(strPass, strToCheck)) { sec++ }
		strToCheck = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'; // check if uppercase letters
		if (contains(strPass, strToCheck)) { sec++ }
		strToCheck = '.$!;:-_#'; // check if uppercase letters
		if (contains(strPass, strToCheck)) { sec++ }

		// check if at least one uppercase AND one lowercase AND one number
		if (strPass.length < minLen) sec = 0;
		if (strPass.length >= minLen) sec++;
		if (strPass.length >= (minLen+2)) sec++;
		if (strPass.length >= (minLen+4)) sec++;

		var nCount = sec * checkByStep;

		if (nCount > check) nCount = check;

		return Math.ceil(nCount);
	}
	// check if string in pattern
	function contains(strText, strPattern) {
		for (i = 0; i < strText.length; i++) {
			if (strPattern.indexOf(strText.charAt(i)) > -1) return true;
		}
		return false;
	}

	//CHECKBOX bei "checked" gr�n machen
	/*$('#submitWrap label *').not('a').click(function(){
		var checkbox = $('#agb');
		checkStatus = checkbox.checked;
		checkStatus? $('#agb').checked = false :  $('#agb').checked = true;
	});*/

 	var checkSel = function () {
    	$('#submitWrap label').toggleClass('green');
	}
	if ( $("#agb:checked").length) checkSel();
	$("#agb").click(checkSel);

	var activeCookie = function () {
    	$('.cookie-txt').toggleClass('checked');
	}
	if ( $("#cookie:checked").length) activeCookie();
	$("#cookie").click(activeCookie);

	// check password and show errors and secure bar
	var ratio = '';
	var pwdMinLen = 8;
	$('#password').keyup(function(){
		$('#validChar').text('');
//		$.validationEngine.buildPrompt($(this),$progressbar,'pass');
		var strPass = $(this).val();
		if (!hasValidChar(strPass)) {
			$('#validChar').text($('#txtInvalidChar').text());
			return;
		}
		if (strPass.length >= 8) {
			$('#securePwd .valid-icon').removeClass('invalid');
		}
		else{ $('#securePwd .valid-icon').addClass('invalid');}
		ratio = checkPass($(this).val(),pwdMinLen);
		if (ratio) {
			$('#securePwdBar').css({width: ratio+'%'});
			if (ratio > 69) {
				$('#securePwd #securePwdBar').css('background-position', '0 -39px');
			} else if (ratio > 41) {
				$('#securePwd #securePwdBar').css('background-position', '0 -26px');
			} else if (ratio < 41) {
				$('#securePwd #securePwdBar').css('background-position', '0px 0px !important');
			} else {$('#securePwd #securePwdBar').css('background-position', '0px 0px');}
		} else {
			$('#securePwdBar').css({width: 0});
			$('#securePwd .valid-icon').addClass('invalid');
		}
		if ( (ratio > 49) && (strPass.length < 5)) {
			$('#securePwdBar').css({'width':'48px', 'background-position':'0px 0px'});
		}
	});
});