<script src="{js_path}cntchar-min.js" type="text/javascript"></script>
<div class="container-fluid">
    {alert}
    <form action="" method="POST" name="announcement">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{an_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-envelope"></i>
                </span>
                <span class="text">{an_send_message}</span>
            </button>
        </div>
        <p class="mb-4">{an_sub_title}</p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{an_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <input class="form-control" name="subject" maxlength="100"
                                                    value="{an_none}" type="text" placeholder="{an_subject}">
                                            </td>
                                            <td width="20%">
                                                <!--<input class="form-control" name="color-picker" type="color">-->
                                                <select class="form-control" name="color-picker">
                                                    <option disabled selected>{al_color}</option>
                                                    {colors}
                                                    <option value="{color}">{color}</option>
                                                    {/colors}
                                                </select>
                                            </td>
                                            <td width="35%">
                                                <table class="table table-borderless" width="100%" cellspacing="0">
                                                    <tr>
                                                        <td>
                                                            {an_send_as}
                                                        </td>
                                                        <td>
                                                            <input class="form-check-input" type="checkbox"
                                                                name="message" checked> {an_send_as_message}
                                                        </td>
                                                        <td>
                                                            <input class="form-check-input" type="checkbox" name="mail">
                                                            {an_send_as_email}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <i class="fas fa-question-circle" data-toggle="popover"
                                                    data-trigger="hover" data-content="{an_info}" data-html="true"></i>
                                                <textarea class="form-control" name="text" rows="10"
                                                    onkeyup="javascript:cntChars('announcement', 5000);"></textarea>
                                                (<span id="cntChars">0</span> / 5000 {an_characters})
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
