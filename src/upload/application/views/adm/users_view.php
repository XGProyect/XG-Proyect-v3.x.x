<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{us_title}</h1>
    </div>
    <p class="mb-4">{us_sub_title}</p>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{us_search}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <form class="form-search" action="" method="get">
                            <input type="hidden" name="page" value="users">
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="text" name="user"
                                        class="form-control bg-light border-0 small search-query"
                                        placeholder="{us_username_placeholder}" value="{user}"
                                        aria-label="{us_username_placeholder}" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit" aria-label="{us_search}">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                        <button class="btn btn-primary{status_box}" href="#">
                                            <i class="icon-user icon-white"></i>
                                            {user_rank}
                                        </button>
                                        <button class="btn btn-primary dropdown-toggle{status_box}"
                                            data-toggle="dropdown" href="#">
                                            <span class="caret"></span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=users&type={type}&user={user}&mode=edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                    {us_edit}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=users&user={user}&mode=delete"
                                                    onclick="return confirm('{us_delete_confirm}')">
                                                    <i class="fas fa-trash-alt"></i> {us_delete}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=ban&mode=ban&ban_name={user}&regexp=">
                                                    <i class="fas fa-user-slash"></i>
                                                    {us_ban}
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="admin.php?page=permissions">
                                                    <i class="fas fa-user-tag"></i>
                                                    {us_change_permissions}
                                                </a>
                                            </li>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="text-center">
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=info&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span class="text">{us_general_info}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=settings&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-user-cog"></i>
                                </span>
                                <span class="text">{us_settings}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=research&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-flask"></i>
                                </span>
                                <span class="text">{us_research}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=premium&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-gem"></i>
                                </span>
                                <span class="text">{us_premium}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=planets&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-globe-americas"></i>
                                </span>
                                <span class="text">{us_planets}</span>
                            </{tag}>
                            <{tag} class="btn btn-info btn-icon-split {status}"
                                href="admin.php?page=users&type=moons&user={user}">
                                <span class="icon text-white-50">
                                    <i class="fas fa-moon"></i>
                                </span>
                                <span class="text">{us_moons}</span>
                            </{tag}>
                        </div>
                    </div>
                </div>
            </div>

            {content}
        </div>
    </div>
</div>
