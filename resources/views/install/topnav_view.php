<script type="text/javascript">
    $(function () {
        $('#language').change(function () {
            $('#change_language').submit();
        });
    });
</script>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark p-0" aria-label="Main navigation">
    <div class="container-fluid">

        <a class="navbar-brand" href="admin.php?page=home">
            <img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-white.png" alt="XG Proyect Logo"
                title="XG Proyect" width="150px">
        </a>

        <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                {menu_items}
            </ul>
            <form name="change_language" id="change_language" method="post" action="" class="d-flex" role="search">
                <select id="language" name="language" onchange="submit()" class="form-control form-select">
                    <option selected disabled>{ins_language_select}</option>
                    {language_select}
                </select>
            </form>
        </div>
    </div>
</nav>