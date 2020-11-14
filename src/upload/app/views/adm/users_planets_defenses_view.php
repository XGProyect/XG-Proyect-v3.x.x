<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseDefenses" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapseDefenses">
        <h6 class="m-0 font-weight-bold text-primary">{planets}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseDefenses" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_info" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        {defenses_list}
                        <tr>
                            <td>{defense}</td>
                            <td><input type="number" class="form-control" name="{field}" value="{amount}"></td>
                        </tr>
                        {/defenses_list}
                    </table>
                    <div class="text-center">
                        <input type="hidden" name="send_data" value="1">
                        <button type="submit" class="btn btn-primary btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-save"></i>
                            </span>
                            <span class="text">{us_send_data}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>