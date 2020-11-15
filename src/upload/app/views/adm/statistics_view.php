<div class="container-fluid">
    {alert}
    <form method="post" action="">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{cs_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{cs_save_changes}</span>
            </button>
        </div>
        <p class="mb-4">{cs_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{cs_general}</h6>
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
                                                    {cs_point_per_resources_used}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="stat_points"
                                                    id="stat_points" value="{stat_points}" min="1">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {cs_time_between_updates}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="stat_update_time"
                                                    id="stat_update_time" value="{stat_update_time}" min="1">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {cs_access_lvl}
                                                </span>
                                            </td>
                                            <td>
                                                <select class="form-control" name="stat_admin_level"
                                                    id="stat_admin_level">
                                                    {user_levels}
                                                    <option value="{id}" {sel}>{name}</option>
                                                    {/user_levels}
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
