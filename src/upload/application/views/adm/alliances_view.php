<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{al_title}</h1>
    </div>
    <p class="mb-4">{al_sub_title}</p>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{al_search}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <form class="form-search" action="" method="get">
                            <input type="hidden" name="page" value="alliances">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="alliance"
                                        class="form-control bg-light border-0 small search-query"
                                        placeholder="{al_alliance_placeholder}" value="{alliance}"
                                        aria-label="{al_alliance_placeholder}" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" aria-label="{al_search}">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                        <button class="btn btn-primary{status_box}" href="#">
                                            <i class="icon-wrench icon-white"></i>
                                            {al_actions}
                                        </button>
                                        <button class="btn btn-primary dropdown-toggle{status_box}"
                                            data-toggle="dropdown" href="#">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=alliances&type={type}&alliance={alliance}&mode=edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                    {al_edit}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=alliances&alliance={alliance}&mode=delete"
                                                    onclick="return confirm('{al_delete_confirm}')">
                                                    <i class="fas fa-trash-alt"></i>
                                                    {al_delete}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="text-center">
                            <{tag} class="btn btn-info btn-icon-split{status}"
                                href="admin.php?page=alliances&type=info&alliance={alliance}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                <span class="text">{al_general_info}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split{status}"
                                href="admin.php?page=alliances&type=ranks&alliance={alliance}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-sitemap"></i>
                                </span>
                                <span class="text">{al_ranks}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split{status}"
                                href="admin.php?page=alliances&type=members&alliance={alliance}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-users"></i>
                                </span>
                                <span class="text">{al_members}</span>
                            </{tag}>
                        </div>
                    </div>
                </div>
            </div>

            {content}
        </div>
    </div>
</div>