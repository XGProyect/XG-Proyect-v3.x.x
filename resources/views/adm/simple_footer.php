<!-- Bootstrap core JavaScript-->
<script src="{admin_public_path}vendor/bootstrap/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    window.addEventListener('DOMContentLoaded', event => {

        // Toggle the side navigation
        const sidebarToggle = document.body.querySelector('#sidebarToggle');
        if (sidebarToggle) {
            // Uncomment Below to persist sidebar toggle between refreshes
            // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            //     document.body.classList.toggle('sb-sidenav-toggled');
            // }
            sidebarToggle.addEventListener('click', event => {
                event.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
                localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
            });
        }

        // Poppover
        // Obtener todos los elementos con el atributo data-bs-toggle="popover"
        const popovers = document.querySelectorAll('[data-bs-toggle="popover"]');

        // Iterar sobre cada elemento y agregar el evento
        popovers.forEach((popover) => {
            // Crear un nuevo Popover para cada elemento
            const popoverInstance = new bootstrap.Popover(popover, {
                trigger: 'hover'
            });
        });

        // Check All
        // Obtener el elemento que activa/desactiva todos los checkboxes
        const checkall = document.getElementById('checkall');

        if (checkall) {
            checkall.addEventListener('click', function () {
                // Obtener la tabla que contiene los checkboxes
                const table = this.closest('table');

                // Obtener todos los checkboxes dentro de la tabla
                const checkboxes = table.querySelectorAll('.form-check-input');

                // Marcar/desmarcar todos los checkboxes según el estado del checkbox principal
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = checkall.checked;
                });
            });
        }


        // Limpiar el contenido de los elementos con la clase 'badge-counter'
        const badgeCounters = document.querySelectorAll('.badge-counter');

        if (badgeCounters) {
            badgeCounters.forEach(function (badgeCounter) {
                badgeCounter.innerHTML = '';
            });
        }

        // Ocultar los elementos con la clase 'dropdown-list'
        const dropdownLists = document.querySelectorAll('.dropdown-list');

        if (dropdownLists) {
            dropdownLists.forEach(function (dropdownList) {
                dropdownList.style.display = 'none';
            });
        }

        function compareVersion(version1, version2) {
            // Convertir las versiones en arrays de números
            const v1 = version1.toString().split('.').map(Number);
            const v2 = version2.toString().split('.').map(Number);

            // Iterar sobre las versiones
            for (let i = 0; i < Math.max(v1.length, v2.length); i++) {
                const num1 = i < v1.length ? v1[i] : 0; // Manejar versiones desiguales
                const num2 = i < v2.length ? v2[i] : 0;

                // Comparar los segmentos de versión
                if (num1 < num2) {
                    return true;
                } else if (num1 > num2) {
                    return false;
                }
            }

            // Las versiones son iguales o una es subconjunto de la otra
            return false;
        }

        fetch('//updates.xgproyect.org/latest.php')
            .then(response => response.json())
            .then(data => {
                if (typeof data === 'object' && data !== null) {
                    for (const key in data) {
                        if (compareVersion('{version}', data[key])) {
                            let test = compareVersion('{version}', data[key]);
                            console.log(test);

                            const badgeCounters = document.querySelectorAll('.badge-counter');
                            badgeCounters.forEach(badgeCounter => {
                                badgeCounter.innerHTML = '1';
                            });

                            const dropdownLists = document.querySelectorAll('.dropdown-list');
                            dropdownLists.forEach(dropdownList => {
                                dropdownList.style.display = '';
                            });
                        }
                    }
                }
            })
            .catch(error => console.error('Error fetching data:', error));
    });


</script>
<!-- <script type="text/javascript">
            $(document).ready(function () {
                // color pickers
                $('[name=color-picker]').change(function () {
                    $('[name=text]').css('color', $(this).val());
                });

                // popovers
                $('[data-bs-toggle="popover"]').popover({
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

           
        </script> -->

</body>

</html>