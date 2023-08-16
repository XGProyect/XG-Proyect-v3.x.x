<?php
require (dirname( __FILE__, 3 ) .'/config/config.php');  // Assuming your config file is in the default XGP location

// Fetching relevant game settings based on Option_id values
$option_ids = [1, 4, 5, 15, 16, 22];
$placeholders = implode(',', array_fill(0, count($option_ids), '?'));
$stmt = $pdo->prepare("SELECT Option_id, Option_name, Option_value FROM ge1_options WHERE Option_id IN ($placeholders)");
$stmt->execute($option_ids);
$options = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Transforming the fetched data
$serverdata = [];
foreach ($options as $option) {
    switch ($option['Option_id']) {
        case 1:
            $serverdata['Universe'] = $option['Option_value'];
            break;
        case 4:
            $serverdata['Uni Speed'] = $option['Option_value'] / 2500;
            break;
        case 5:
            $serverdata['Fleet Speed'] = $option['Option_value'] / 2500;
            break;
        case 15:
            $serverdata['Fleet to debris'] = $option['Option_value'] / 100;
            break;
        case 16:
            $serverdata['Def to debris'] = $option['Option_value'] / 100;
            break;
        case 22:
            $serverdata['Initial Planet Fields'] = $option['Option_value'];
            break;
    }
}

$json = json_encode($serverdata, JSON_PRETTY_PRINT);

// Remove curly braces and square brackets from the JSON
$json_no_brackets = preg_replace('/[{}\[\]]/', '', $json);

// Output the styled JSON in HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Settings API Result</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
        code { color: #333; }
        .key { color: blue; }
        .string { color: green; }
        .number { color: red; }
        .bool { color: #955; }
    </style>
</head>
<body>
<pre><code>$json_no_brackets</code></pre>
</body>
</html>
HTML;
?>
