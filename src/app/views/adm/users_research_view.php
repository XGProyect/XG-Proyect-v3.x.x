<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseResearch" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapseResearch">
        <h6 class="m-0 font-weight-bold text-primary">{research}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseResearch" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_info" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        {technologies_list}
                        <tr>
                            <td>{technology}</td>
                            <td><input type="number" class="form-control" name="{field}" value="{level}"></td>
                        </tr>
                        {/technologies_list}
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