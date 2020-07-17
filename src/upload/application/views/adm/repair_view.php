<div class="container-fluid">
    {alert}
    <form action="" method="POST">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{db_opt_db}</h1>
        </div>
        <p class="mb-4"></p>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{np_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        {head}
                                    </tr>
                                    {tables}
                                    {results}
                                    <tr>
                                        <td colspan="5">
                                            <div align="center" style="display: {display}">
                                                <input type="radio" name="Optimize" value="yes" checked="on"> {db_yes}
                                                <input type="radio" name="Optimize" value="no"> {db_no}
                                                <strong>{db_optimize}</strong><br>
                                                <input type="radio" name="Repair" value="yes" checked="on"> {db_yes}
                                                <input type="radio" name="Repair" value="no"> {db_no}
                                                <strong>{db_repair}</strong><br><br>
                                                <input type="submit" class="btn btn-primary">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>