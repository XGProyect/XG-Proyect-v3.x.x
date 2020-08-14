<?php
$lang = [
    // system
    'sys_building_queue_build_order' => 'Building Order',
    'sys_building_queue_destroy_order' => 'Demolition Order',
    'sys_building_queue_not_enough_resources' => 'The %s of your building %s level %s at %s could not be performed.<br><br>Insufficient resources: %s.',
    'sys_building_queue_not_enough_resources_from' => 'System Message',
    'sys_building_queue_not_enough_resources_subject' => 'Production cancelled',

    //topnav
    'tn_commander' => 'Commander',
    'tn_admiral' => 'Admiral',
    'tn_engineer' => 'Engineer',
    'tn_geologist' => 'Geologist',
    'tn_technocrat' => 'Technocrat',
    'tn_add_admiral' => '+2 max. fleet slots',
    'tn_add_engineer' => 'Minimizes losses in half defenses,<br/>+10% energy production',
    'tn_add_geologist' => '+10% mine production',
    'tn_add_technocrat' => '+2 Espionage level,<br/>25% Less time for research',
    'tn_get_now' => 'Get now!',
];

/**
 *
 *
 * OLD LINES
 *
 *
 */

//SERVER GENERALS
$lang['Metal'] = 'Metal';
$lang['Crystal'] = 'Crystal';
$lang['Deuterium'] = 'Deuterium';
$lang['Darkmatter'] = 'Dark Matter';
$lang['Energy'] = 'Energy';
$lang['Messages'] = 'Messages';
$lang['write_message'] = 'Write Message';
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
//TOPNAV
$lang['tn_vacation_mode'] = 'Vacation mode active until ';
$lang['tn_vacation_mode_active'] = "Vacation mode active";
$lang['tn_delete_mode'] = 'Your account was set for deletion. Deletion date: ';

//----------------------------------------------------------------------------//
//LEFT MENU
$lang['lm_overview'] = 'Overview';
$lang['lm_galaxy'] = 'Galaxy';
$lang['lm_empire'] = 'Empire';
$lang['lm_fleet'] = 'Fleet';
$lang['lm_movement'] = 'Fleet movement';
$lang['lm_resources'] = 'Resources';
$lang['lm_resources_settings'] = 'Resource settings';
$lang['lm_station'] = 'Facilities';
$lang['lm_research'] = 'Research';
$lang['lm_shipyard'] = 'Shipyard';
$lang['lm_defenses'] = 'Defense';
$lang['lm_officiers'] = 'Recruit Officers';
$lang['lm_trader'] = 'Merchant';
$lang['lm_technology'] = 'Technology';
$lang['lm_messages'] = 'Messages';
$lang['lm_alliance'] = 'Alliance';
$lang['lm_buddylist'] = 'Buddies';
$lang['lm_notes'] = 'Notes';
$lang['lm_statistics'] = 'Highscore';
$lang['lm_search'] = 'Search';
$lang['lm_options'] = 'Options';
$lang['lm_forums'] = 'Board';
$lang['lm_logout'] = 'Log out';
$lang['lm_administration'] = 'Administration';
$lang['lm_module_not_accesible'] = 'Page could not be found.';

