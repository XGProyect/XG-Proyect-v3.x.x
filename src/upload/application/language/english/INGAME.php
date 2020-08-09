<?php
$lang = [
    // buildings
    'building_metal_mine' => "Metal Mine",
    'building_crystal_mine' => "Crystal Mine",
    'building_deuterium_sintetizer' => "Deuterium Synthesizer",
    'building_solar_plant' => "Solar Plant",
    'building_fusion_reactor' => "Fusion Reactor",
    'building_robot_factory' => "Robotics Factory",
    'building_nano_factory' => "Nanite Factory",
    'building_hangar' => "Shipyard",
    'building_metal_store' => "Metal Storage",
    'building_crystal_store' => "Crystal Storage",
    'building_deuterium_tank' => "Deuterium Tank",
    'building_laboratory' => "Research Lab",
    'building_terraformer' => "Terraformer",
    'building_ally_deposit' => "Alliance Depot",
    'building_mondbasis' => "Lunar Base",
    'building_phalanx' => "Sensor Phalanx",
    'building_jump_gate' => "Jump Gate",
    'building_missile_silo' => "Missile Silo",

    // defenses
    'defense_rocket_launcher' => 'Rocket Launcher',
    'defense_light_laser' => 'Light Laser',
    'defense_heavy_laser' => 'Heavy Laser',
    'defense_gauss_cannon' => 'Gauss Cannon',
    'defense_ion_cannon' => 'Ion Cannon',
    'defense_plasma_turret' => 'Plasma Turret',
    'defense_small_shield_dome' => 'Small Shield Dome',
    'defense_large_shield_dome' => 'Large Shield Dome',
    'defense_anti-ballistic_missile' => 'Anti-Ballistic Missiles',
    'defense_interplanetary_missile' => 'Interplanetary Missiles',

    // ships
    'ship_small_cargo_ship' => 'Small Cargo',
    'ship_big_cargo_ship' => 'Large Cargo',
    'ship_light_fighter' => 'Light Fighter',
    'ship_heavy_fighter' => 'Heavy Fighter',
    'ship_cruiser' => 'Cruiser',
    'ship_battleship' => 'Battleship',
    'ship_colony_ship' => 'Colony Ship',
    'ship_recycler' => 'Recycler',
    'ship_espionage_probe' => 'Espionage Probe',
    'ship_bomber' => 'Bomber',
    'ship_solar_satellite' => 'Solar Satellite',
    'ship_destroyer' => 'Destroyer',
    'ship_deathstar' => 'Deathstar',
    'ship_battlecruiser' => 'Battlecruiser',

    // research
    "research_espionage_technology" => 'Espionage Technology',
    "research_computer_technology" => 'Computer Technology',
    "research_weapons_technology" => 'Weapons Technology',
    "research_shielding_technology" => 'Shielding Technology',
    "research_armour_technology" => 'Armour Technology',
    "research_energy_technology" => 'Energy Technology',
    "research_hyperspace_technology" => 'Hyperspace Technology',
    "research_combustion_drive" => 'Combustion Drive',
    "research_impulse_drive" => 'Impulse Drive',
    "research_hyperspace_drive" => 'Hyperspace Drive',
    "research_laser_technology" => 'Laser Technology',
    "research_ionic_technology" => 'Ion Technology',
    "research_plasma_technology" => 'Plasma Technology',
    "research_intergalactic_research_network" => 'Intergalactic Research Network',
    "research_astrophysics" => 'Astrophysics',
    "research_graviton_technology" => 'Graviton Technology',

    // types of planets or galaxy objects
    'planet_type' => [
        1 => 'Planet',
        2 => 'Debris',
        3 => 'Moon',
    ],

    // types of planets or galaxy objects
    'planet_type_shortcuts' => [
        1 => '(P)',
        2 => '(D)',
        3 => '(M)',
    ],

    // type of structures
    'construction' => 'Construction',
    'research' => 'Research',
    'ships' => 'Ships',
    'missiles' => 'Rockets',
    'defenses' => 'Defence',

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
$lang['ge_home_planet'] = 'Homeworld';
$lang['ge_colony'] = 'Colony';

$lang['online'] = 'Online';
$lang['minutes'] = '15 min';
$lang['offline'] = 'Offline';

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

$lang['user_level'] = array(
    '0' => 'Player',
    '1' => 'Moderator',
    '2' => 'Operator',
    '3' => 'Administrator',
);

//SHORT NAMES FOR COMBAT REPORTS
$lang['tech_rc'] = array(
    202 => "S. cargo",
    203 => "L. cargo",
    204 => "F. light",
    205 => "F. heavy",
    206 => "Cruiser",
    207 => "Battleship",
    208 => "Col. Ship",
    209 => "Recycler",
    210 => "Probes",
    211 => "Bomber",
    212 => "S. Satellite",
    213 => "Destroyer",
    214 => "Deathstar",
    215 => "Battlecru.",
    401 => "Rocket L.",
    402 => "Light L.",
    403 => "Heavy L.",
    404 => "Gauss C.",
    405 => "Ion C.",
    406 => "Plasma T.",
    407 => "S. Dome",
    408 => "L. Dome",
);

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
//EMPIRE
$lang['iv_imperium_title'] = 'Empire View';
$lang['iv_planet'] = 'Planet';
$lang['iv_name'] = 'Name';
$lang['iv_coords'] = 'Coords';
$lang['iv_fields'] = 'Fields';
$lang['iv_resources'] = 'Resources';
$lang['iv_buildings'] = 'Facilities';
$lang['iv_technology'] = 'Research';
$lang['iv_ships'] = 'Fleet';
$lang['iv_defenses'] = 'Defenses';

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
//RESOURCES
$lang['rs_amount'] = 'quantity';
$lang['rs_lvl'] = 'level';
$lang['rs_production_on_planet'] = 'Resource production on planet "%s"';
$lang['rs_basic_income'] = 'Basic Income';
$lang['rs_storage_capacity'] = 'Storage Capacity';
$lang['rs_calculate'] = 'calculate';
$lang['rs_sum'] = 'Total:';
$lang['rs_daily'] = 'Res per day:';
$lang['rs_weekly'] = 'Res per week:';

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
//INFOS
$lang['in_jump_gate_done'] = 'The jump gate was used, the next jump can be made in: ';
$lang['in_jump_gate_error_data'] = 'Error, data for the jump are not correct!';
$lang['in_jump_gate_not_ready_target'] = 'The jump gate is not ready on the finish moon, will be ready in ';
$lang['in_jump_gate_doesnt_have_one'] = 'You have no jump gate in the moon!';
$lang['in_jump_gate_already_used'] = 'The jump gate was used, time to recharge its energy: ';
$lang['in_jump_gate_available'] = 'available';
$lang['in_rf_again'] = 'Rapidfire against';
$lang['in_rf_from'] = 'Rapidfire from';
$lang['in_level'] = 'Level';
$lang['in_prod_p_hour'] = 'production/hour';
$lang['in_difference'] = 'Difference';
$lang['in_used_energy'] = 'Energy consumption';
$lang['in_prod_energy'] = 'Energy Production';
$lang['in_used_deuter'] = 'Deuterium consumption';
$lang['in_range'] = 'Sensor range';
$lang['in_title_head'] = 'Information of';
$lang['in_name'] = 'Name';
$lang['in_struct_pt'] = 'Structural Integrity';
$lang['in_shield_pt'] = 'Shield Strength';
$lang['in_attack_pt'] = 'Attack Strength';
$lang['in_capacity'] = 'Cargo Capacity';
$lang['in_units'] = 'units';
$lang['in_base_speed'] = 'Base speed';
$lang['in_consumption'] = 'Fuel usage (Deuterium)';
$lang['in_jump_gate_start_moon'] = 'Start moon';
$lang['in_jump_gate_finish_moon'] = 'Finish moon';
$lang['in_jump_gate_select_ships'] = 'Use Jump Gate: number of ships';
$lang['in_jump_gate_jump'] = 'Jump';
$lang['in_destroy'] = 'Destroy:';
$lang['in_needed'] = 'Requires';
$lang['in_dest_durati'] = 'Destruction time';
$lang['in_storage_capacity'] = 'Storage capacity';
$lang['in_max_colonies'] = 'Maximun colonies';
$lang['in_max_expeditions'] = 'Maximun expeditions';
$lang['in_astrophysics_first'] = 'Positions 3 and 13 can be populated from level 4 onwards.';
$lang['in_astrophysics_second'] = 'Positions 2 and 14 can be populated from level 6 onwards.';
$lang['in_astrophysics_third'] = 'Positions 1 and 15 can be populated from level 8 onwards.';

// -------------------------- MINES ------------------------------------------------------------------------------------------------------//
$lang['info'][1]['name'] = "Metal Mine";
$lang['info'][1]['description'] = "Metal is the primary resource used in the foundation of your Empire. At greater depths, the mines can produce more output of viable metal for use in the construction of buildings, ships, defense systems, and research. As the mines drill deeper, more energy is required for maximum production. As metal is the most abundant of all resources available, its value is considered to be the lowest of all resources for trading.";
$lang['info'][2]['name'] = "Crystal Mine";
$lang['info'][2]['description'] = "Crystals are the main resource used to build electronic circuits for computers and other electronic circuits and form certain alloy compounds for shields. Compared to the metal production process, the processing of raw crystalline structures into industrial crystals requires special processing. As such, more energy is required to process the raw crystal than needed for metal. Development of ships and buildings, and specialized research upgrades, require a certain quantity of crystals.";
$lang['info'][3]['name'] = "Deuterium Synthesizer";
$lang['info'][3]['description'] = "Deuterium is also called heavy hydrogen. It is a stable isotope of hydrogen with a natural abundance in the oceans of colonies of approximately one atom in 6500 of hydrogen (~154 PPM). Deuterium thus accounts for approximately 0.015% (on a weight basis, 0.030%) of all. Deuterium is processed by special synthesizers which can separate the water from the Deuterium using specially designed centrifuges. The upgrade of the synthesizer allows for increasing the amount of Deuterium deposits processed. Deuterium is used when carrying out sensor phalanx scans, viewing galaxies, as fuel for ships, and performing specialized research upgrades.";

// -------------------------- ENERGY ----------------------------------------------------------------------------------------------------//
$lang['info'][4]['name'] = "Solar Plant";
$lang['info'][4]['description'] = "Gigantic solar arrays are used to generate power for the mines and the deuterium synthesizer. As the solar plant is upgraded, the surface area of the photovoltaic cells covering the planet increases, resulting in a higher energy output across the power grids of your planet.";
$lang['info'][12]['name'] = "Fusion Reactor";
$lang['info'][12]['description'] = "In fusion power plants, hydrogen nuclei are fused into helium nuclei under enormous temperature and pressure, releasing tremendous amounts of energy. For each gram of Deuterium consumed, up to 41,32*10^-13 Joule of energy can be produced; with 1 g you are able to produce 172 MWh energy.<br><br>Larger reactor complexes use more deuterium and can produce more energy per hour. The energy effect could be increased by researching energy technology.<br><br>The energy production of the fusion plant is calculated like that:<br>30 * [Level Fusion Plant] * (1,05 + [Level Energy Technology] * 0,01) ^ [Level Fusion Plant]";

// -------------------------- BUILDINGS ----------------------------------------------------------------------------------------------------//
$lang['info'][14]['name'] = "Robotics Factory";
$lang['info'][14]['description'] = "The Robotics Factory primary goal is the production of State of the Art construction robots. Each upgrade to the robotics factory results in the production of faster robots, which is used to reduce the time needed to construct buildings.";
$lang['info'][15]['name'] = "Nanite Factory";
$lang['info'][15]['description'] = "A nanomachine, also called a nanite, is a mechanical or electromechanical device whose dimensions are measured in nanometers (millionths of a millimeter, or units of 10^-9 meter). The microscopic size of nanomachines translates into higher operational speed. This factory produces nanomachines that are the ultimate evolution in robotics technology. Once constructed, each upgrade significantly decreases production time for buildings, ships, and defensive structures.";
$lang['info'][21]['name'] = "Shipyard";
$lang['info'][21]['description'] = "The planetary shipyard is responsible for the construction of spacecraft and defensive mechanisms. As the shipyard is upgraded, it can produce a wider variety of vehicles at a much greater rate of speed. If a nanite factory is present on the planet, the speed at which ships are constructed is massively increased.";
$lang['info'][22]['name'] = "Metal Storage";
$lang['info'][22]['description'] = "This storage facility is used to store metal ore. Each level of upgrading increases the amount of metal ore that can be stored. If the storage capacity is exceeded, the metal mines are automatically shut down to prevent a catastrophic collapse in the metal mine shafts.";
$lang['info'][23]['name'] = "Crystal Storage";
$lang['info'][23]['description'] = "Raw crystal is stored in this building. With each level of upgrade, it increases the amount of crystal can be stored. Once the mines output exceeds the storage capacity, the crystal mines automatically shut down to prevent a collapse in the mines.";
$lang['info'][24]['name'] = "Deuterium Tank";
$lang['info'][24]['description'] = "The Deuterium tank is for storing newly-synthesized deuterium. Once it is processed by the synthesizer, it is piped into this tank for later use. With each upgrade of the tank, the total storage capacity is increased. Once the capacity is reached, the Deuterium Synthesizer is shut down to prevent the tanks rupture.";
$lang['info'][31]['name'] = "Research Lab";
$lang['info'][31]['description'] = "An essential part of any empire, Research Labs are where new technologies are discovered, and older technologies are improved upon. With each level of the Research Lab constructed, the speed in which new technologies are researched is increased, while also unlocking newer technologies to research. In order to conduct research as quickly as possible, research scientists are immediately dispatched to the colony to begin work and development. In this way, knowledge about new technologies can easily be disseminated throughout the empire.";
$lang['info'][33]['name'] = "Terraformer";
$lang['info'][33]['description'] = "With the ever increasing mining of a colony, a problem arose. How can we continue to operate at a planets capacity and still survive? The land is being mined out and the atmosphere is deteriorating. Mining a colony to capacity can not only destroy the planet, but may kill all life on it. Scientists working feverishly discovered a method of creating enormous land masses using nanomachines. The Terraformer was born.<br><br>Once built, the Terraformer cannot be torn down.";
$lang['info'][34]['name'] = "Alliance Depot";
$lang['info'][34]['description'] = "The alliance depot supplies fuel to friendly fleets in orbit helping with defense. For each upgrade level of the alliance depot, 10,000 units of deuterium per hour can be sent to an orbiting fleet.";
$lang['info'][41]['name'] = "Lunar Base";
$lang['info'][41]['description'] = "Since a moon has no atmosphere and is an extremely hostile environment, a lunar base must first be built before the moon can be developed. The Lunar Base provides oxygen, heating, and gravity to create a living environment for the colonists. With each level constructed, a larger living and development area is provided within the biosphere. With each level of the Lunar Base constructed, three fields are developed for other buildings. <br>Once built, the lunar base can not be torn down.";
$lang['info'][42]['name'] = "Sensor Phalanx";
$lang['info'][42]['description'] = "Utilizing high-resolution sensors, the Sensor Phalanx first scans the spectrum of light, composition of gases, and radiation emissions from a distant world and transmits the data to a supercomputer for processing. Once the information is obtained, the supercomputer compares changes in the spectrum, gas composition, and radiation emissions, to a base line chart of known changes of the spectrum created by various ship movements. The resulting data then displays activity of any fleet within the range of the phalanx. To prevent the supercomputer from overheating during the process, it is cooled by utilizing 5k of processed Deuterium. To use the Phalanx, click on any planet in the Galaxy View within your sensors range.";
$lang['info'][43]['name'] = "Jump Gate";
$lang['info'][43]['description'] = "A Jump Gate is a system of giant transceivers capable of sending even the largest fleets to a receiving Gate anywhere in the universe without loss of time. Utilizing technology similar to that of a Worm Hole to achieve the jump, deuterium is not required. A recharge period of one hour must pass between jumps to allow for regeneration. Transporting resources through the Gate is not possible.";
$lang['info'][44]['name'] = "Missile Silo";
$lang['info'][44]['description'] = "When Earth destroyed itself in a full scale nuclear exchange back in the 21st century, the technology needed to build such weapons still existed in the universe. Scientists all over the universe worried about the threat of a nuclear bombardment from a rogue leader. So it was decided to use the same technology as a deterrent from launching such a horrible attack.<br><br> Missile silos are used to construct, store and launch interplanetary and anti-ballistic missiles. With each level of the silo, five interplanetary missiles or ten anti-ballistic missiles can be stored. Storage of both Interplanetary missiles and Anti-Ballistic missiles in the same silo is allowed.";

// -------------------------- TECHNOLOGY ----------------------------------------------------------------------------------------------------//
$lang['info'][106]['name'] = "Espionage Technology";
$lang['info'][106]['description'] = 'Espionage Technology is your intelligence gathering tool. This technology allows you to view your targets resources, fleets, buildings, and research levels using specially designed probes. Launched on your target, these probes transmit back to your planet an encrypted data file that is fed into a computer for processing. After processing, the information on your target is
then displayed for evaluation.<br><br> With Espionage Technology, the level of your technology to that of your target is critical. If your target has a higher level of Espionage Technology than you, you will need to launch more probes to gather all the information on your target. However this runs the great risk of detection by your target, resulting in the probes destruction. However, launching too few probes will result in missing information that is most critical, which could result in the total destruction of your fleet if an attack is launched.<br><br>At certain levels of Espionage Technology research, new attack warning systems are installed:<br><br>At Level <font color="#ff0000">2</font>, the total number of attacking ships will be displayed along with the simple attack warning.<br>At Level <font color="#ff0000">4</font>, the type of attacking ships along with the number of ships are displayed.
<br>At Level <font color="#ff0000">8</font>, the exact number of each type of ship launched is displayed.';
$lang['info'][108]['name'] = "Computer Technology";
$lang['info'][108]['description'] = "Once launched on any mission, fleets are controlled primarily by a series of computers located on the originating planet. These massive computers calculate the exact time of arrival, controls course corrections as needed, calculates trajectories, and regulates flight speeds. <br><br>With each level researched, the flight computer is upgraded to allow an additional slot to be launched. Computer technology should be continuously developed throughout the building of your empire.";
$lang['info'][109]['name'] = "Weapons Technology";
$lang['info'][109]['description'] = "Weapons Technology is a key research technology and is critical to your survival against enemy Empires. With each level of Weapons Technology researched, the weapons systems on ships and your defense mechanisms become increasingly more efficient. Each level increases the base strength of your weapons by 10% of the base value.";
$lang['info'][110]['name'] = "Shielding Technology";
$lang['info'][110]['description'] = "With the invention of the magnetosphere generator, scientists learned that an artificial shield could be produced to protect the crew in space ships not only from the harsh solar radiation environment in deep space, but also provide protection from enemy fire during an attack. Once scientists finally perfected the technology, a magnetosphere generator was installed on all ships and defence systems. <br><br>As the technology is advanced to each level, the magnetosphere generator is upgraded which provides an additional 10% strength to the shields base value.";
$lang['info'][111]['name'] = "Armour Technology";
$lang['info'][111]['description'] = "The environment of deep space is harsh. Pilots and crew on various missions not only faced intense solar radiation, they also faced the prospect of being hit by space debris, or destroyed by enemy fire in an attack. With the discovery of an aluminum-lithium titanium carbide alloy, which was found to be both light weight and durable, this afforded the crew a certain degree of protection. With each level of Armour Technology developed, a higher quality alloy is produced, which increases the armours strength by 10%.";
$lang['info'][113]['name'] = "Energy Technology";
$lang['info'][113]['description'] = "As various researches were advancing, it was discovered that the current technology of energy distribution was not sufficient enough to begin certain specialized researches. With each upgrade of your Energy Technology, new researches can be conducted which unlocks development of more sophisticated ships and defenses.";
$lang['info'][114]['name'] = "Hyperspace Technology";
$lang['info'][114]['description'] = 'In theory, the idea of hyperspace travel relies on the existence of a separate and adjacent dimension. When activated, a hyperspace drive shunts the starship into this other dimension, where it can cover vast distances in an amount of time greatly reduced from the time it would take in "normal" space. Once it reaches the point in hyperspace that corresponds to its destination in real space, it re-emerges.<br> Once a sufficient level of Hyperspace Technology is researched, the Hyperspace Drive is no longer just a theory.';
$lang['info'][115]['name'] = "Combustion Drive";
$lang['info'][115]['description'] = "The Combustion Drive is the oldest of technologies, but is still in use. With the Combustion Drive, exhaust is formed from propellants carried within the ship prior to use. In a closed chamber, the pressures are equal in each direction and no acceleration occurs. If an opening is provided at the bottom of the chamber then the pressure is no longer opposed on that side. The remaining pressure gives a resultant thrust in the side opposite the opening, which propels the ship forward by expelling the exhaust rearwards at extreme high speed.<br> <br>With each level of the Combustion Drive developed, the speed of small and large cargo ships, light fighters, recyclers, and espionage probes are increased by 10%.";
$lang['info'][117]['name'] = "Impulse Drive";
$lang['info'][117]['description'] = 'The impulse drive is essentially an augmented fusion rocket, usually consisting of a fusion reactor, an accelerator-generator, a driver coil assembly and a vectored thrust nozzle to direct the plasma exhaust. The fusion reaction generates a highly energized plasma. This plasma, ("electro-plasma") can be employed for propulsion, or can be diverted through the EPS to the power transfer grid, via EPS conduits, so as to supply other systems. The accelerated plasma is passed through the driver coils, thereby generating a subspace field which improves the propulsive effect. <br><br>With each level of the Impulse Drive developed, the speed of bombers, cruisers, heavy fighters, and colony ships are increased by 20% of the base value. Interplanetary missiles also travel farther with each level.';
$lang['info'][118]['name'] = "Hyperspace Drive";
$lang['info'][118]['description'] = "With the advancement of Hyperspace Technology, the Hyperspace Drive was created. Hyperspace is an alternate region of space co-existing with our own universe which may be entered using an energy field or other device. The HyperSpace Drive utilizes this alternate region by distorting the space-time continuum, which results in speeds that exceed the speed of light (otherwise known as FTL travel). During FTL travel, time and space is warped to the point that results in a trip that would normally take 1000 light years to be completed, to be accomplished in about an hour. <br><br>With each level the Hyperspace Drive is developed, the speed of battleships, battlecruisers, destroyers, and deathstars are increased by 30%.";
$lang['info'][120]['name'] = "Laser Technology";
$lang['info'][120]['description'] = "In physics, a laser is a device that emits light through a specific mechanism for which the term laser is an acronym: Light Amplification by Stimulated Emission of Radiation. Lasers have many uses to the empire, from upgrading computer communications systems to the creation of newer weapons and space ships.";
$lang['info'][121]['name'] = "Ion Technology";
$lang['info'][121]['description'] = "Simply put, an ion is an atom or a group of atoms that has acquired a net electric charge by gaining or losing one or more electrons. Utilized in advanced weapons systems, a concentrated beam of Ions can cause considerable damage to objects that it strikes.";
$lang['info'][122]['name'] = "Plasma Technology";
$lang['info'][122]['description'] = "In the universe, there exists four states of matter: solid, liquids, gas, and plasma. Being an advanced version of Ion technology, Plasma Technology expands on the destructive effect that Ion Technology delivered, and opens the door to create advanced weapons systems and ships. Plasma matter is created by superheating gas and compressing it with extreme high pressures to create a sphere of superheated plasma matter. The resulting plasma sphere causes considerable damage to the target in which the sphere is launched to.";
$lang['info'][123]['name'] = "Intergalactic Research Network";
$lang['info'][123]['description'] = "This is your deep space network to communicate researches to your colonies. With the IRN, faster research times can be achieved by linking the highest level research labs equal to the level of the IRN developed. <br><br>In order to function, each colony must be able to conduct the research independently.";
$lang['info'][124]['name'] = "Astrophysics";
$lang['info'][124]['description'] = "Further findings in the field of astrophysics allow for the construction of laboratories that can be fitted on more and more ships. This makes long expeditions far into unexplored areas of space possible. In addition these advancements can be used to further colonize the galaxies. For every two levels of this technology an additional planet can be utilized.";
$lang['info'][199]['name'] = "Graviton Technology";
$lang['info'][199]['description'] = "The graviton is an elementary particle that mediates the force of gravity in the framework of quantum field theory. The graviton must be massless (because the gravitational force has unlimited range) and must have a spin of 2 (because gravity is a second-rank tensor field). Graviton Technology is only used for one thing, for the construction of the fearsome DeathStar. <br><br>Out of all of the technologies to research, this one carries the most risk of detection during the phase of preparation.";

// -------------------------- SHIPS ----------------------------------------------------------------------------------------------------//
$lang['info'][202]['name'] = "Small Cargo";
$lang['info'][202]['description'] = "The first ship built by any emperor, the small cargo is an agile resource moving ship that has a cargo capacity of 5,000 resource units. This multi-use ship not only has the ability to quickly transport resources between your colonies, but also accompanies larger fleets on raiding missions on enemy targets. [Ship refitted with Impulse Drives once reached level 5]";
$lang['info'][203]['name'] = "Large Cargo";
$lang['info'][203]['description'] = "As time evolved, the raids on colonies resulted in larger and larger amounts of resources being captured. As a result, Small Cargos were being sent out in mass numbers to compensate for the larger captures. It was quickly learned that a new class of ship was needed to maximize resources captured in raids, yet also be cost effective. After much development, the Large Cargo was born.<br><br>To maximize the resources that can be stored in the holds, this ship has little in the way of weapons or armor. Thanks to the highly developed combustion engine installed, it serves as the most economical resource supplier between planets, and most effective in raids on hostile worlds.";
$lang['info'][204]['name'] = "Light Fighter";
$lang['info'][204]['description'] = "This is the first fighting ship all emperors will build. The light fighter is an agile ship, but vulnerable by themselves. In mass numbers, they can become a great threat to any empire. They are the first to accompany small and large cargo to hostile planets with minor defenses.";
$lang['info'][205]['name'] = "Heavy Fighter";
$lang['info'][205]['description'] = "In developing the heavy fighter, researchers reached a point at which conventional drives no longer provided sufficient performance. In order to move the ship optimally, the impulse drive was used for the first time. This increased the costs, but also opened new possibilities. By using this drive, there was more energy left for weapons and shields; in addition, high-quality materials were used for this new family of fighters. With these changes, the heavy fighter represents a new era in ship technology and is the basis for cruiser technology.<br><br>Slightly larger than the light fighter, the heavy fighter has thicker hulls, providing more protection, and stronger weaponry.";
$lang['info'][206]['name'] = "Cruiser";
$lang['info'][206]['description'] = "With the development of the heavy laser and the ion cannon, light and heavy fighters encountered an alarmingly high number of defeats that increased with each raid. Despite many modifications, weapons strength and armour changes, it could not be increased fast enough to effectively counter these new defensive measures. Therefore, it was decided to build a new class of ship that combined more armor and more firepower. As a result of years of research and development, the Cruiser was born. <br><br>Cruisers are armored almost three times of that of the heavy fighters, and possess more than twice the firepower of any combat ship in existence. They also possess speeds that far surpassed any spacecraft ever made. For almost a century, cruisers dominated the universe. However, with the development of Gauss cannons and plasma turrets, their predominance ended. They are still used today against fighter groups, but not as predominantly as before.";
$lang['info'][207]['name'] = "Battleship";
$lang['info'][207]['description'] = "Once it became apparent that the cruiser was losing ground to the increasing number of defense structures it was facing, and with the loss of ships on missions at unacceptable levels, it was decided to build a ship that could face those same type of defense structures with as little loss as possible. After extensive development, the Battleship was born. Built to withstand the largest of battles, the Battleship features large cargo spaces, heavy cannons, and high hyperdrive speed. Once developed, it eventually turned out to be the backbone of every raiding Emperors fleet.";
$lang['info'][208]['name'] = "Colony Ship";
$lang['info'][208]['description'] = "In the 20th Century, Man decided to go for the stars. First, it was landing on the Moon. After that, a space station was built. Mars was colonized soon afterwards. It was soon determined that our growth depended on colonizing other worlds. Scientists and engineers all over the world gathered together to develop mans greatest achievement ever. The Colony Ship is born.<br><br>This ship is used to prepare a newly discovered planet for colonization. Once it arrives at the destination, the ship is instantly transformed into habital living space to assist in populating and mining the new world. 9 Planets maximum can be colonized.";
$lang['info'][209]['name'] = "Recycler";
$lang['info'][209]['description'] = "As space battles became larger and more fierce, the resultant debris fields became too large to gather safely by conventional means. Normal transporters could not get close enough without receiving substantial damage. A solution was developed to this problem. The Recycler. <br><br>Thanks to the new shields and specially built equipment to gather wreckage, gathering debris no longer presented a danger. Each Recycler can gather 20,000 units of debris.";
$lang['info'][210]['name'] = "Espionage Probe";
$lang['info'][210]['description'] = "Espionage probes are small, agile drones that provide data on fleets and planets. Fitted with specially designed engines, it allows them to cover vast distances in only a few minutes. Once in orbit around the target planet, they quickly collect data and transmit the report back via your Deep Space Network for evaluation. But there is a risk to the intelligent gathering aspect. During the time the report is transmitted back to your network, the signal can be detected by the target and the probes can be destroyed.";
$lang['info'][211]['name'] = "Bomber";
$lang['info'][211]['description'] = "Over the centuries, as defenses were starting to get larger and more sophisticated, fleets were starting to be destroyed at an alarming rate. It was decided that a new ship was needed to break defenses to ensure maximum results. After years of research and development, the Bomber was created.<br><br>Using laser-guided targeting equipment and Plasma Bombs, the Bomber seeks out and destroys any defense mechanism it can find. As soon as the hyperspace drive is developed to Level 8, the Bomber is retrofitted with the hyperspace engine and can fly at higher speeds.";
$lang['info'][212]['name'] = "Solar Satellite";
$lang['info'][212]['description'] = "It quickly became apparent that more energy was needed to power larger mines then could be produced by conventional ground based solar planets and fusion reactors. Scientists worked on the problem and discovered a method of transmitting electrical energy to the colony using specially designed satellites in geosynchronous orbit.<br><br> Solar Satellites gather solar energy and transmit it to a ground station using advanced laser technology. The efficiency of a solar satellite depends on the strength of the solar radiation it receives. In principle, energy production in orbits closer to the sun is greater than for planets in orbits distant from the sun. Since the satellites primary goal is the transmission of energy, they lack shielding and weapons capability, and because of this they are usually destroyed in large numbers in a major battle. However they do possess a small self-defense mechanism to defend itself in an espionage mission from an enemy empire if the mission is detected.";
$lang['info'][213]['name'] = "Destroyer";
$lang['info'][213]['description'] = "The Destroyer is the result of years of work and development. With the development of Deathstars, it was decided that a class of ship was needed to defend against such a massive weapon.Thanks to its improved homing sensors, multi-phalanx Ion cannons, Gauss Cannons and Plasma Turrets, the Destroyer turned out to be one of the most fearsome ships created.<br><br>Because the destroyer is very large, its maneuverability is severely limited, which makes it more of a battle station than a fighting ship. The lack of maneuverability is made up for by its sheer firepower, but it also costs significant amounts of deuterium to build and operate.";
$lang['info'][214]['name'] = "Deathstar";
$lang['info'][214]['description'] = "The Deathstar is the ultimate ship ever created. This moon sized ship is the only ship that can be seen with the naked eye on the ground. By the time you spot it, unfortunately, it is too late to do anything.<br><br> Armed with a gigantic graviton cannon, the most advanced weapons system ever created in the Universe, this massive ship has not only the capability of destroying entire fleets and defenses, but also has the capability of destroying entire moons. Only the most advanced empires have the capability to build a ship of this mammoth size.";
$lang['info'][215]['name'] = "Battlecruiser";
$lang['info'][215]['description'] = "This ship is one of the most advanced fighting ships ever to be developed, and is particularly deadly when it comes to destroying attacking fleets. With its improved laser cannons on board and advanced Hyperspace engine, the Battlecruiser is a serious force to be dealt with in any attack.<br><br> Due to the ships design and its large weapons system, the cargo holds had to be cut, but this is compensated for by the lowered fuel consumption.";

// -------------------------- DEFENSES ----------------------------------------------------------------------------------------------------//
$lang['info'][401]['name'] = "Rocket Launcher";
$lang['info'][401]['description'] = "Your first basic line of defense. These are simple ground based launch facilities that fire conventional warhead tipped missiles at attacking enemy targets. As they are cheap to construct and no research is required, they are well suited for defending raids, but lose effectiveness defending from larger scale attacks. Once you begin construction on more advanced defense weapons systems, Rocket Launchers become simple fodder to allow your more damaging weapons to inflict greater damage for a longer period of time.<br><br>After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][402]['name'] = "Light Laser";
$lang['info'][402]['description'] = "As technology developed and more sophisticated ships were created, it was determined that a stronger line of defense was needed to counter the attacks. As Laser Technology advanced, a new weapon was designed to provide the next level of defense. Light Lasers are simple ground based weapons that utilize special targeting systems to track the enemy and fire a high intensity laser designed to cut through the hull of the target. In order to be kept cost effective, they were fitted with an improved shielding system, however the structural integrity is the same as that of the Rocket Launcher.<br><br> After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][403]['name'] = "Heavy Laser";
$lang['info'][403]['description'] = "The Heavy Laser is a practical, improved version of the Light Laser. Being more balanced than the Light Laser with improved alloy composition, it utilizes stronger, more densely packed beams, and even better onboard targeting systems. <br><br> After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][404]['name'] = "Gauss Cannon";
$lang['info'][404]['description'] = 'Far from being a science-fiction "weapon of tomorrow," the concept of a weapon using an electromagnetic impulse for propulsion originated as far back as the mid-to-late 1930s. Basically, the Gauss Cannon consists of a system of powerful electromagnets which fires a projectile by accelerating between a number of metal rails. Gauss Cannons fire high-density metal projectiles at extremely high velocity. <br><br>This weapon is so powerful when fired that it creates a sonic boom which is heard for miles, and the crew near the weapon must take special precautions due to the massive concussion effects generated.';
$lang['info'][405]['name'] = "Ion Cannon";
$lang['info'][405]['description'] = "An ion cannon is a weapon that fires beams of ions (positively or negatively charged particles). The Ion Cannon is actually a type of Particle Cannon; only the particles used are ionized. Due to their electrical charges, they also have the potential to disable electronic devices, and anything else that has an electrical or similar power source, using a phenomena known as the the Electromagnetic Pulse (EMP effect). Due to the cannons highly improved shielding system, this cannon provides improved protection for your larger, more destructive defense weapons.<br><br> After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][406]['name'] = "Plasma Turret";
$lang['info'][406]['description'] = "One of the most advanced defense weapons systems ever developed, the Plasma Turret uses a large nuclear reactor fuel cell to power an electromagnetic accelerator that fires a pulse, or toroid, of plasma. During operation, the Plasma turret first locks on a target and begins the process of firing. A plasma sphere is created in the turrets core by super heating and compressing gases, stripping them of their ions. Once the gas is superheated, compressed, and a plasma sphere is created, it is then loaded into the electromagnetic accelerator which is energized. Once fully energized, the accelerator is activated, which results in the plasma sphere being launched at an extremely high rate of speed to the intended target. From the targets perspective, the approaching bluish ball of plasma is impressive, but once it strikes, it causes instant destruction.<br><br> Defensive facilities deactivate as soon as they are too heavily damaged. After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][407]['name'] = "Small Shield Dome";
$lang['info'][407]['description'] = "Colonizing new worlds brought about a new danger, space debris. A large asteroid could easily wipe out the world and all inhabitants. Advancements in shielding technology provided scientists with a way to develop a shield to protect an entire planet not only from space debris but, as it was learned, from an enemy attack. By creating a large electromagnetic field around the planet, space debris that would normally have destroyed the planet was deflected, and attacks from enemy Empires were thwarted. The first generators were large and the shield provided moderate protection, but it was later discovered that small shields did not afford the protection from larger scale attacks. The small shield dome was the prelude to a stronger, more advanced planetary shielding system to come.<br><br> After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][408]['name'] = "Large Shield Dome";
$lang['info'][408]['description'] = "The Large Shield Dome is the next step in the advancement of planetary shields, it is the result of years of work improving the Small Shield Dome. Built to withstand a larger barrage of enemy fire by providing a higher energized electromagnetic field, large domes provide a longer period of protection before collapsing.<br><br> After a battle, there is up to a 70 % chance that failed defensive facilities can be returned to use.";
$lang['info'][502]['name'] = "Anti-Ballistic Missiles";
$lang['info'][502]['description'] = 'Anti Ballistic Missiles (ABM) are your only line of defence when attacked by Interplanetary Missiles (IPM) on your planet or moon. When a launch of IPMs is detected, these missiles automatically arm, process a launch code in their flight computers, target the inbound IPM, and launch to intercept. During the flight, the target IPM is constantly tracked and course corrections are applied until the ABM reaches the target and destroys the attacking IPM. Each ABM destroys one incoming IPM.';
$lang['info'][503]['name'] = "Interplanetary Missiles";
$lang['info'][503]['description'] = 'Interplanetary Missiles (IPM) are your offensive weapon to destroy the defences of your target. Using state of the art tracking technology, each missile targets a certain number of defences for destruction. Tipped with an anti-matter bomb, they deliver a destructive force so severe that destroyed shields and defences cannot be repaired. The only way to counter these missiles is with ABMs.';

//----------------------------------------------------------------------------//
//STATISTICS
$lang['st_player'] = 'Player';
$lang['st_alliance'] = 'Alliance';
$lang['st_points'] = 'Points';
$lang['st_fleets'] = 'Fleet';
$lang['st_researh'] = 'Research';
$lang['st_buildings'] = 'Building';
$lang['st_defenses'] = 'Defense';
$lang['st_position'] = 'Rank';
$lang['st_members'] = 'Member';
$lang['st_per_member'] = 'Per Member';
$lang['st_statistics'] = 'Statistics';
$lang['st_updated'] = 'Updated';
$lang['st_show'] = 'show';
$lang['st_per'] = 'by';
$lang['st_in_the_positions'] = 'in ranks';
$lang['st_actions'] = 'Actions';
$lang['st_ally_request'] = 'Send request to this alliance';

//----------------------------------------------------------------------------//
//SYSTEM
$lang['sys_attacker_lostunits'] = "The attacker has lost a total of";
$lang['sys_defender_lostunits'] = "The defender has lost a total of";
$lang['sys_units'] = "units";
$lang['debree_field_1'] = "A debris field";
$lang['debree_field_2'] = "floating in the orbit of the planet.";
$lang['sys_gcdrunits'] = "At these space coordinates now float %s %s and %s %s.";
$lang['sys_moonproba'] = "The probability that a moon emerge from the rubble is:";
$lang['sys_moonbuilt'] = "The huge amount of metal and glass are functioning and form a lunar satellite in orbit the planet %s [%d:%d:%d]!";
$lang['sys_attack_title'] = "Fleets clash in ";
$lang['sys_attack_round'] = "Round";
$lang['sys_attack_attacker_pos'] = "Attacker";
$lang['sys_attack_techologies'] = "Weapons: %d %% Shield: %d %% Armor: %d %% ";
$lang['sys_attack_defender_pos'] = "Defender";
$lang['sys_ship_type'] = "Type";
$lang['sys_ship_count'] = "Amount";
$lang['sys_ship_weapon'] = "Weapons";
$lang['sys_ship_shield'] = "Shield";
$lang['sys_ship_armour'] = "Armor";
$lang['sys_destroyed'] = "Destroyed";
$lang['fleet_attack_1'] = "The attacking fleet fires a total force of";
$lang['fleet_attack_2'] = "on the defender. The defender's shields absorb";
$lang['fleet_defs_1'] = "The defending fleet fires a total force of";
$lang['fleet_defs_2'] = "on the attacker. The attacker's shields absorb";
$lang['damage'] = "points of damage,";
$lang['with'] = 'with';
$lang['shots'] = 'shots';
$lang['sys_attacker_won'] = "The attacker has won the battle";
$lang['sys_defender_won'] = "The defender has won the battle";
$lang['sys_both_won'] = "The battle ended in a draw";
$lang['sys_stealed_ressources'] = "obtaining";
$lang['sys_and'] = "and";
$lang['sys_mess_tower'] = "Control Tower";
$lang['sys_mess_attack_report'] = "Battle Report";
$lang['sys_spy_maretials'] = "Resources";
$lang['sys_spy_fleet'] = "Fleet";
$lang['sys_spy_defenses'] = "Defense";
$lang['sys_mess_qg'] = "Headquarters";
$lang['sys_mess_spy_report'] = "Report espionage";
$lang['sys_mess_spy_lostproba'] = "Probability of detection of the fleet of spy : %d %% ";
$lang['sys_mess_spy_control'] = "Space Control";
$lang['sys_mess_spy_activity'] = "Espionage activity";
$lang['sys_mess_spy_ennemyfleet'] = "An enemy fleet on the planet";
$lang['sys_mess_spy_seen_at'] = "was seen near your planet";
$lang['sys_mess_spy_destroyed'] = "Your fleet has been destroyed espionage";
$lang['sys_stay_mess_stay'] = "Parking Fleet";
$lang['sys_stay_mess_start'] = "your fleet arrives on the planet";
$lang['sys_stay_mess_end'] = " and offers the following resources : ";
$lang['sys_adress_planet'] = "[%s:%s:%s]";
$lang['sys_stay_mess_goods'] = "%s : %s, %s : %s, %s : %s";
$lang['sys_colo_mess_from'] = "Colonization";
$lang['sys_colo_mess_report'] = "Report of settlement";
$lang['sys_colo_defaultname'] = "Colony";
$lang['sys_colo_arrival'] = "The settlers arrived at the coordinates ";
$lang['sys_colo_maxcolo'] = ", but, unfortunately, can not colonize, can have no more ";
$lang['sys_colo_allisok'] = ", 	the settlers are beginning to build a new colony.";
$lang['sys_colo_notfree'] = ", 	settlers would not have found a planet with these details. They are forced to turn back completely demoralized ...";
$lang['sys_colo_astro_level'] = ", but is not possible because you do not have the required level of astrophysics.";
$lang['sys_colo_planet'] = " planet ";
$lang['sys_expe_report'] = "Report of expedition";
$lang['sys_recy_report'] = "Recycling Report";
$lang['sys_expe_blackholl_1'] = "The fleet was sucked into a black hole is partially destroyed.";
$lang['sys_expe_blackholl_2'] = "The fleet was sucked into a black hole, and was completely destroyed!";
$lang['sys_expe_nothing_1'] = "Your exploration nearly ran into a neutron stars gravitation field and needed some time to free itself. Because of that a lot of Deuterium was consumed and the expedition fleet had to come back without any results.";
$lang['sys_expe_nothing_2'] = "Besides some quaint, small pets from a unknown mash planet, this expedition brings nothing thrilling back from the trip.";
$lang['sys_expe_found_goods'] = "The fleet has discovered an unmanned spacecraft! <br> His scouts have recovered %s de %s, %s de %s, %s de %s y %s de %s.";
$lang['sys_expe_found_ships'] = "Your expedition ran into the shipyards of a colony that was deserted eons ago. In the shipyards hangar they discover some ships that could be salvaged. The technicians are trying to get some of them to fly again.<br><br>The following ships are now part of the fleet:";
$lang['sys_expe_back_home'] = "Your expedition returned to the hangar.";
$lang['sys_mess_transport'] = "Transport Fleet";
$lang['sys_tran_mess_owner'] = "Your fleet has reached the planet %s %s and delivered its goods:<br>%s: %s %s: %s %s: %s.";
$lang['sys_tran_mess_user'] = "Your fleet is returning from planet %s %s.<br><br>The fleet is delivering %s %s, %s %s and %s %s.";
$lang['sys_mess_fleetback'] = "Return of the fleet";
$lang['sys_tran_mess_back'] = "A fleet back to planet % s% s. The fleet does not give resources.";
$lang['sys_recy_gotten'] = "Your fleet arrived at the coordinates indicated and gatherers %s units %s and %s units of %s.";
$lang['sys_gain'] = "Benefits";
$lang['sys_fleet_won'] = "Your fleet is returning from planet %s %s.<br><br>The fleet is delivering %s %s, %s %s and %s %s.";
$lang['sys_perte_attaquant'] = "Forward Party";
$lang['sys_perte_defenseur'] = "Part Defender";
$lang['sys_debris'] = "Debris";
$lang['sys_destruc_title'] = "Probability of kill moon %s :";
$lang['sys_mess_destruc_report'] = "Destruction Report";
$lang['sys_destruc_lune'] = "The probability of destroying the moon is: %d %% ";
$lang['sys_destruc_rip'] = "The probability that the stars of death are destroyed is: %d %% ";
$lang['sys_destruc_stop'] = "The defender has failed to stop the destruction of the moon";
$lang['sys_destruc_mess1'] = "The Death Stars shoot the graviton over the moon orbit";
$lang['sys_destruc_mess'] = "A fleet from planet %s [%d:%d:%d] goes to planet moon [%d:%d:%d]";
$lang['sys_destruc_moon'] = ". The tremors began to shake the surface of the moon, after a few moments the moon couldn't longer resist and blows into thousand of pieces, mission accomplished, the fleet returns to the planet of origin.";
$lang['sys_destruc_ds'] = ". The tremors began to shake the surface of the moon, but something goes wrong, the Death Stars graviton also causes tremors making the Death Stars blow into thousand of pieces.";
$lang['sys_destruc_none'] = ". The Death Stars didn't generate enough power, the mission failed and the ships returned.";
$lang['sys_the'] = " the ";
$lang['sys_stay_mess_back'] = "One of your fleet return from ";
$lang['sys_stay_mess_bend'] = " and offers ";
$lang['sys_missile_attack'] = 'Missile Attack';
$lang['sys_all_destroyed'] = 'All interplanetary missiles has been destroyed by interceptor missiles.';
$lang['sys_planet_without_defenses'] = 'Planet without defenses';
$lang['sys_some_destroyed'] = ' has been destroyed by interceptor missiles.';
$lang['sys_missile_string'] = 'Missiles attack (%1%) from %2% arrive to planet %3% <br><br>';

//----------------------------------------------------------------------------//
//class.CheckSession.php
$lang['css_account_banned_message'] = 'YOUR ACCOUNT HAS BEEN SUSPENDED';
$lang['css_account_banned_expire'] = 'Expiration:';

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
$lang['fcp_colony'] = 'Colony';
$lang['fgp_require'] = 'Requires: ';
$lang['fgf_time'] = 'Construction Time: ';

//----------------------------------------------------------------------------//
// CombatReport.php
$lang['cr_lost_contact'] = 'Contact was lost with the attacking fleet.';
$lang['cr_first_round'] = '(The fleet was destroyed in the first round)';
$lang['cr_type'] = 'Type';
$lang['cr_total'] = 'Total';
$lang['cr_weapons'] = 'Weapons';
$lang['cr_shields'] = 'Shields';
$lang['cr_armor'] = 'Armor';
$lang['cr_destroyed'] = 'Destroyed!';
$lang['cr_no_access'] = 'The requested report doesn\'t exists';

/* end of INGAME.php */
