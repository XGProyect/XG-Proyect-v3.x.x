<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{mg_title}</h1>
    </div>
    <p class="mb-4">{mg_sub_title}</p>

    <div class="row">
        <div class="col-lg-12">
            <form name="frm_message_filter" method="POST" action="admin.php?page=messages">
                <input type="hidden" name="search" value="1">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseFilter" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseFilter">
                        <h6 class="m-0 font-weight-bold text-primary">{mg_filter_by}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse {show_search}" id="collapseFilter" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>
                                            <input class="form-control" type="text" name="message_user"
                                                placeholder="{mg_filter_user}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" name="message_subject"
                                                placeholder="{mg_filter_planet}">
                                        </td>
                                        <td>
                                            <input class="form-control" type="date" name="message_date" min="1000-01-01"
                                                max="3000-12-31">
                                        </td>
                                        <td>
                                            <select class="form-control" name="message_type">
                                                <option value="">{mg_filter_type}</option>
                                                {type_options}
                                                <option value="{value}">{name}</option>
                                                {/type_options}
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <input class="form-control" type="text" name="message_text"
                                                placeholder="{mg_filter_content}">
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="btn btn-primary btn-icon-split">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <span class="text">{mg_filter_start_search}</span>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form name="frm_message_results" method="POST" action="admin.php?page=messages">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseResults" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseResults">
                        <h6 class="m-0 font-weight-bold text-primary">{mg_search_results}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse {show_results}" id="collapseResults" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td colspan="8">
                                            <button type="submit" class="btn btn-danger btn-icon-split">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                                <span class="text">{mg_delete_selected}</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <input class="form-check-input" type="checkbox" name="checkall"
                                                id="checkall">
                                        </th>
                                        <th>{mg_sender}</th>
                                        <th>{mg_receiver}</th>
                                        <th>{mg_time}</th>
                                        <th>{mg_type}</th>
                                        <th>{mg_from}</th>
                                        <th>{mg_subject}</th>
                                        <th>{mg_actions}</th>
                                    </tr>
                                    {results}
                                    <tr data-toggle="collapse" data-target="#toggle{message_id}" aria-expanded="false"
                                        aria-controls="toggle{message_id}">
                                        <td>
                                            <input class="form-check-input" type="checkbox"
                                                name="delete_messages[{message_id}]">
                                        </td>
                                        <td>{sender}</td>
                                        <td>{receiver}</td>
                                        <td>{message_time}</td>
                                        <td>{message_type}</td>
                                        <td>{message_from}</td>
                                        <td>{message_subject}</td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-circle btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="admin.php?page=messages&action=delete&messageId={message_id}"
                                                class="btn btn-danger btn-circle btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8">
                                            <div class="collapse" id="toggle{message_id}">
                                                <div class="card shadow mb-4">
                                                    <div
                                                        class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                                        <h6 class="m-0 font-weight-bold text-primary">{mg_the_message}
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
                                                                <div class="dropdown-header">{mg_actions}</div>
                                                                <a class="dropdown-item"
                                                                    href="admin.php?page=messages&action=delete&messageId={message_id}">{mg_delete_this}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Card Body -->
                                                    <div class="card-body justify-content-center mx-auto">
                                                        {message_text}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    {/results}
                                    <tr>
                                        <td colspan="8">
                                            <button type="submit" class="btn btn-danger btn-icon-split">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-trash-alt"></i>
                                                </span>
                                                <span class="text">{mg_delete_selected}</span>
                                            </button>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
