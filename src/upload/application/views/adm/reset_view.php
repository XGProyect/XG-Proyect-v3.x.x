<div class="container-fluid">
    {alert}
    <form name="frm_reset" action="" method="post">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{re_reset_all}</h1>
        </div>
        <p class="mb-4"></p>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{re_general}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{re_reset_moons}</td>
                                        <td><input type="checkbox" class="form-input-check" name="moons"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_notes}</td>
                                        <td><input type="checkbox" class="form-input-check" name="notes"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_rw}</td>
                                        <td><input type="checkbox" class="form-input-check" name="rw"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_buddies}</td>
                                        <td><input type="checkbox" class="form-input-check" name="friends"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_allys}</td>
                                        <td><input type="checkbox" class="form-input-check" name="alliances"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_fleets}</td>
                                        <td><input type="checkbox" class="form-input-check" name="fleets"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_banned}</td>
                                        <td><input type="checkbox" class="form-input-check" name="banneds"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_messages}</td>
                                        <td><input type="checkbox" class="form-input-check" name="messages"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_statpoints}</td>
                                        <td><input type="checkbox" class="form-input-check" name="statpoints"></td>
                                    </tr>
                                    <tr class="bg-danger">
                                        <td><span class="text-gray-900"><strong>{re_reset_all}</strong></span></td>
                                        <td><input type="checkbox" class="form-input-check" name="resetall"></td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split"
                                        onClick="return confirm('{re_reset_universe_confirmation}');">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-undo-alt"></i>
                                        </span>
                                        <span class="text">{re_reset_go}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseBuildings" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseBuildings">
                        <h6 class="m-0 font-weight-bold text-primary">{re_buldings}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseBuildings" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{re_buildings_pl}</td>
                                        <td><input type="checkbox" class="form-input-check" name="edif_p"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_buildings_lu}</td>
                                        <td><input type="checkbox" class="form-input-check" name="edif_l"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_buldings}</td>
                                        <td><input type="checkbox" class="form-input-check" name="edif"></td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split"
                                        onClick="return confirm('{re_reset_universe_confirmation}');">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-undo-alt"></i>
                                        </span>
                                        <span class="text">{re_reset_go}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseResearch" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseResearch">
                        <h6 class="m-0 font-weight-bold text-primary">{re_inve_ofis}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseResearch" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{re_ofici}</td>
                                        <td><input type="checkbox" class="form-input-check" name="ofis"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_investigations}</td>
                                        <td><input type="checkbox" class="form-input-check" name="inves"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_invest}</td>
                                        <td><input type="checkbox" class="form-input-check" name="inves_c"></td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split"
                                        onClick="return confirm('{re_reset_universe_confirmation}');">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-undo-alt"></i>
                                        </span>
                                        <span class="text">{re_reset_go}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{re_defenses_and_ships}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{re_defenses}</td>
                                        <td><input type="checkbox" class="form-input-check" name="defenses"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_ships}</td>
                                        <td><input type="checkbox" class="form-input-check" name="ships"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_reset_hangar}</td>
                                        <td><input type="checkbox" class="form-input-check" name="h_d"></td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split"
                                        onClick="return confirm('{re_reset_universe_confirmation}');">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-undo-alt"></i>
                                        </span>
                                        <span class="text">{re_reset_go}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mb-4">
                    <!-- Card Header - Accordion -->
                    <a href="#collapseGeneral" class="d-block card-header py-3" data-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="collapseGeneral">
                        <h6 class="m-0 font-weight-bold text-primary">{re_resources}</h6>
                    </a>
                    <!-- Card Content - Collapse -->
                    <div class="collapse show" id="collapseGeneral" style="">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless" width="100%" cellspacing="0">
                                    <tr>
                                        <td>{re_resources_dark}</td>
                                        <td><input type="checkbox" class="form-input-check" name="dark"></td>
                                    </tr>
                                    <tr>
                                        <td>{re_resources_met_cry}</td>
                                        <td><input type="checkbox" class="form-input-check" name="resources"></td>
                                    </tr>
                                </table>
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary btn-icon-split"
                                        onClick="return confirm('{re_reset_universe_confirmation}');">
                                        <span class="icon text-white-50">
                                            <i class="fas fa-undo-alt"></i>
                                        </span>
                                        <span class="text">{re_reset_go}</span>
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