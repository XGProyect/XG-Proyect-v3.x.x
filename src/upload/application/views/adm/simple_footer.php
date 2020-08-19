
    <!-- Bootstrap core JavaScript-->
    <script src="{admin_public_path}vendor/jquery/jquery.min.js"></script>
    <script src="{admin_public_path}vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{admin_public_path}vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{admin_public_path}js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="{admin_public_path}vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{admin_public_path}vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!--<script src="{admin_public_path}vendor/chart.js/Chart.min.js"></script>-->

    <!-- Page level custom scripts -->
    <!--<script src="{admin_public_path}js/demo/chart-area-demo.js"></script>-->
    <!--<script src="{admin_public_path}js/demo/chart-pie-demo.js"></script>-->

    <script type="text/javascript">
        $(document).ready(function () {
            // color pickers
            $('[name=color-picker]').change(function(){
                $('[name=text]').css('color', $(this).val());
            });

            // popovers
            $('[data-toggle="popover"]').popover({
                trigger: 'hover'
            })

            // datatables
            $('#dataTable').DataTable();

            // check all
            $('#checkall').click(function () {
                $(this).parents('table:eq(0)').find('.form-check-input').attr('checked', this.checked);
            });

            // check version
            $('.badge-counter').html('');
            $('.dropdown-list').hide();

            $.getJSON('//updates.xgproyect.org/latest.php', function (data) {
                $.each(data, function (index, element) {
                    if (compareversion('{version}', element)) {
                        $('.badge-counter').html('1');
                        $('.dropdown-list').css('display', '')
                    }
                });
            });
        });

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
                return (result);
            }
    </script>

</body>

</html>
