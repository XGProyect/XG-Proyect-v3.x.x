function setUniID(id) {
    document.forms["loginForm"].uni_id.value = id;
}

function setUniUrl(url)
{
    jQuery("#getPassword").attr("href", "//"+url+"/game/reg/mail.php");
    jQuery("#pwLost").attr("href","//"+url+"/game/reg/mail.php");
    jQuery("#loginForm").attr("action", "//"+url+"/game/reg/login2.php");
}

function changeAction(type,formular)
{
    var uniUrl = document.forms[formular].uni_url.value;

    if(type == "login")
    {
        document.forms[formular].action = "//"+uniUrl+"/game/reg/login2.php";
    }
    else if(type == "getpw")
    {
        document.forms[formular].action = "//"+uniUrl+"/index.php?page=mail";
    }
    else if(type == "register")
    {
        document.forms[formular].action = "//"+uniUrl+"/index.php?page=register";

    }
}
function select_uni(code, name, cssClass) {
    $('#uni_selection').css('display', 'none');
    $('#uni_name').text(name);
    $('#uni_domain').attr('value',code);

    if (cssClass != '') {
        //$('#uni_name').css('margin-left','30px');
        $('#uni_select_box').removeClass($('#uni_select_box').attr('class'));
        $('#uni_select_box').addClass(cssClass);
    } else {
    //$('#uni_name').css('margin-left','0px');
    }
}

function hideLoginErrorBox() {
        $(".usernameLoginformError").hide();
    }

function switch_uni_selection() {
    curr_style = $('#uni_selection').css('display');

    if (curr_style == 'none') {
        $('#uni_selection').css('display','inline');
    } else {
        $('#uni_selection').css('display','none');
    }
}

function highlightRow(rowId) {
    $('#'+rowId).addClass('hoverSelectbox');
}

function unHighlightRow(rowId) {
    $('#'+rowId).removeClass('hoverSelectbox');
}

function browserAgentHasString(string){
    return navigator.userAgent.toLowerCase().indexOf(string.toLowerCase()) > -1;
}
function checkIpadApp() {
    if (browserAgentHasString('ipad') && $("#ipadapp").length > 0) {
        var $link = $("#ipadapp > a");
        if (typeof($link.attr('data-question')) !== 'undefined' && $link.attr('data-question').length > 0) {
            if (confirm($link.attr('data-question'))) {
                location.href = $link.attr('href');
            }
        }
    }
}
