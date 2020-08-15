<?php
$lang = [
    // system
    'sys_building_queue_build_order' => 'Building Order',
    'sys_building_queue_destroy_order' => 'Demolition Order',
    'sys_building_queue_not_enough_resources' => 'The %s of your building %s level %s at %s could not be performed.<br><br>Insufficient resources: %s.',
    'sys_building_queue_not_enough_resources_from' => 'System Message',
    'sys_building_queue_not_enough_resources_subject' => 'Production cancelled',
];

/**
 *
 *
 * OLD LINES
 *
 *
 */

//SERVER GENERALS
// DevelopmentsLib.php & UpdatesLibrary.php
$lang['Metal'] = 'Metal';
$lang['Crystal'] = 'Crystal';
$lang['Deuterium'] = 'Deuterium';
$lang['Darkmatter'] = 'Dark Matter';
$lang['Energy'] = 'Energy';
$lang['write_message'] = 'Write Message'; //FleetsLib.php

// TimingLib.php
$lang['online'] = 'Online';
$lang['minutes'] = '15 min';
$lang['offline'] = 'Offline';

// used by FleetsLib.php and GalaxyLib.php
// TODO: refactor and remove
$lang['type_mission'][1] = 'Attack';
$lang['type_mission'][2] = 'ACS Attack';
$lang['type_mission'][3] = 'Transport';
$lang['type_mission'][4] = 'Deploy';
$lang['type_mission'][5] = 'ACS Defense';
$lang['type_mission'][6] = 'Espionage';
$lang['type_mission'][7] = 'Colonize';
$lang['type_mission'][8] = 'Recycle';
$lang['type_mission'][9] = 'Destroy';
$lang['type_mission'][15] = 'Expedition';

//----------------------------------------------------------------------------//
//BUILDINGS - RESEARCH - SHIPYARD - DEFENSES
$lang['bd_cancel'] = 'cancel';
$lang['bd_remaining'] = 'Remaining';
$lang['bd_shipyard_required'] = 'You need to build a shipyard on this planet first!';
$lang['bd_building_shipyard'] = 'You can not build ships when the shipyard is upgrading';
$lang['bd_available'] = 'Available: ';
$lang['bd_build_ships'] = 'Build';
$lang['bd_protection_shield_only_one'] = 'The shield can be built only once!';
$lang['bd_actual_production'] = 'Actual production:';
$lang['bd_completed'] = 'Completed';
$lang['bd_operating'] = '(Working)';
$lang['bd_continue'] = 'continue';
$lang['bd_ready'] = 'finished';
$lang['bd_finished'] = 'finished';

//----------------------------------------------------------------------------//
//TECHTREE
$lang['tt_requirements'] = 'Requirements';
$lang['tt_lvl'] = 'level ';

$lang['tech'] = array(
    0 => "Buildings",
    1 => "Metal Mine",
    2 => "Crystal Mine",
    3 => "Deuterium Synthesizer",
    4 => "Solar Plant",
    12 => "Fusion Reactor",
    14 => "Robotics Factory",
    15 => "Nanite Factory",
    21 => "Shipyard",
    22 => "Metal Storage",
    23 => "Crystal Storage",
    24 => "Deuterium Tank",
    31 => "Research Lab",
    33 => "Terraformer",
    34 => "Alliance Depot",
    40 => "Lunar Buildings",
    41 => "Lunar Base",
    42 => "Sensor Phalanx",
    43 => "Jump Gate",
    44 => "Missile Silo",
    100 => "Research",
    106 => "Espionage Technology",
    108 => "Computer Technology",
    109 => "Weapons Technology",
    110 => "Shielding Technology",
    111 => "Armour Technology",
    113 => "Energy Technology",
    114 => "Hyperspace Technology",
    115 => "Combustion Drive",
    117 => "Impulse Drive",
    118 => "Hyperspace Drive",
    120 => "Laser Technology",
    121 => "Ion Technology",
    122 => "Plasma Technology",
    123 => "Intergalactic Research Network",
    124 => "Astrophysics",
    199 => "Graviton Technology",
    200 => "Ships",
    202 => "Small Cargo",
    203 => "Large Cargo",
    204 => "Light Fighter",
    205 => "Heavy Fighter",
    206 => "Cruiser",
    207 => "Battleship",
    208 => "Colony Ship",
    209 => "Recycler",
    210 => "Espionage Probe",
    211 => "Bomber",
    212 => "Solar Satellite",
    213 => "Destroyer",
    214 => "Deathstar",
    215 => "Battlecruiser",
    400 => "Defense",
    401 => "Rocket Launcher",
    402 => "Light Laser",
    403 => "Heavy Laser",
    404 => "Gauss Cannon",
    405 => "Ion Cannon",
    406 => "Plasma Turret",
    407 => "Small Shield Dome",
    408 => "Large Shield Dome",
    502 => "Anti-Ballistic Missiles",
    503 => "Interplanetary Missiles",
);

$lang['res']['descriptions'] = array(
    202 => "The small cargo is an agile ship which can quickly transport resources to other planets.",
    203 => "This cargo ship has a much larger cargo capacity than the small cargo, and is generally faster thanks to an improved drive.",
    204 => "This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable on its own. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargoes to hostile planets with minor defences.",
    205 => "This fighter is better armoured and has a higher attack strength than the light fighter.",
    206 => "Cruisers are armoured almost three times as heavily as heavy fighters and have more than twice the firepower. In addition, they are very fast.",
    207 => "Battleships form the backbone of a fleet. Their heavy cannons, high speed, and large cargo holds make them opponents to be taken seriously.",
    208 => "Vacant planets can be colonised with this ship.",
    209 => "Recyclers are the only ships able to harvest debris fields floating in a planet`s orbit after combat.",
    210 => "Espionage probes are small, agile drones that provide data on fleets and planets over great distances.",
    211 => "The bomber was developed especially to destroy the planetary defences of a world.",
    212 => "Solar satellites are simple platforms of solar cells, located in a high, stationary orbit. They gather sunlight and transmit it to the ground station via laser. A solar satellite produces 27 energy on this planet.",
    213 => "The destroyer is the king of the warships.",
    214 => "The destructive power of the deathstar is unsurpassed.",
    215 => "The Battlecruiser is highly specialized in the interception of hostile fleets.",
    401 => "The rocket launcher is a simple, cost-effective defensive option.",
    402 => "Concentrated firing at a target with photons can produce significantly greater damage than standard ballistic weapons.",
    403 => "The heavy laser is the logical development of the light laser.",
    404 => "The Gauss Cannon fires projectiles weighing tons at high speeds.",
    405 => "The Ion Cannon fires a continuous beam of accelerating ions, causing considerable damage to objects it strikes.",
    406 => "Plasma Turrets release the energy of a solar flare and surpass even the destroyer in destructive effect.",
    407 => "The small shield dome covers an entire planet with a field which can absorb a tremendous amount of energy.",
    408 => "The evolution of the small shield dome can employ significantly more energy to withstand attacks.",
    502 => "Anti-Ballistic Missiles destroy attacking interplanetary missiles",
    503 => "Interplanetary Missiles destroy enemy defences. Your interplanetary missiles have got a coverage of %s systems.",
);

/* end of INGAME.php */
