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
                    <a href="game.php?page=overview" target="_blank">{tn_game}</a> | <a href="admin.php?page=logout" onclick="return confirm('{tn_exit_confirm}');" target="_top">{tn_logout}</a>
                </p>
                <ul class="nav">
                    <li>
                        <a>Admin Control Panel (XGProyect {version})</a>
                    </li>
                    <li id="old_version_alert">
                        <a>{tn_last_version}</a>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        function compareversion(version1, version2) {
            var result = false;

            if (typeof version1 !== 'object') {
                version1 = version1.toString().split('.');
            }
            if (typeof version2 !== 'object') {
                version2 = version2.toString().split('.');
            }

            for (var i = 0; i < (Math.max(version1.length, version2.length)); i++) {

                if (version1[i] == undefined) {
                    version1[i] = 0;
                }
                if (version2[i] == undefined) {
                    version2[i] = 0;
                }

                if (Number(version1[i]) < Number(version2[i])) {
                    result = true;
                    break;
                }
                if (version1[i] != version2[i]) {
                    break;
                }
            }
            return(result);
        }

        $.getJSON('http://www.xgproyect.org/current.php', function (data) {
            $.each(data, function (index, element) {
                if (compareversion('{version}', element)) {
                    $('#old_version_alert').html('<a href="http://www.xgproyect.org/downloads/" target="_blank" style="display:inline-block;padding-right:0px;">{tn_last_version}' + element + ' {tn_update}</a> <i class="icon-download icon-white">');
                } else {
                    $('#old_version_alert').html('<a>{tn_last_version}' + element + '</a>');
                }
            });
        });
    });
</script>