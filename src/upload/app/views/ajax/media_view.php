
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css"></style></head><body><div class="inner-box clearfix">
            <h2>{aj_media_wallpapers}</h2>
            <div id="wallpapers" class="clearfix">
            </div>
        </div>
        <div class="inner-box last clearfix">
            <h2>{aj_media_pictures}</h2>
            <div id="screens">
            </div>
        </div>
        <div class="inner-box last clearfix">
            <h2>{aj_media_concept_art}</h2>
            <div id="screens">
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                //GALLERY Fancybox
                $('#wallpapers a.zoom, #screens a').fancybox({
                    'overlayColor': '#000',
                    'hideOnContentClick': true,
                    'onStart': function () {
                        $.validationEngine.closePrompt('.formError', true);
                    }
                });
            });
        </script></body></html>