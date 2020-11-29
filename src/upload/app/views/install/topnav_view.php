<script type="text/javascript">
    $(function () {
        $('#language').change(function () {
            $('#change_language').submit();
        });
    });
</script>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand">XG Proyect</a>
            <div class="nav-collapse collapse">
                <p class="navbar-text pull-right">
                <div style="float:right;height:0px;padding:0px;margin:0px">
                    <form name="change_language" id="change_language" method="post" action="">
                        <select id="language" name="language" onchange="submit()">
                            <option selected disabled>{ins_language_select}</option>
                            {language_select}
                        </select>
                    </form>
                </div>
                </p>
                <ul class="nav">
                    {menu_items}
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
