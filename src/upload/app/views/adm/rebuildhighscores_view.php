<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{sb_title}</h1>
        <button onclick="javascript:location.reload();" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-sync-alt"></i>
            </span>
            <span class="text">{sb_rebuild}</span>
        </button>
    </div>
    <p class="mb-4">{sb_sub_title}</p>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{sb_stats_updated}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span>
                                                {sb_top_memory}
                                            </span>
                                        </td>
                                        <td>
                                            {memory_p}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>
                                                {sb_start_memory}
                                            </span>
                                        </td>
                                        <td>
                                            {memory_i}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>
                                                {sb_final_memory}
                                            </span>
                                        </td>
                                        <td>
                                            {memory_e}
                                        </td>
                                    </tr>
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