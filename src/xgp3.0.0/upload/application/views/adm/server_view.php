

        <div class="span9">
	      {alert}
          <div class="hero-unit">
            <h1>{se_server_parameters}</h1>
            <br />
			<form action="" method="POST">
			<input type="hidden" name="opt_save" value="1">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_name}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_server_name}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="game_name"  value="{game_name}" type="text" maxlength="60">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_logo}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_server_logo}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="game_logo"  value="{game_logo}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_lang}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<select name="language">{language_settings}</select>
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_general_speed}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_normal_speed}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="game_speed" value="{game_speed}" type="text" maxlength="5">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_fleet_speed}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_normal_speed_fleett}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="fleet_speed" value="{fleet_speed}" type="text" maxlength="5">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_resources_producion_speed}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_normal_speed_resoruces}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="resource_multiplier" value="{resource_multiplier}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_admin_email}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="admin_email" size="60" maxlength="254" value="{admin_email}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_forum_link}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="forum_url" size="60" maxlength="254" value="{forum_url}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_server_op_close}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="closed"{closed} type="checkbox" />
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_server_status_message}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<textarea name="close_reason" cols="80" rows="5" size="80" >{close_reason}</textarea>
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_ssl_enabled}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="ssl_enabled"{ssl_enabled} type="checkbox" />
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_date_time_zone}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<select name="date_time_zone">{date_time_zone}</select>
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_date_format}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="date_format" value="{date_format}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_date_format_extended}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="date_format_extended" value="{date_format_extended}" type="text">

				<br /><br />
				<h4>{se_several_parameters}</h4>

				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_admin_protection}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_title_admins_protection}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="adm_attack" {adm_attack} type="checkbox" />
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_debug_mode}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_debug_message}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="debug"{debug} type="checkbox" />
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_ships_cdr}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_ships_cdr_message}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="Fleet_Cdr" maxlength="3" size="3" value="{shiips}" type="text"> %
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_def_cdr}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_def_cdr_message}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="Defs_Cdr" maxlength="3" size="3" value="{defenses}" type="text"> %

				<br /><br />
				<h4>{se_noob_protect}</h4>

				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_noob_protect_active}</strong>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="noobprotection"{noobprot} type="checkbox" />
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_noob_protect2}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_noob_protect_e2}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="noobprotectiontime" value="{noobprot2}" type="text">
				<label>
					<ul class="popups">
					    <li class="span3">
					        <div class="hover-group popup">
					            <div class="image-wrapper">
					            <strong>{se_noob_protect3}</strong>
					              <i class="icon-info-sign"></i>
					              <div class="hover-toggle btn-group">
					                  <span class="btn btn-info">{se_noob_protect_e3}</span>
					              </div>
					            </div>
					        </div>
					    </li>
					</ul>
				</label>
				<input name="noobprotectionmulti" value="{noobprot3}" type="text">

				<div align="center">
					<input value="{se_save_parameters}" type="submit" class="btn btn-primary">
				</div>

			</form>
          </div>
        </div><!--/span-->