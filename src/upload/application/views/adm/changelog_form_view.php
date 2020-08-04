<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<div class="container-fluid">
    {alert}
    <form action="" method="POST" name="changelog">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{ch_title}</h1>
        </div>
        <p class="mb-4">{ch_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{current_action}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>
                                            <input class="form-control" type="date" name="changelog_date" min="1000-01-01" max="3000-12-31">
                                        </td>
                                        <td>
                                            <input class="form-control" type="text" name="changelog_version" value="{changelog_version}" placeholder="{ch_version}">
                                        </td>
                                        <td>
                                        <select class="form-control" name="changelog_language">
                                            <option value="">{ch_pick_language}</option>
                                            {languages}
                                            <option value="{language_id}" {selected}>{language_name}</option>
                                            {/languages}
                                        </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <textarea class="form-control" name="text" rows="10"
                                                onkeyup="javascript:cntChars('changelog', 5000);"></textarea>
                                            (<span id="cntChars">0</span> / 5000 {ch_characters})
                                        </td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-save"></i>
                                        </span>
                                        <span class="text">{ch_save}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
