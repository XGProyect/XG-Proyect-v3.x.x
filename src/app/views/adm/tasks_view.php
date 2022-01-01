<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{ta_title}</h1>
    </div>
    <p class="mb-4">{ta_sub_title}</p>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{ta_general}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{ta_task}</th>
                                        <th>{ta_next_run}</th>
                                        <th>{ta_last_run}</th>
                                        <th>{ta_actions}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tasks_list}
                                    <tr>
                                        <td>
                                            <span>
                                                {name}
                                            </span>
                                        </td>
                                        <td>
                                            {next_run}
                                        </td>
                                        <td>
                                            {last_run}
                                        </td>
                                        <td>
                                            {actions}
                                        </td>
                                    </tr>
                                    {/tasks_list}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
        </div>
    </div>
</div>
