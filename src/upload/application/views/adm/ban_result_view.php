<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<script type="text/javascript" src="{js_path}filterlist-min.js"></script>
<div class="container-fluid">
    {alert}
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{bn_title}</h1>
    </div>
    <p class="mb-4">{bn_sub_title}</p>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                    aria-expanded="true" aria-controls="collapseGeneral">
                    <h6 class="m-0 font-weight-bold text-primary">{bn_username}: {name}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <form action="" method="POST" name="frm_ban">
                                <input type="hidden" name="ban_name" value="{name}">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <th>
                                            {bn_reason}
                                        </th>
                                        <td colspan="2">
                                            <textarea class="form-control" name="text" rows="5"
                                                onkeyup="javascript:cntChars('frm_ban', 50);">{reason}</textarea>
                                            (<span id="cntChars">0</span> / 50 {bn_characters})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colspan="2">{changedate}</th>
                                    </tr>
                                    <tr>
                                        <th>{bn_time_days}</th>
                                        <td><input name="days" class="form-control" type="text" value="0"></td>
                                    </tr>
                                    <tr>
                                        <th>{bn_time_hours}</th>
                                        <td><input name="hour" class="form-control" type="text" value="0"></td>
                                    </tr>
                                    <tr>
                                        <th>{bn_vacation_mode}</th>
                                        <td>
                                            <input name="vacat" class="form-control-check" type="checkbox" {vacation} />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <div align="center">
                                                <input type="submit" value="{bn_ban_user}" name="bannow"
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
        </div>
    </div>
</div>