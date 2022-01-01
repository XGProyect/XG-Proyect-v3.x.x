<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapsePlanets" class="d-block card-header py-3" data-toggle="collapse" role="button"
        aria-expanded="true" aria-controls="collapsePlanets">
        <h6 class="m-0 font-weight-bold text-primary">{planets}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapsePlanets" style="">
        <div class="card-body">
            <table class="table table-borderless" width="100%" cellspacing="0">
                {planets_list}
                <tr>
                    <td class="text-left">
                        <div class="btn-group">
                            <img src="{image_path}{planet_image}.jpg" alt="{planet_image}.jpg"
                                title="{planet_image}.jpg" border="0" {planet_image_style}>
                            {moon_image}
                            <button class="btn btn-info dropdown-toggle" data-toggle="dropdown">{planet_name}
                                {planet_status}
                                [{moon_name} {moon_status}]
                                {us_user_planets_actions} <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=planets&edit=planet&user={user}&planet={planet_id}">
                                        {us_user_planets_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=planets&edit=buildings&user={user}&planet={planet_id}">
                                        {us_user_buildings_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=planets&edit=ships&user={user}&planet={planet_id}">
                                        {us_user_ships_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=planets&edit=defenses&user={user}&planet={planet_id}">
                                        {us_user_defenses_edit}
                                    </a>
                                </li>
                                <li>
                                    <hr>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=planets&edit=delete&dltmode=soft&user={user}&planet={planet_id}">
                                        {us_user_delete_planet}
                                        {us_user_delete_pm_soft}
                                    </a>
                                </li>
                                <!--<li><a href="admin.php?page=users&type=planets&edit=delete&dltmode=physical&user={user}&planet={planet_id}">{us_user_delete_planet} {us_user_delete_pm_physical}</a></li>-->
                                <li>
                                    <hr>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=maker&mode=moon&planet={planet_id}">{us_user_add_moon}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=delete&dltmode=soft&user={user}&moon={moon_id}">{us_user_delete_moon}
                                        {us_user_delete_pm_soft}
                                    </a>
                                </li>
                                <!--<li><a href="admin.php?page=users&type=moons&edit=delete&dltmode=physical&user={user}&moon={moon_id}">{us_user_delete_moon} {us_user_delete_pm_physical}</a></li>-->
                            </ul>
                        </div>
                    </td>
                </tr>
                {/planets_list}
            </table>
        </div>
    </div>
</div>
