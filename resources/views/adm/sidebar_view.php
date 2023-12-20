<nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">{general}</div>

            <a class="nav-link collapsed{active_2}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseSettingsMenu" aria-expanded="false" aria-controls="collapseSettingsMenu">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-fw fa-cogs"></i>
                </div>
                {configuration}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse{active_2_show}" id="collapseSettingsMenu" aria-labelledby="headingSettings"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <div class="bg-white py-2 rounded">
                        {menu_block_2}
                    </div>
                </nav>
            </div>


            <a class="nav-link collapsed{active_3}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseInformationMenu" aria-expanded="false" aria-controls="collapseInformationMenu">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-fw fa-info-circle"></i>
                </div>
                {information}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse{active_3_show}" id="collapseInformationMenu" aria-labelledby="headingInformation"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <div class="bg-white py-2 rounded">
                        {menu_block_3}
                    </div>
                </nav>
            </div>


            <a class="nav-link collapsed{active_4}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEditionMenu"
                aria-expanded="false" aria-controls="collapseEditionMenu">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-fw fa-pen"></i>
                </div>
                {edition}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse{active_4_show}" id="collapseEditionMenu" aria-labelledby="headingEdition"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <div class="bg-white py-2 rounded">
                        {menu_block_4}
                    </div>
                </nav>
            </div>


            <a class="nav-link collapsed{active_5}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseToolsMenu"
                aria-expanded="false" aria-controls="collapseToolsMenu">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-fw fa-tools"></i>
                </div>
                {tools}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse{active_5_show}" id="collapseToolsMenu" aria-labelledby="headingTools"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <div class="bg-white py-2 rounded">
                        {menu_block_5}
                    </div>
                </nav>
            </div>

            <a class="nav-link collapsed{active_6}" href="#" data-bs-toggle="collapse"
                data-bs-target="#collapseMaintenanceMenu" aria-expanded="false" aria-controls="collapseMaintenanceMenu">
                <div class="sb-nav-link-icon">
                    <i class="fas fa-fw fa-brush"></i>
                </div>
                {maintenance}
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>

            <div class="collapse{active_6_show}" id="collapseMaintenanceMenu" aria-labelledby="headingMaintenance"
                data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <div class="bg-white py-2 rounded">
                        {menu_block_6}
                    </div>
                </nav>
            </div>

        </div>
    </div>
</nav>