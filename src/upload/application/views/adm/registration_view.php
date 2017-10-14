

<div class="span9">
    {alert}
    <div class="hero-unit">
        <h1>{ur_settings}</h1>
        <br />
        <form action="" method="POST">
            <input type="hidden" name="opt_save" value="1">
            <label>
                <ul class="popups">
                    <li class="span3">
                        <div class="hover-group popup">
                            <div class="image-wrapper">
                                <strong>{ur_open_close}</strong>
                            </div>
                        </div>
                    </li>
                </ul>
            </label>
            <input name="reg_closed"{reg_closed} type="checkbox" />
            <label>
                <ul class="popups">
                    <li class="span3">
                        <div class="hover-group popup">
                            <div class="image-wrapper">
                                <strong>{ur_welcome_message}</strong>
                            </div>
                        </div>
                    </li>
                </ul>
            </label>
            <input name="reg_welcome_message"{reg_welcome_message} type="checkbox" />
            <label>
                <ul class="popups">
                    <li class="span3">
                        <div class="hover-group popup">
                            <div class="image-wrapper">
                                <strong>{ur_welcome_email}</strong>
                            </div>
                        </div>
                    </li>
                </ul>
            </label>
            <input name="reg_welcome_email"{reg_welcome_email} type="checkbox" />

            <div align="center">
                <input value="{ur_save_parameters}" type="submit" class="btn btn-primary">
            </div>

        </form>
    </div>
</div><!--/span-->