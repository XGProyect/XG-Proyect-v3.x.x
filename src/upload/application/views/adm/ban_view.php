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
                    <h6 class="m-0 font-weight-bold text-primary">{np_general}</h6>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseGeneral" style="">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless" width="100%" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <form action="" method="GET" name="users">
                                                <input type="hidden" name="page" value="ban">
                                                <input type="hidden" name="mode" value="ban">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="border:0px;">{bn_users_list}
                                                            ({bn_total_users}{users_amount})</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0px;">
                                                            <div align="center">
                                                                <select name="ban_name" style="width:100%;" size="20">
                                                                    {users_list}
                                                                </select>
                                                                <script type="text/javascript">
                                                                                < !--
                                                                            var UserList = new filterlist(document.users.ban_name);
                                                                                //-->
                                                                </script>
                                                                <br />
                                                                <span class="small_font">
                                                                    <a href="javascript:UserList.set('^A')"
                                                                        title="{bn_select_title} A">A</a>
                                                                    <a href="javascript:UserList.set('^B')"
                                                                        title="{bn_select_title} B">B</a>
                                                                    <a href="javascript:UserList.set('^C')"
                                                                        title="{bn_select_title} C">C</a>
                                                                    <a href="javascript:UserList.set('^D')"
                                                                        title="{bn_select_title} D">D</a>
                                                                    <a href="javascript:UserList.set('^E')"
                                                                        title="{bn_select_title} E">E</a>
                                                                    <a href="javascript:UserList.set('^F')"
                                                                        title="{bn_select_title} F">F</a>
                                                                    <a href="javascript:UserList.set('^G')"
                                                                        title="{bn_select_title} G">G</a>
                                                                    <a href="javascript:UserList.set('^H')"
                                                                        title="{bn_select_title} H">H</a>
                                                                    <a href="javascript:UserList.set('^I')"
                                                                        title="{bn_select_title} I">I</a>
                                                                    <a href="javascript:UserList.set('^J')"
                                                                        title="{bn_select_title} J">J</a>
                                                                    <a href="javascript:UserList.set('^K')"
                                                                        title="{bn_select_title} K">K</a>
                                                                    <a href="javascript:UserList.set('^L')"
                                                                        title="{bn_select_title} L">L</a>
                                                                    <a href="javascript:UserList.set('^M')"
                                                                        title="{bn_select_title} M">M</a>
                                                                    <a href="javascript:UserList.set('^N')"
                                                                        title="{bn_select_title} N">N</a>
                                                                    <a href="javascript:UserList.set('^O')"
                                                                        title="{bn_select_title} O">O</a>
                                                                    <a href="javascript:UserList.set('^P')"
                                                                        title="{bn_select_title} P">P</a>
                                                                    <a href="javascript:UserList.set('^Q')"
                                                                        title="{bn_select_title} Q">Q</a>
                                                                    <a href="javascript:UserList.set('^R')"
                                                                        title="{bn_select_title} R">R</a>
                                                                    <a href="javascript:UserList.set('^S')"
                                                                        title="{bn_select_title} S">S</a>
                                                                    <a href="javascript:UserList.set('^T')"
                                                                        title="{bn_select_title} T">T</a>
                                                                    <a href="javascript:UserList.set('^U')"
                                                                        title="{bn_select_title} U">U</a>
                                                                    <a href="javascript:UserList.set('^V')"
                                                                        title="{bn_select_title} V">V</a>
                                                                    <a href="javascript:UserList.set('^W')"
                                                                        title="{bn_select_title} W">W</a>
                                                                    <a href="javascript:UserList.set('^X')"
                                                                        title="{bn_select_title} X">X</a>
                                                                    <a href="javascript:UserList.set('^Y')"
                                                                        title="{bn_select_title} Y">Y</a>
                                                                    <a href="javascript:UserList.set('^Z')"
                                                                        title="{bn_select_title} Z">Z</a>
                                                                </span>
                                                                <br />
                                                                <span class="small_font">
                                                                    {bn_sort}:
                                                                    <a href="admin.php?page=ban">{bn_sort_by_user}</a>
                                                                    <a
                                                                        href="admin.php?page=ban&order=id">{bn_sort_by_id}</a>
                                                                    <a
                                                                        href="admin.php?page=ban&view=user_banned">{bn_sort_suspended}</a>
                                                                </span>
                                                                <br /><br />
                                                                <br />
                                                                <input type="text" name="regexp"
                                                                    onKeyUp="UserList.set(this.value)">
                                                                <br />
                                                                <input type="button"
                                                                    onClick="UserList.set(this.form.regexp.value)"
                                                                    value="{bn_button_filter}" class="btn btn-primary">
                                                                <input type="button"
                                                                    onClick="UserList.reset();this.form.regexp.value = ''"
                                                                    value="{bn_button_remove}" class="btn btn-primary">
                                                                <br /><br />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0px;">
                                                            <div align="center">
                                                                <input type="submit" value="{bn_button_ban}"
                                                                    name="banuser" class="btn btn-primary">
                                                                <input type="button"
                                                                    onClick="UserList.reset();this.form.regexp.value = ''"
                                                                    value="{bn_button_reset}" class="btn btn-primary">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="" method="POST" name="userban">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="border:0px;">{bn_banned_list}
                                                            ({bn_total_users}{banned_amount})</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0px;">
                                                            <div align="center">
                                                                <select name="unban_name" style="width:100%;" size="20">
                                                                    {banned_list}
                                                                </select>
                                                                <script type="text/javascript">
                                                                                < !--
                                                                            var UsersBan = new filterlist(document.userban.unban_name);
                                                                                //-->
                                                                </script>
                                                                <br />
                                                                <span class="small_font">
                                                                    <a href="javascript:UsersBan.set('^A')"
                                                                        title="{bn_select_title} A">A</a>
                                                                    <a href="javascript:UsersBan.set('^B')"
                                                                        title="{bn_select_title} B">B</a>
                                                                    <a href="javascript:UsersBan.set('^C')"
                                                                        title="{bn_select_title} C">C</a>
                                                                    <a href="javascript:UsersBan.set('^D')"
                                                                        title="{bn_select_title} D">D</a>
                                                                    <a href="javascript:UsersBan.set('^E')"
                                                                        title="{bn_select_title} E">E</a>
                                                                    <a href="javascript:UsersBan.set('^F')"
                                                                        title="{bn_select_title} F">F</a>
                                                                    <a href="javascript:UsersBan.set('^G')"
                                                                        title="{bn_select_title} G">G</a>
                                                                    <a href="javascript:UsersBan.set('^H')"
                                                                        title="{bn_select_title} H">H</a>
                                                                    <a href="javascript:UsersBan.set('^I')"
                                                                        title="{bn_select_title} I">I</a>
                                                                    <a href="javascript:UsersBan.set('^J')"
                                                                        title="{bn_select_title} J">J</a>
                                                                    <a href="javascript:UsersBan.set('^K')"
                                                                        title="{bn_select_title} K">K</a>
                                                                    <a href="javascript:UsersBan.set('^L')"
                                                                        title="{bn_select_title} L">L</a>
                                                                    <a href="javascript:UsersBan.set('^M')"
                                                                        title="{bn_select_title} M">M</a>
                                                                    <a href="javascript:UsersBan.set('^N')"
                                                                        title="{bn_select_title} N">N</a>
                                                                    <a href="javascript:UsersBan.set('^O')"
                                                                        title="{bn_select_title} O">O</a>
                                                                    <a href="javascript:UsersBan.set('^P')"
                                                                        title="{bn_select_title} P">P</a>
                                                                    <a href="javascript:UsersBan.set('^Q')"
                                                                        title="{bn_select_title} Q">Q</a>
                                                                    <a href="javascript:UsersBan.set('^R')"
                                                                        title="{bn_select_title} R">R</a>
                                                                    <a href="javascript:UsersBan.set('^S')"
                                                                        title="{bn_select_title} S">S</a>
                                                                    <a href="javascript:UsersBan.set('^T')"
                                                                        title="{bn_select_title} T">T</a>
                                                                    <a href="javascript:UsersBan.set('^U')"
                                                                        title="{bn_select_title} U">U</a>
                                                                    <a href="javascript:UsersBan.set('^V')"
                                                                        title="{bn_select_title} V">V</a>
                                                                    <a href="javascript:UsersBan.set('^W')"
                                                                        title="{bn_select_title} W">W</a>
                                                                    <a href="javascript:UsersBan.set('^X')"
                                                                        title="{bn_select_title} X">X</a>
                                                                    <a href="javascript:UsersBan.set('^Y')"
                                                                        title="{bn_select_title} Y">Y</a>
                                                                    <a href="javascript:UsersBan.set('^Z')"
                                                                        title="{bn_select_title} Z">Z</a>
                                                                </span>
                                                                <br />
                                                                <span class="small_font">
                                                                    {bn_sort}:
                                                                    <a href="admin.php?page=ban">{bn_sort_by_user}</a>
                                                                    <a
                                                                        href="admin.php?page=ban&order2=id">{bn_sort_by_id}</a>
                                                                </span>
                                                                <br /><br />
                                                                <br />
                                                                <input type="text" name="regexp"
                                                                    onKeyUp="UsersBan.set(this.value)">
                                                                <br />
                                                                <input type="button"
                                                                    onClick="UsersBan.set(this.form.regexp.value)"
                                                                    value="{bn_button_filter}" class="btn btn-primary">
                                                                <input type="button"
                                                                    onClick="UsersBan.set(this.form.regexp.value)"
                                                                    value="{bn_button_remove}" class="btn btn-primary">
                                                                <br /><br />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:0px;">
                                                            <div align="center">
                                                                <input type="submit" value="{bn_button_lift_ban}"
                                                                    name="liftbanuser" class="btn btn-primary">
                                                                <input type="button"
                                                                    onClick="UsersBan.reset();this.form.regexp.value = ''"
                                                                    value="{bn_button_reset}" class="btn btn-primary">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
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
</div>