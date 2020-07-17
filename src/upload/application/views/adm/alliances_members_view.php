<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseMembers" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapseMembers">
        <h6 class="m-0 font-weight-bold text-primary">{al_alliance_members}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseMembers" style="">
        <div class="card-body">
            <div class="table-responsive">
                {alert_info}
                <form name="save_ranks" method="post" action="">
                    <table class="table table-borderless" width="100%" cellspacing="0">
                        <tr>
                            <td class="align_center">
                                <input type="checkbox" class="form-check-input" name="checkall" id="checkall">
                            </td>
                            <th>{al_alliance_username}</th>
                            <th>{al_alliance_pending_request}</th>
                            <th>{al_alliance_request_text}</th>
                            <th>{al_inscription_date}</th>
                            <th>{al_alliance_member_rank}</th>
                        </tr>
                        {members_table}
                        <tr>
                            <td colspan="11">
                                <div align="center">
                                    <input type="submit" name="delete_members" value="{al_delete_members}"
                                        class="btn btn-primary">
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>