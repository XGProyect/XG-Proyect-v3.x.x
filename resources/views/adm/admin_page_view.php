<body class="sb-nav-fixed">
    {navigation}
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            {sidebar}
        </div>
        <div id="layoutSidenav_content">
            <main>
                {page_content}
            </main>
            {footer}
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModal">{ready_to_leave}</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">{ready_to_leave_instructions}</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button"
                        data-dismiss="modal">{ready_to_leave_cancel}</button>
                    <a class="btn btn-primary" href="admin.php?page=logout">{ready_to_leave_logout}</a>
                </div>
            </div>
        </div>
    </div>