//----------------------------------------------------------------------------//
//BUILDINGS - RESEARCH - SHIPYARD - DEFENSES
$lang['bd_dismantle'] = 'Dismantle';
$lang['bd_interrupt'] = 'Interrupt';
$lang['bd_cancel'] = 'cancel';
$lang['bd_working'] = 'Working';
$lang['bd_build'] = 'build';
$lang['bd_build_next_level'] = 'upgrade level ';
$lang['bd_add_to_list'] = 'Add to list';
$lang['bd_no_more_fields'] = 'No more room on the planet';
$lang['bd_remaining'] = 'Remaining';
$lang['bd_lab_required'] = 'You need to build a research laboratory on this planet first!';
$lang['bd_building_lab'] = 'Can not research when the laboratory is expanding';
$lang['bd_lvl'] = 'level';
$lang['bd_spy'] = ' spy';
$lang['bd_commander'] = ' commander';
$lang['bd_research'] = 'research';
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
$lang['bd_from'] = 'from<br />';

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
    1 => "Used in the extraction of metal ore, metal mines are of primary importance to all emerging and established empires.",
    2 => "Crystals are the main resource used to build electronic circuits and form certain alloy compounds.",
    3 => "Deuterium Synthesizers draw the trace Deuterium content from the water on a planet.",
    4 => "Solar power plants absorb energy from solar radiation. All mines need energy to operate.",
    12 => "The fusion reactor uses deuterium to produce energy.",
    14 => "Robotic factories provide construction robots to aid in the construction of buildings. Each level increases the speed of the upgrade of buildings.",
    15 => "This is the ultimate in robotics technology. Each level cuts the construction time for buildings, ships, and defences.",
    21 => "All types of ships and defensive facilities are built in the planetary shipyard.",
    22 => "Provides storage for excess metal.",
    23 => "Provides storage for excess crystal.",
    24 => "Giant tanks for storing newly-extracted deuterium.",
    31 => "A research lab is required in order to conduct research into new technologies.",
    33 => "The terraformer increases the usable surface of planets.",
    34 => "The alliance depot supplies fuel to friendly fleets in orbit helping with defence.",
    41 => "Because the moon has no atmosphere, you need a moon base to generate living space.",
    42 => "Using the sensor phalanx, the fleets of other empires can be discovered and observed. The larger the sensor chain phalanx, the greater the range to scan.",
    43 => "The huge quantum leaps are transmitters that are capable of sending large fleets throughout the universe without loss of time.",
    44 => "Missile silos are used to store missiles.",
    106 => "Information about other planets and moons can be gained using this technology.",
    108 => "More fleets can be commanded by increasing computer capacities. Each level of computer technology increases the maximum number of fleets by one.",
    109 => "Weapons technology makes weapons systems more efficient. Each level of weapons technology increases the weapon strength of units by 10 % of the base value.",
    110 => "Shielding technology makes the shields on ships and defensive facilities more efficient. Each level of shield technology increases the strength of the shields by 10 % of the base value.",
    111 => "Special alloys improve the armour on ships and defensive structures. The effectiveness of the armour can be increased by 10 % per level.",
    113 => "The command of different types of energy is necessary for many new technologies.",
    114 => "By integrating the 4th and 5th dimensions it is now possible to research a new kind of drive that is more economical and efficient.",
    115 => "The development of this drive makes some ships faster, although each level increases speed by only 10 % of the base value.",
    117 => "The impulse drive is based on the reaction principle. Further development of this drive makes some ships faster, although each level increases speed by only 20 % of the base value.",
    118 => "Hyperspace drive warps space around a ship. The development of this drive makes some ships faster, although each level increases speed by only 30 % of the base value.",
    120 => "Focusing light produces a beam that causes damage when it strikes an object.",
    121 => "The concentration of ions allows for the construction of cannons, which can inflict enormous damage and reduce the deconstruction costs per level by 4%.",
    122 => "A further development of ion technology which accelerates high-energy plasma, which then inflicts devastating damage and additionally optimises the production of metal, crystal and deuterium (1%/0.66%/0.33% per level).",
    123 => "Researchers on different planets communicate via this network.",
    124 => "With an astrophysics research module, ships can undertake long expeditions. Every second level of this technology will allow you to colonise an extra planet.",
    199 => "Firing a concentrated charge of graviton particles can create an artificial gravity field, which can destroy ships or even moons.",
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

//----------------------------------------------------------------------------//
// FLEET MOVEMENTS
$lang['cff_no_fleet_data'] = 'No fleet data';
$lang['cff_aproaching'] = 'They approach ';
$lang['cff_ships'] = ' ships';
$lang['cff_from_the_planet'] = 'from the planet ';
$lang['cff_from_the_moon'] = 'from the moon ';
$lang['cff_the_planet'] = 'the planet ';
$lang['cff_debris_field'] = 'debris field ';
$lang['cff_to_the_moon'] = 'to the moon ';
$lang['cff_the_position'] = 'position ';
$lang['cff_to_the_planet'] = ' to planet ';
$lang['cff_the_moon'] = ' the moon ';
$lang['cff_from_planet'] = 'the planet ';
$lang['cff_from_debris_field'] = 'the debris field ';
$lang['cff_from_the_moon'] = 'of the moon ';
$lang['cff_from_position'] = 'position ';
$lang['cff_missile_attack'] = 'Missile attack';
$lang['cff_to'] = ' to ';
$lang['cff_one_of_your'] = 'One of your ';
$lang['cff_a'] = 'One ';
$lang['cff_of'] = ' of ';
$lang['cff_goes'] = ' goes ';
$lang['cff_toward'] = ' toward ';
$lang['cff_with_the_mission_of'] = '. With the mission of: ';
$lang['cff_to_explore'] = ' to explore ';
$lang['cff_comming_back'] = ' comes back from ';
$lang['cff_back'] = 'Comming back';
$lang['cff_to_destination'] = 'Heading to destination';
$lang['cff_flotte'] = ' fleets';

//----------------------------------------------------------------------------//
// EXTRA LANGUAGE FUNCTIONS
$lang['fcm_moon'] = 'Moon';
$lang['fgp_require'] = 'Requires: ';
$lang['fgf_time'] = 'Construction Time: ';

/* end of INGAME.php */
