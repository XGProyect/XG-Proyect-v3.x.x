<div class="container-fluid">
    {alert}
    <form action="" method="POST" name="changelog">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{ch_title}</h1>
            <a href="admin.php?page=changelog&action=add" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{ch_new_item}</span>
            </a>
        </div>
        <p class="mb-4">{ch_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{ch_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <th>{ch_date}</th>
                                        <th>{ch_version}</th>
                                        <th>{ch_language}</th>
                                        <th>{ch_actions}</th>
                                    </tr>
                                    {changelog}
                                    <tr data-toggle="collapse" data-target="#toggle{changelog_id}" aria-expanded="false"
                                        aria-controls="toggle{changelog_id}">
                                        <td>{changelog_date}</td>
                                        <td>{changelog_version}</td>
                                        <td>{changelog_language}</td>
                                        <td>
                                            <a href="admin.php?page=changelog&action=edit&changelogId={changelog_id}"
                                                class="btn btn-primary btn-circle btn-sm">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-primary btn-circle btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="admin.php?page=changelog&action=delete&changelogId={changelog_id}"
                                                class="btn btn-danger btn-circle btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4">
                                            <div class="collapse" id="toggle{changelog_id}">
                                                <div class="card shadow mb-4">
                                                    <div
                                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                        <h6 class="m-0 font-weight-bold text-primary">{ch_the_description}
                                                        </h6>
                                                        <div class="dropdown no-arrow">
                                                            <a class="dropdown-toggle" href="#" role="button"
                                                                id="dropdownMenuLink" data-toggle="dropdown"
                                                                aria-haspopup="true" aria-expanded="false">
                                                                <i
                                                                    class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                                                aria-labelledby="dropdownMenuLink"
                                                                x-placement="bottom-end"
                                                                style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(17px, 19px, 0px);">
                                                                <div class="dropdown-header">{ch_actions}</div>
                                                                <a class="dropdown-item"
                                                                    href="admin.php?page=changelog&action=edit&changelogId={changelog_id}">{ch_edit_this}</a>
                                                                <a class="dropdown-item"
                                                                    href="admin.php?page=changelog&action=delete&changelogId={changelog_id}">{ch_delete_this}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Card Body -->
                                                    <div class="card-body justify-content-center mx-auto">
                                                        {changelog_description}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    {/changelog}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
