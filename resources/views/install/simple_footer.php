<footer class="text-center">
    <div class="container-fluid px-4 pt-3 text-center">
        <div class="small">
            <div class="text-muted">Powered by XG Proyect&reg; Version {version} Copyright &copy; {year}</div>
        </div>
    </div>
</footer>

<!-- Bootstrap core JavaScript-->
<script src="{admin_public_path}vendor/bootstrap/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    window.addEventListener('DOMContentLoaded', event => {

        document.querySelector('#navbarSideCollapse').addEventListener('click', () => {
            document.querySelector('.offcanvas-collapse').classList.toggle('open')
        })
    });


</script>


</body>

</html>