<div class="card shadow mb-4">
    <!-- Card Header - Accordion -->
    <a href="#collapseMoons" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true"
        aria-controls="collapseMoons">
        <h6 class="m-0 font-weight-bold text-primary">{moons}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div class="collapse show" id="collapseMoons" style="">
        <div class="card-body">
            <table class="table table-borderless" width="100%" cellspacing="0">
                {moons_list}
                <tr>
                    <td class="text-left">
                        <div class="btn-group">
                            <img src="{image_path}{moon_image}.jpg" alt="{moon_image}.jpg" title="{moon_image}.jpg"
                                border="0" {moon_image_style}>
                            <button class="btn btn-info dropdown-toggle" data-toggle="dropdown">{moon_name}
                                {moon_status}
                                {us_user_planets_actions} <span class="caret"></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=moon&user={user}&moon={moon_id}">
                                        {us_user_moons_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=buildings&user={user}&moon={moon_id}">
                                        {us_user_buildings_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=ships&user={user}&moon={moon_id}">
                                        {us_user_ships_edit}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=defenses&user={user}&moon={moon_id}">
                                        {us_user_defenses_edit}
                                    </a>
                                </li>
                                <li>
                                    <hr>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="admin.php?page=users&type=moons&edit=delete&dltmode=soft&user={user}&moon={moon_id}">
                                        {us_user_delete_moon}
                                        {us_user_delete_pm_soft}
                                    </a>
                                </li>
                                <!--<li><a href="admin.php?page=users&type=moons&edit=delete&dltmode=physical&user={user}&moon={moon_id}">{us_user_delete_moon} {us_user_delete_pm_physical}</a></li>-->
                            </ul>
                        </div>
                    </td>
                </tr>
                {/moons_list}
            </table>
        </div>
    </div>
</div>
