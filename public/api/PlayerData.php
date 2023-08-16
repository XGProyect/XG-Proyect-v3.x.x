<?php
require (dirname( __FILE__, 3 ) .'/config/config.php');  // Assuming your config file is in the default XGP location

// Fetch data from {$db_prefix}_users table
$stmt = $pdo->prepare("SELECT user_id, user_name, user_authlevel, user_ally_id FROM " . DB_PREFIX . "users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process and transform user data
$playerData = [];
foreach ($users as $user) {
    $playerType = '';
    switch ($user['user_authlevel']) {
        case 0:
            $playerType = 'Player';
            break;
        case 1:
            $playerType = 'GO';
            break;
        case 2:
            $playerType = 'SGO';
            break;
        case 3:
            $playerType = 'Admin';
            break;
    }

    // Fetching planet data for the user
    $planetStmt = $pdo->prepare("SELECT planet_id, planet_galaxy, planet_system, planet_planet, planet_type FROM ge1_planets WHERE planet_user_id = ? ORDER BY planet_galaxy, planet_system, planet_planet");
    $planetStmt->execute([$user['user_id']]);
    $planets = $planetStmt->fetchAll(PDO::FETCH_ASSOC);

    $planetData = [];
    $previousCoordinates = "";
    foreach ($planets as $planet) {
        $coordinates = $planet['planet_galaxy'] . ':' . $planet['planet_system'] . ':' . $planet['planet_planet'];
        
        if ($planet['planet_type'] == 3 && $coordinates === $previousCoordinates) {
            end($planetData); // Move internal pointer to the end of the array
            $planetData[key($planetData)]['Coordinates'] .= " (m)";
        } else {
            $planetID = $planet['planet_type'] == 1 ? 'Planet ID' : 'Moon ID';
            $planetData[] = [
                $planetID => $planet['planet_id'],
                'Coordinates' => $coordinates
            ];
            $previousCoordinates = $coordinates;
        }
    }

    // Fetching player statistics data
    $statsStmt = $pdo->prepare("SELECT user_statistic_buildings_points, user_statistic_defenses_points, user_statistic_ships_points, user_statistic_technology_points FROM ge1_users_statistics WHERE user_statistic_user_id = ?");
    $statsStmt->execute([$user['user_id']]);
    $statistics = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Preparing the player data array
$playerInfo = [
    'Player Name' => $user['user_name'],
    'Player Type' => $playerType
];

// Include the Ally ID only if it's set and not null
if (isset($user['user_ally_id']) && $user['user_ally_id'] != 0) {
    $playerInfo['Ally ID'] = $user['user_ally_id'];
}

$playerInfo['Planets'] = $planetData;
$playerInfo['Economy Points'] = number_format(intval($statistics['user_statistic_buildings_points']));
$playerInfo['Defense Points'] = number_format(intval($statistics['user_statistic_defenses_points']));
$playerInfo['Fleet Points'] = number_format(intval($statistics['user_statistic_ships_points']));
$playerInfo['Research Points'] = number_format(intval($statistics['user_statistic_technology_points']));


// Add "PlayerID_" title to the user_id key
$key = 'PlayerID_' . $user['user_id'];
$playerData[$key] = $playerInfo;

}

// Output Pretty JSON
$json = json_encode($playerData, JSON_PRETTY_PRINT);

// Remove curly braces and square brackets from the JSON
$json_no_brackets = preg_replace('/[{}\[\]]/', '', $json);

// Output the styled JSON in HTML
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Players API Result</title>
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
