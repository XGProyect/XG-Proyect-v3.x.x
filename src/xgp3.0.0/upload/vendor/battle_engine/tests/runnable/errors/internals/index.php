<?php

if ($handle = opendir('.')) {
    $thelist ="";
    while (false !== ($file = readdir($handle)))
    {
        if ($file != "." && $file != ".." && strtolower(substr($file, strrpos($file, '.') + 1)) == 'html')
        {
            $thelist .= '<li><a href="'.$file.'">'.$file.'</a></li>';
        }
    }
    closedir($handle);
}


echo "
<!DOCTYPE HTML>
<head>
	<meta http-equiv=\"content-type\" content=\"text/html\" />
	<title>internals</title>
</head>
<body>
<ul>
$thelist
</ul>
</body>
</html>
";