<div id="leftmenu">
    <div id="menu">
        <p class="player-name">
            {{ $lm_players }} <strong>{!! $user_name !!}</strong>
        </p>
        <table>
            <tr>
                <td>
                    <img src="{{ $dpath }}menu/ogame-produktion.jpg" width="110" height="40" />
                </td>
            </tr>
            {!! $menu_block1 !!}
            <tr>
                <td>
                    <img src="{{ $dpath }}menu/info-help.jpg" width="110" height="19">
                </td>
            </tr>
            {!! $menu_block2 !!}
            <tr>
                <td>
                    <img src="{{ $dpath }}menu/user-menu.jpg" width="110" height="35">
                </td>
            </tr>
            {!! $menu_block3 !!}
            {!! $admin_link !!}
            <tr>
                <td>
                    <img src="{{ $dpath }}menu/info-help.jpg" width="110" height="19">
                </td>
            </tr>
            <tr>
                <td>
                    <div style="text-align:center">
                        {{ $servername }} ({!! $changelog !!})
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div style="text-align:center">
                        <span style="color:#FFFFFF">
                            <a href="#"
                                title="Powered by XG Proyect {{ $version }} &copy; 2008 - {{ $year }} GNU General Public License">&copy;
                                2008 - {{ $year }}</a>
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="menu-mobile">
        <button id="menu-toggle" aria-controls="menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div id="mobile-menu">
            <ul>
                <li class="menu-title">{{ $lm_players }} {!! $user_name !!}</li>
                <li class="menu-title">{{ $lm_production }}</li>
                {!! $menu_mobile1 !!}
                <li class="menu-title">{{ $lm_info }}</li>
                {!! $menu_mobile2 !!}
                <li class="menu-title">{{ $lm_user_menu }}</li>
                {!! $menu_mobile3 !!}
                <li>{!! $admin_link !!}</li>
                <li class="menu-title">
                    <a href="#"
                        title="Powered by XG Proyect {{ $version }} &copy; 2008 - {{ $year }} GNU General Public License">&copy;
                        2008 - {{ $year }}</a>
                </li>
            </ul>
        </div>
    </div>
</div>
<script language="JavaScript">
    function f(target_url, win_name) {
        var new_win = window.open(target_url, win_name,
            'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=550,height=280,top=0,left=0');
        new_win.focus();
    }

    const menuToggle = document.querySelector('#menu-toggle');
    const mobileMenu = document.querySelector('#mobile-menu ul');

    menuToggle.addEventListener('click', function() {
        const expanded = this.getAttribute('aria-expanded') === 'true' || false;
        this.setAttribute('aria-expanded', !expanded);

        if (mobileMenu.classList.contains('show')) {
            mobileMenu.classList.remove('show');
        } else {
            mobileMenu.classList.add('show');
        }
    });
</script>