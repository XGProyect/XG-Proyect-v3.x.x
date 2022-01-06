<div class="inner-box clearfix">
    <h2>{aj_info_about_title}</h2>
    <p>{aj_info_about_description}</p>
</div>
<div class="inner-box clearfix">
    <h2>{aj_info_features}</h2>
    <ul>
        <li>{aj_info_description_line1}</li>
        <li>{aj_info_description_line2}</li>
        <li>{aj_info_description_line3}</li>
        <li>{aj_info_description_line4}</li>
        <li>{aj_info_description_line5}</li>
        <li>{aj_info_description_line6}</li>
        <li>{aj_info_description_line7}</li>
        <li>{aj_info_description_line8}</li>
    </ul>
    <a class="overlay button" href="index.php?page=team" title="{aj_info_team}">{aj_info_team}</a> <a class="overlay button" href="index.php?page=credits" title="{aj_info_credits}">{aj_info_credits}</a>
</div>
<div class="inner-box last clearfix">
    <h2>{aj_info_images}</h2>
    <div id="screens">
    </div>
    <br class="clearfloat" />
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //GALLERY Fancybox
        $('#ajaxContent a.overlay').fancybox({
            'onStart': function () {
                $.validationEngine.closePrompt('.formError', true);
            },
            type: 'iframe',
            'hideOnContentClick': true,
            height: 433,
            width: 480

        });
        $('#screens a').fancybox({
            'overlayColor': '#000',
            'hideOnContentClick': true,
            'onStart': function () {
                $.validationEngine.closePrompt('.formError', true);
            }
        });
    });
</script>