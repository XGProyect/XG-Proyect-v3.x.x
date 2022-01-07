<br />
<div id="content">
    <table width="600px">
        <tbody>
            <tr>
                <td class="c" colspan="2">{in_title_head} {name}</td>
            </tr>
            <tr>
                <th colspan="2">
                    <table>
                        <tbody>
                            <tr>
                                <td style="vertical-align: top;">
                                    <img src="{dpath}elements/{image}.gif" style="width: 120px;height: 120px;border: 2px solid #415680;">
                                </td>
                                <td>
                                    <p style="max-width: 538px;">{description}</p>
                                    <ul class="rapid_fire">
                                        {rf_info_fr}
                                        {rf_info_to}
                                    </ul>
                                    <table class="general_table technical">
                                        <thead>
                                            <tr>
                                                <th colspan="2">{in_technical_data}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th style="font-weight: 700;">{in_struct_pt}</th>
                                                <th>
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_struct_pt}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{armour_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_research_bonus}<span style=display:block;font-size:8px;font-weight:400;>({armour_level} x {armour_bonus})</span></th><th style=text-align:right;font-weight:400;>{armour_bonus_total}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{armour_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {armour_total}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-weight: 700;">{in_shield_pt}</th>
                                                <th>
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_shield_pt}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{shielding_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_research_bonus}<span style=display:block;font-size:8px;font-weight:400;>({shielding_tech_level} x {shielding_bonus})</span></th><th style=text-align:right;font-weight:400;>{shielding_bonus_total}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{shielding_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {shielding_total}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-weight: 700;">{in_attack_pt}</th>
                                                <th>
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_attack_pt}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{weapons_technology_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_research_bonus}<span style=display:block;font-size:8px;font-weight:400;>({weapons_technology_level} x {weapons_technology_bonus})</span></th><th style=text-align:right;font-weight:400;>{weapons_technology_bonus_total}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{weapons_technology_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {weapons_technology_total}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-weight: 700;">{in_base_speed}</th>
                                                <th>
                                                    <!-- <span style="cursor: help;border-bottom: 1px dotted #9c0;">{base_speed} {upd_speed}</span> -->
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_base_speed}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{speed_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_research_bonus}<span style=display:block;font-size:8px;font-weight:400;>({speed_research_level} x {speed_research_bonus})</span></th><th style=text-align:right;font-weight:400;>{speed_research_bonus_total}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{speed_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {speed_total}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-weight: 700;">{in_capacity}</th>
                                                <th>
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_base_speed}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{capacity_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_research_bonus}<span style=display:block;font-size:8px;font-weight:400;>({capacity_basic_value} x {capacity_research_percent}%)</span></th><th style=text-align:right;font-weight:400;>{capacity_research_bonus}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{capacity_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {capacity_total}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-weight: 700;">{in_consumption}</th>
                                                <th>
                                                    <span style="cursor: help;border-bottom: 1px dotted #9c0;" onmouseover="return overlib('<table width=180px><tbody><tr><td class=c style=text-align:center colspan=2>{in_base_speed}</td></tr><tr><th style=text-align:left;font-weight:400;>{in_basic_value}</th><th style=text-align:right;font-weight:400;>{consumption_basic_value}</th></tr><tr><th style=text-align:left;font-weight:400;>{in_bonus}<span style=display:block;font-size:8px;font-weight:400;>({consumption_percent}%)</span></th><th style=text-align:right;font-weight:400;>-{consumption_bonus}</th></tr><tr><th colspan=2 style=\'border-top:1px solid #848484;\'><span style=color:#9c0;padding-left:5px;float:right;font-weight:400;>{consumption_total}</span></th></tr></tbody></table>',LEFT, ABOVE);" onmouseout="return nd();">
                                                        {consumption_total}
                                                    </span>
                                                </th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </th>
            </tr>
        </tbody>
    </table>

</div>