<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css"></style>
</head>

<body>
    <div class="inner-box clearfix">
        <h2>{aj_home_conquer_universe}</h2>
        <p>{aj_home_description}</p>
        <a class="overlay button" href="index.php?page=rules" title="{aj_home_rules}">{aj_home_rules}</a>
    </div>

    <div id="trailer" class="inner-box last clearfix">
        <h2 id="trailer">{aj_home_trailer}</h2>
        <div id="flashTrailer">
            <iframe width="425" height="270" src="https://www.youtube.com/embed/HhSnnQg4oUc" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>

    <script type="text/javascript">
        // Trailer-Klick-Error-Ausblendung.
        $('#trailer').click(function() {
            $.validationEngine.closePrompt('.formError', true);
        });

        $('#ajaxContent a.overlay').fancybox({
            'onStart': function() {
                $.validationEngine.closePrompt('.formError', true);
            },
            type: 'iframe',
            'hideOnContentClick': true,
            height: 433,
            width: 480
        });
    </script>
</body>

</html>