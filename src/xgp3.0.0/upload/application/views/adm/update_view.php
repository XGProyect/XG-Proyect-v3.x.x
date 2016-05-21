

        <div class="span9">
	      {alert}
          <div class="hero-unit">
                <h1>{up_title}</h1>
                <br />
                <p>{up_sub_title}</p>
                <form name="update_form" method="post" action="">
                    <input type="hidden" name="send" value="send">
                    <label>{up_test_mode}</label>
                    <input type="checkbox" name="demo_mode" checked>
                    <p><em><small>{up_test_mode_notice}</small></em></p>
                    <div align="center">
                        <input type="button" class="btn btn-primary" name="next" onclick="submit();" value="{up_go}">
                    </div>
                </form>
          </div>
        </div><!--/span-->