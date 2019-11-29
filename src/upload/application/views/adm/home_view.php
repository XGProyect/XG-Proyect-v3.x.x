<div class="container-fluid">

    {alert}
    <div class="alert {second_style}">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{error_type}</strong>
        <br>
        {error_message}
    </div>
    {/alert}

    <!-- Page Heading -->
    <h1 class="h3 mb-0 text-gray-800">{hm_title}</h1>
    <p class="mb-4">{hm_sub_title}</p>

    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#collapseStatistics" class="d-block card-header py-3" data-toggle="collapse" role="button"
            aria-expanded="true" aria-controls="collapseStatistics">
            <h6 class="m-0 font-weight-bold text-primary">{hm_server_statistics}</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="collapseStatistics" style="">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <tbody>
                            <tr>
                                <td>{hm_number_users}:</td>
                                <td>{number_users}</td>
                                <td>{hm_number_alliances}:</td>
                                <td>{number_alliances}</td>
                            </tr>
                            <tr>
                                <td>{hm_number_planets}:</td>
                                <td>{number_planets}</td>
                                <td>{hm_number_moons}:</td>
                                <td>{number_moons}</td>
                            </tr>
                            <tr>
                                <td>{hm_number_fleets}:</td>
                                <td>{number_fleets}</td>
                                <td>{hm_number_reports}:</td>
                                <td>{number_reports}</td>
                            </tr>
                            <tr>
                                <td>{hm_average_user_points}:</td>
                                <td>≃{average_user_points}</td>
                                <td>{hm_average_alliance_points}:</td>
                                <td>≃{average_alliance_points}</td>
                            </tr>
                            <tr>
                                <td>{hm_database_size}:</td>
                                <td>{database_size}</td>
                                <td>{hm_database_server}</td>
                                <td>{database_server}</td>
                            </tr>
                            <tr>
                                <td>{hm_php_version}:</td>
                                <td>{php_version}</td>
                                <td>{hm_server_version}</td>
                                <td>{server_version}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header - Accordion -->
        <a href="#collapseCredits" class="d-block card-header py-3" data-toggle="collapse" role="button"
            aria-expanded="true" aria-controls="collapseCredits">
            <h6 class="m-0 font-weight-bold text-primary">{hm_credits}</h6>
        </a>
        <!-- Card Content - Collapse -->
        <div class="collapse show" id="collapseCredits" style="">
            <div class="card-body text-center">
                <p>
                    <strong>{hm_proyect_leader}</strong>
                    <br>
                    <a href="https://github.com/LucasKovacs" target="_blank">lucky</a>
                    <br>
                    <a href="https://github.com/BeReal86" target="_blank">BeReal</a>
                    <br><br>
                    <strong>{hm_extensions}</strong>
                    <br>
                    <a href="https://github.com/jstar88/opbe" target="_blank">jstar - OPBE</a>
                    <br>
                    <a href="https://codeigniter.com/" target="_blank">CodeIgniter</a>
                    <br>
                    <a href="https://startbootstrap.com/themes/sb-admin-2/" target="_blank">
                        Start Bootstrap | SB Admin 2
                    </a>
                    <br><br>
                    <strong>{hm_principal_contributors}</strong>
                    <br>
                    adri93, Alberto14, angelus_ira, Anghelito, Arali, Borboco, Calzon, cyberghoser1, cyberrichy,
                    edering, Gmir17, Green, JonaMix, jtsamper, Kloud, LordPretender, Loucouss, medel, MSW, Neko,
                    Neurus, Nickolay, Pada, pele87, PowerMaster, privatethedawn, quaua, Razican, Tarta, Think,
                    thyphoon, tomtom, Tonique, Trojan, Saint, shoghicp, slaver7, war4head, zebulonbof, zorro2666
                </p>
            </div>
        </div>
    </div>
</div>
