<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{pr_title}</h1>
            <button type="submit" class="btn btn-primary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-save"></i>
                </span>
                <span class="text">{pr_save_changes}</span>
            </button>
        </div>
        <p class="mb-4">{pr_sub_title}</p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{pr_general}</h6>
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
                                                    {pr_pay_url}
                                                </span>
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="premium_url" class="field span12"
                                                    cols="75" rows="5" placeholder="{premium_url}">{premium_url}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_registration_dark_matter}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="registration_dark_matter" min="0"
                                                    value="{registration_dark_matter}">
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
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseOtherParameters" class="d-block card-header py-3" data-toggle="collapse"
                        role="button" aria-expanded="true" aria-controls="collapseOtherParameters">
                        <h6 class="m-0 font-weight-bold text-primary">{pr_trader}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseOtherParameters" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_trader_price}
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="number" name="merchant_price"
                                                    value="{merchant_price}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_merchant_base_min_exchange_rate}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{pr_merchant_explanation}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="merchant_base_min_exchange_rate"
                                                    value="{merchant_base_min_exchange_rate}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_merchant_base_max_exchange_rate}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{pr_merchant_explanation}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="merchant_base_max_exchange_rate"
                                                    value="{merchant_base_max_exchange_rate}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_merchant_metal_multiplier}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{pr_merchant_explanation}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text" name="merchant_metal_multiplier"
                                                    value="{merchant_metal_multiplier}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_merchant_crystal_multiplier}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{pr_merchant_explanation}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="merchant_crystal_multiplier"
                                                    value="{merchant_crystal_multiplier}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span>
                                                    {pr_merchant_deuterium_multiplier}
                                                    <i class="fas fa-question-circle" data-toggle="popover"
                                                        data-trigger="hover" data-content="{pr_merchant_explanation}"
                                                        data-html="true"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <input class="form-control" type="text"
                                                    name="merchant_deuterium_multiplier"
                                                    value="{merchant_deuterium_multiplier}">
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
