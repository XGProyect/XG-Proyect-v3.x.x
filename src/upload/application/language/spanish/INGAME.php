<?php
$lang = [
    // buildings
    'building_metal_mine' => "Mina de metal",
    'building_crystal_mine' => "Mina de cristal",
    'building_deuterium_sintetizer' => "Sintetizador de deuterio",
    'building_solar_plant' => "Planta de energía solar",
    'building_fusion_reactor' => "Planta de fusión",
    'building_robot_factory' => "Fábrica de Robots",
    'building_nano_factory' => "Fábrica de Nanobots",
    'building_hangar' => "Hangar",
    'building_metal_store' => "Almacén de Metal",
    'building_crystal_store' => "Almacén de Cristal",
    'building_deuterium_tank' => "Contenedor de deuterio",
    'building_laboratory' => "Laboratorio de investigación",
    'building_terraformer' => "Terraformer",
    'building_ally_deposit' => "Depósito de la Alianza",
    'building_mondbasis' => "Base lunar",
    'building_phalanx' => "Sensor Phalanx",
    'building_jump_gate' => "Salto cuántico",
    'building_missile_silo' => "Silo",

    // defenses
    'defense_rocket_launcher' => 'Lanzamisiles',
    'defense_light_laser' => 'Láser pequeño',
    'defense_heavy_laser' => 'Láser grande',
    'defense_gauss_cannon' => 'Cañón Gauss',
    'defense_ion_cannon' => 'Cañón iónico',
    'defense_plasma_turret' => 'Cañón de plasma',
    'defense_small_shield_dome' => 'Cúpula pequeña de protección',
    'defense_large_shield_dome' => 'Cúpula grande de protección',
    'defense_anti-ballistic_missile' => 'Misiles antibalísticos',
    'defense_interplanetary_missile' => 'Misil interplanetario',

    // ships
    'ship_small_cargo_ship' => 'Nave pequeña de carga',
    'ship_big_cargo_ship' => 'Nave grande de carga',
    'ship_light_fighter' => 'Cazador ligero',
    'ship_heavy_fighter' => 'Cazador pesado',
    'ship_cruiser' => 'Crucero',
    'ship_battleship' => 'Nave de batalla',
    'ship_colony_ship' => 'Colonizador',
    'ship_recycler' => 'Reciclador',
    'ship_espionage_probe' => 'Sonda de espionaje',
    'ship_bomber' => 'Bombardero',
    'ship_solar_satellite' => 'Satélite solar',
    'ship_destroyer' => 'Destructor',
    'ship_deathstar' => 'Estrella de la muerte',
    'ship_battlecruiser' => 'Acorazado',

    // research
    "research_espionage_technology" => 'Tecnología de espionaje"',
    "research_computer_technology" => 'Tecnología de computación',
    "research_weapons_technology" => 'Tecnología militar',
    "research_shielding_technology" => 'Tecnología de defensa',
    "research_armour_technology" => 'Tecnología de blindaje',
    "research_energy_technology" => 'Tecnología de energía',
    "research_hyperspace_technology" => 'Tecnología de hiperespacio',
    "research_combustion_drive" => 'Motor de combustión',
    "research_impulse_drive" => 'Motor de impulso',
    "research_hyperspace_drive" => 'Propulsor hiperespacial',
    "research_laser_technology" => 'Tecnología láser',
    "research_ionic_technology" => 'Tecnología iónica',
    "research_plasma_technology" => 'Tecnología de plasma',
    "research_intergalactic_research_network" => 'Red de investigación intergaláctica',
    "research_astrophysics" => 'Astrofísica',
    "research_graviton_technology" => 'Tecnología de gravitón',

    // types of planets or galaxy objects
    'planet_type' => [
        1 => 'Planeta',
        2 => 'Escombros',
        3 => 'Luna',
    ],

    // type of structures
    'construction' => 'Construcción',
    'research' => 'Investigación',
    'ships' => 'Naves',
    'missiles' => 'Misiles',
    'defenses' => 'Defensa',

    // system
    'sys_building_queue_build_order' => 'Orden de construcción',
    'sys_building_queue_destroy_order' => 'Orden de destrucción',
    'sys_building_queue_not_enough_resources' => 'La %s de tu %s de nivel %s en %s no pudo ejecutarse.<br><br>Recursos insuficientes: %s.',
    'sys_building_queue_not_enough_resources_from' => 'Mensaje del sistema',
    'sys_building_queue_not_enough_resources_subject' => 'Producción cancelada',

    // topnav
    'tn_commander' => 'Comandante',
    'tn_admiral' => 'Almirante',
    'tn_engineer' => 'Ingeniero',
    'tn_geologist' => 'Geólogo',
    'tn_technocrat' => 'Tecnócrata',
    'tn_add_admiral' => '+2 huecos de flota máximos',
    'tn_add_engineer' => 'Minimiza las perdidas de las defensas a la mitad,<br/>+10% de producción de energía',
    'tn_add_geologist' => '+10% producción de minas',
    'tn_add_technocrat' => '+2 al nivel de espionaje,<br/>25% menos tiempo de investigación',
    'tn_get_now' => '¡Consíguelo ahora!',
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
$lang['Crystal'] = 'Cristal';
$lang['Deuterium'] = 'Deuterio';
$lang['Darkmatter'] = 'Materia Oscura';
$lang['Energy'] = 'Energía';
$lang['Messages'] = 'Mensajes';
$lang['write_message'] = 'Escribir mensaje';
$lang['ge_home_planet'] = 'Planeta Principal';
$lang['ge_colony'] = 'Colonia';

$lang['online'] = 'Conectado';
$lang['minutes'] = '15 min';
$lang['offline'] = 'Desconectado';

$lang['type_mission'][1] = 'Atacar';
$lang['type_mission'][2] = 'Ataq. confederación';
$lang['type_mission'][3] = 'Transportar';
$lang['type_mission'][4] = 'Desplegar';
$lang['type_mission'][5] = 'Mantener posición';
$lang['type_mission'][6] = 'Espiar';
$lang['type_mission'][7] = 'Colonizar';
$lang['type_mission'][8] = 'Reciclar campo de escombros';
$lang['type_mission'][9] = 'Destruir';
$lang['type_mission'][15] = 'Expedición';

$lang['user_level'] = array(
    '0' => 'Jugador',
    '1' => 'Moderador',
    '2' => 'Operador',
    '3' => 'Administrador',
);

//SHORT NAMES FOR COMBAT REPORTS
$lang['tech_rc'] = array(
    202 => "P. carga",
    203 => "G. carga",
    204 => "C. ligero",
    205 => "C. pesado",
    206 => "Crucero",
    207 => "N. Batalla",
    208 => "Colon.",
    209 => "Recic.",
    210 => "Sondas",
    211 => "Bombarderos",
    212 => "Satélites",
    213 => "Destructor",
    214 => "E. Muerte",
    215 => "Acorazado",
    401 => "L. misiles",
    402 => "L. pequeño",
    403 => "L. grande",
    404 => "C. Gauss",
    405 => "C. iónico",
    406 => "C. de plasma",
    407 => "C. pequeña",
    408 => "C. grande",
);

//----------------------------------------------------------------------------//
//TOPNAV
$lang['tn_vacation_mode'] = 'Modo vacaciones activo hasta el ';
$lang['tn_vacation_mode_active'] = "Modo vacaciones activo";
$lang['tn_delete_mode'] = 'Tu cuenta se encuentra en modo borrar. La misma será borrada el ';

//----------------------------------------------------------------------------//
//LEFT MENU
$lang['lm_overview'] = 'Resumen';
$lang['lm_galaxy'] = 'Galaxia';
$lang['lm_empire'] = 'Imperio';
$lang['lm_fleet'] = 'Flota';
$lang['lm_movement'] = 'Movimientos de flota';
$lang['lm_resources'] = 'Recursos';
$lang['lm_resources_settings'] = 'Opciones de recursos';
$lang['lm_station'] = 'Instalaciones';
$lang['lm_research'] = 'Investigación';
$lang['lm_shipyard'] = 'Hangar';
$lang['lm_defenses'] = 'Defensa';
$lang['lm_officiers'] = 'Casino';
$lang['lm_trader'] = 'Mercader';
$lang['lm_technology'] = 'Técnica';
$lang['lm_messages'] = 'Mensajes';
$lang['lm_alliance'] = 'Alianza';
$lang['lm_buddylist'] = 'Amigos';
$lang['lm_notes'] = 'Notas';
$lang['lm_statistics'] = 'Clasificación';
$lang['lm_search'] = 'Búsqueda';
$lang['lm_options'] = 'Opciones';
$lang['lm_forums'] = 'Foro';
$lang['lm_logout'] = 'Salir';
$lang['lm_administration'] = 'Administración';
$lang['lm_module_not_accesible'] = 'No se ha encontrado la página.';

//----------------------------------------------------------------------------//
//BUILDINGS - RESEARCH - SHIPYARD - DEFENSES
$lang['bd_dismantle'] = 'Desmontar';
$lang['bd_interrupt'] = 'Interrumpir';
$lang['bd_cancel'] = 'Cancelar';
$lang['bd_working'] = 'Trabajando';
$lang['bd_build'] = 'Mejorar';
$lang['bd_add_to_list'] = 'En cola';
$lang['bd_no_more_fields'] = 'No hay espacio en el planeta';
$lang['bd_remaining'] = 'Restantes';
$lang['bd_lab_required'] = '¡Es necesario construir primero un laboratorio de investigación en este planeta!';
$lang['bd_building_lab'] = 'No se puede investigar cuando se esta ampliando el laboratorio';
$lang['bd_lvl'] = 'Nivel';
$lang['bd_spy'] = ' espia';
$lang['bd_commander'] = ' comandante';
$lang['bd_research'] = 'Investigar';
$lang['bd_shipyard_required'] = '¡Debes construir un hangar en este planeta para continuar!';
$lang['bd_building_shipyard'] = 'No puedes fabricar durante la ampliación del hangar, fábrica de robots o nanobots';
$lang['bd_available'] = 'Disponible: ';
$lang['bd_build_ships'] = 'Construir';
$lang['bd_protection_shield_only_one'] = '¡El escudo de protección sólo se puede construir una vez!';
$lang['bd_actual_production'] = 'Producción actual:';
$lang['bd_completed'] = 'Completado';
$lang['bd_operating'] = '(En funcionamiento)';
$lang['bd_continue'] = 'Continuar';
$lang['bd_ready'] = 'Listo';
$lang['bd_finished'] = 'Terminado';
$lang['bd_from'] = 'de<br />';

//----------------------------------------------------------------------------//
//TECHTREE
$lang['tt_requirements'] = 'Requisitos';
$lang['tt_lvl'] = 'nivel ';

$lang['tech'] = array(
    0 => "Construcción",
    1 => "Mina de metal",
    2 => "Mina de cristal",
    3 => "Sintetizador de deuterio",
    4 => "Planta de energía solar",
    12 => "Planta de fusión",
    14 => "Fábrica de Robots",
    15 => "Fábrica de Nanobots",
    21 => "Hangar",
    22 => "Almacén de Metal",
    23 => "Almacén de Cristal",
    24 => "Contenedor de deuterio",
    31 => "Laboratorio de investigación",
    33 => "Terraformer",
    34 => "Depósito de la Alianza",
    40 => "Construcciones especiales",
    41 => "Base lunar",
    42 => "Sensor Phalanx",
    43 => "Salto cuántico",
    44 => "Silo",
    100 => "Investigación",
    106 => "Tecnología de espionaje",
    108 => "Tecnología de computación",
    109 => "Tecnología militar",
    110 => "Tecnología de defensa",
    111 => "Tecnología de blindaje",
    113 => "Tecnología de energía",
    114 => "Tecnología de hiperespacio",
    115 => "Motor de combustión",
    117 => "Motor de impulso",
    118 => "Propulsor hiperespacial",
    120 => "Tecnología láser",
    121 => "Tecnología iónica",
    122 => "Tecnología de plasma",
    123 => "Red de investigación intergaláctica",
    124 => 'Astrofísica',
    199 => "Tecnología de gravitón",
    200 => "Naves",
    202 => "Nave pequeña de carga",
    203 => "Nave grande de carga",
    204 => "Cazador ligero",
    205 => "Cazador pesado",
    206 => "Crucero",
    207 => "Nave de batalla",
    208 => "Colonizador",
    209 => "Reciclador",
    210 => "Sonda de espionaje",
    211 => "Bombardero",
    212 => "Satélite solar",
    213 => "Destructor",
    214 => "Estrella de la muerte",
    215 => "Acorazado",
    400 => "Defensa",
    401 => "Lanzamisiles",
    402 => "Láser pequeño",
    403 => "Láser grande",
    404 => "Cañón Gauss",
    405 => "Cañón iónico",
    406 => "Cañón de plasma",
    407 => "Cúpula pequeña de protección",
    408 => "Cúpula grande de protección",
    502 => "Misiles antibalísticos",
    503 => "Misil interplanetario",
);

$lang['res']['descriptions'] = array(
    1 => "Las minas de metal proveen los recursos básicos de un imperio emergente, y permiten la construcción de edificios y naves.",
    2 => "Los cristales son el recurso principal usado para construir circuitos electrónicos y ciertas aleaciones.",
    3 => "El deuterio se usa como combustible para naves, y se recolecta en el mar profundo. Es una sustancia muy escasa, y por ello, relativamente cara.",
    4 => "Las plantas de energía solar convierten energía fotónica en energía eléctrica, para su uso en casi todos los edificios y estructuras.",
    12 => "Un reactor de fusión nuclear que produce un átomo de helio a partir de dos átomos de deuterio usando una presión extremadamente alta y una elevadísima temperatura.",
    14 => "Las fábricas de robots proporcionan unidades baratas y de fácil construcción que pueden ser usadas para mejorar o construir cualquier estructura planetaria. Cada nivel de mejora de la fábrica aumenta la eficiencia y el numero de unidades robóticas que ayudan en la construcción.",
    15 => "La fábrica de nanobots es la última evolución de la robótica. Cada mejora proporciona nanobots más y más eficientes que incrementan la velocidad de construcción.",
    21 => "El hangar es el lugar donde se construyen naves y estructuras de defensa planetaria.",
    22 => "Almacén de metal sin procesar.",
    23 => "Almacén de cristal sin procesar.",
    24 => "Contenedores enormes para almacenar deuterio.",
    31 => "Se necesita un laboratorio de investigación para conducir la investigación en nuevas tecnologías.",
    33 => "El Terraformer es necesario para habilitar áreas inaccesibles de tu planeta para edificar infraestructuras.",
    34 => "El depósito de la alianza ofrece la posibilidad de repostar a las flotas aliadas que estén estacionadas en la órbita ayudando a defender.",
    41 => "Dado que la luna no tiene atmósfera, se necesita una base lunar para generar espacio habitable.",
    42 => "Usando el sensor phalanx, las flotas de otros imperios pueden ser descubiertas y observadas. Cuanto mayor sea la cadena de sensores phalanx, mayor el rango que pueda escanear.",
    43 => "El saltos cuánticos son transmisores enormes que son capaces de enviar grandes flotas a través del universo sin perdida de tiempo.",
    44 => "El silo es un lugar de almacenamiento y lanzamiento de misiles planetarios.",
    106 => "Usando esta tecnología, puede obtenerse información sobre otros planetas.",
    108 => "Cuanto más elevado sea el nivel de tecnología de computación, más flotas podrás controlar simultaneamente. Cada nivel adicional de esta tecnologia, aumenta el numero de flotas en 1.",
    109 => "Este tipo de tecnología incrementa la eficiencia de tus sistemas de armamento. Cada mejora de la tecnología militar añade un 10% de potencia a la base de daño de cualquier arma disponible.",
    110 => "La tecnología de defensa se usa para generar un escudo de partículas protectoras alrededor de tus estructuras.
Cada nivel de esta tecnología aumenta el escudo efectivo en un 10% (basado en el nivel de una estructura dada).",
    111 => "Las aleaciones altamente sofisticadas ayudan a incrementar el blindaje de una nave añadiendo el 10% de su fuerza en cada nivel a la fuerza base.",
    113 => "Entendiendo la tecnología de diferentes tipos de energía, muchas investigaciones nuevas y avanzadas pueden ser adaptadas. La tecnología de energía es de gran importancia para un laboratorio de investigación moderno.",
    114 => "Incorporando la cuarta y quinta dimensión en la tecnología de propulsión, se puede disponer de un nuevo tipo de motor; que es más eficiente y usa menos combustible que los convencionales.",
    115 => "Ejecutar investigaciones en esta tecnología proporciona motores de combustión siempre más rapido, aunque cada nivel aumenta solamente la velocidad en un 10% de la velocidad base de una nave dada.",
    117 => "El sistema del motor de impulso se basa en el principio de la repulsión de partículas. La materia repelida es basura generada por el reactor de fusión usado para proporcionar la energía necesaria para este tipo de motor de propulsión.",
    118 => "Los motores de hiperespacio permiten entrar al mismo a través de una ventana hiperespacial para reducir drásticamente el tiempo de viaje. El hiperespacio es un espacio alternativo con más de 3 dimensiones.",
    120 => "La Tecnología láser es un importante conocimiento; conduce a la luz monocromática firmemente enfocada sobre un objetivo. El daño puede ser ligero o moderado dependiendo de la potencia del rayo...",
    121 => "La Tecnología iónica enfoca un rayo de iones acelerados en un objetivo, lo que puede provocar un gran daño debido a su naturaleza de electrones cargados de energía.",
    122 => "Las armas de plasma son incluso más peligrosas que cualquier otro sistema de armamento conocido, debido a la naturaleza agresiva del plasma.",
    123 => "Los científicos de tus planetas pueden comunicarse entre ellos a través de esta red.",
    124 => "Las naves pueden realizar expediciones largas con el modulo de investigación. Cada segundo nivel de esta tecnología permitirá colonizar un planeta adicional.",
    199 => "A través del disparo de partículas concentradas de gravitón se genera un campo gravitacional artificial con suficiente potencia y poder de atracción para destruir no solo naves, sino lunas enteras.",
    202 => "Las naves pequeñas de carga son naves muy ágiles usadas para transportar recursos desde un planeta a otro",
    203 => "La nave grande de carga es una versión avanzada de las naves pequeñas de carga, permitiendo así una mayor capacidad de almacenamiento y velocidades más altas gracias a un mejor sistema de propulsión.",
    204 => "El cazador ligero es una nave maniobrable que puedes encontrar en casi cualquier planeta. El coste no es particularmente alto, pero asimismo el escudo y la capacidad de carga son muy bajas.",
    205 => "El cazador pesado es la evolución logica del ligero, ofreciendo escudos reforzados y una mayor potencia de ataque.",
    206 => "Los cruceros de combate tienen un escudo casi tres veces más fuerte que el de los cazadores pesados y más del doble de potencia de ataque. Su velocidad de desplazamiento está también entre las más rápidas jamás vista. ",
    207 => "Las naves de batalla son la espina dorsal de cualquier flota militar. Blindaje pesado, potentes sistemas de armamento y una alta velocidad de viaje, así como una gran capacidad de carga hace de esta nave un duro rival contra el que luchar.",
    208 => "Esta nave proporciona lo necesario para ir a donde ningún hombre ha llegado antes y colonizar nuevos mundos.",
    209 => "Los recicladores se usan para recolectar escombros flotando en el espacio para reciclarlos en recursos útiles.",
    210 => "Las sondas de espionaje son pequeños droides no tripulados con un sistema de propulsión excepcionalmente rápido usado para espiar en planetas enemigos.",
    211 => "El Bombardero es una nave de propósito especial, desarrollado para atravesar las defensas planetarias más pesadas.",
    212 => "Los satélites solares son simples satélites en órbita equipados con células fotovoltaicas y transmisores para llevar la energía al planeta. Se transmite por este medio a la tierra usando un rayo láser especial.",
    213 => "El destructor es la nave más pesada jamás vista y posee un potencial de ataque sin precedentes.",
    214 => "No hay nada tan grande y peligroso como una estrella de la muerte aproximándose.",
    215 => "El Acorazado es una nave altamente especializada en la intercepción de flotas hostiles.",
    401 => "El lanzamisiles es un sistema de defensa sencillo, pero barato.",
    402 => "Por medio de un rayo láser concentrado, se puede provocar más daño que con las armas balísticas normales.",
    403 => "Los lásers grandes posee una mejor salida de energía y una mayor integridad estructural que los lásers pequeños.",
    404 => "Usando una inmensa aceleración electromagnética, los cañones gauss aceleran proyectiles pesados.",
    405 => "Los cañones iónicos disparan rayos de iones altamente energéticos contra su objetivo, desestabilizando los escudos y destruyendo los componentes electrónicos.",
    406 => "Los cañones de plasma liberan la energía de una pequeña erupción solar en una bala de plasma. La energía destructiva es incluso superior a la del Destructor.",
    407 => "La cúpula pequeña de protección cubre el planeta con un delgado campo protector que puede absorber inmensas cantidades de energía.",
    408 => "La cúpula grande de protección proviene de una tecnología de defensa mejorada que absorbe incluso más energía antes de colapsarse.",
    502 => "Los misiles antibalísticos destruyen los misiles interplanetarios.",
    503 => "Los misiles interplanetarios destruyen los sistemas de defensa del enemigo. Tus misiles interplanetarios tienen actualmente un alcance de %s sistemas.",
);

//----------------------------------------------------------------------------//
//INFOS
$lang['in_jump_gate_done'] = 'El salto cuántico fue realizado, el próximo salto podrá ser realizado en: ';
$lang['in_jump_gate_error_data'] = '¡Error, los datos para el salto son incorrectos!';
$lang['in_jump_gate_not_ready_target'] = 'El salto cuántico no está listo en la Luna de destino, estará listo en: ';
$lang['in_jump_gate_doesnt_have_one'] = '¡No tienes Salto cuántico en esa luna!';
$lang['in_jump_gate_already_used'] = 'El Salto cuántico fue usado, tiempo para recargar su energia: ';
$lang['in_jump_gate_available'] = 'disponible';
$lang['in_rf_again'] = 'Fuego rápido contra';
$lang['in_rf_from'] = 'Fuego rápido de';
$lang['in_level'] = 'Nivel';
$lang['in_prod_p_hour'] = 'Produción/hora';
$lang['in_difference'] = 'Diferencia';
$lang['in_used_energy'] = 'Consumo de energía';
$lang['in_prod_energy'] = 'Produción de energía';
$lang['in_used_deuter'] = 'Consumo de deuterio';
$lang['in_range'] = 'Rango de sensores';
$lang['in_title_head'] = 'Información de';
$lang['in_name'] = 'Nombre';
$lang['in_struct_pt'] = 'Puntos de Estructura';
$lang['in_shield_pt'] = 'Integridad del Escudo';
$lang['in_attack_pt'] = 'Poder de ataque';
$lang['in_capacity'] = 'Capacidad de carga';
$lang['in_units'] = 'Unidades';
$lang['in_base_speed'] = 'Velocidad base';
$lang['in_consumption'] = 'Consumo de combustible (Deuterio)';
$lang['in_jump_gate_start_moon'] = 'Luna de partida';
$lang['in_jump_gate_finish_moon'] = 'Luna de destino';
$lang['in_jump_gate_select_ships'] = 'Usar Salto Cuántico: número de naves';
$lang['in_jump_gate_jump'] = 'Saltar';
$lang['in_destroy'] = 'Destruir:';
$lang['in_needed'] = 'Necesita';
$lang['in_dest_durati'] = 'Duración de destrucción';
$lang['in_storage_capacity'] = 'Capacidad de almacenamiento';
$lang['in_max_colonies'] = 'Colonias máximas';
$lang['in_max_expeditions'] = 'Expediciones máximas';
$lang['in_astrophysics_first'] = 'Las posiciones 3 y 13 pueden ser colonizadas a partir del nivel 4.';
$lang['in_astrophysics_second'] = 'Las posiciones 2 y 14 pueden ser colonizadas a partir del nivel 6.';
$lang['in_astrophysics_third'] = 'Las posiciones 1 y 15 pueden ser colonizadas a partir del nivel 8.';

// -------------------------- MINES ------------------------------------------------------------------------------------------------------//
$lang['info'][1]['name'] = "Mina de Metal";
$lang['info'][1]['description'] = "Las minas de metal proveen los recursos básicos de un imperio emergente, y permiten la construcción de edificios y naves. El metal es el material más barato disponible y requiere poca energía para su recolección, pero se usa mucho más frecuentemente que el resto de los recursos. Se encuentra en profundidad, debajo de la superficie, lo que conduce a minas cada vez más profundas que necesitan más energía para funcionar.";
$lang['info'][2]['name'] = "Mina de Cristal";
$lang['info'][2]['description'] = "Los cristales son el recurso principal usado para construir circuitos electrónicos y ciertas aleaciones. Comparado con el proceso de producción del metal, el proceso de conversión de estructuras cristalinas en cristales industriales, requiere aproximadamente el doble de energía; por lo que los cristales son más caros al comerciar. Cada nave y edificio necesita una cierta cantidad de cristales, pero los apropiados son muy escasos y se encuentran en grandes profundidades. Las minas necesarias para recolectarlos, por ello, se vuelven más caras al alcanzar mayores profundidades, pero, ciertamente, proveen de más cristales que minas menos profundas.";
$lang['info'][3]['name'] = "Sintetizador de deuterio";
$lang['info'][3]['description'] = "El deuterio es agua pesada: los núcleos de hidrogeno contienen un neutrón adicional y es muy útil como combustible para las naves por la gran cantidad de energía liberada de la reacción entre deuterio y tritio (reacción DT). El deuterio puede ser encontrado frecuentemente en el mar profundo, debido a su peso molecular, y mejorar el sintetizador de deuterio, permite la recolección de este recurso.";

// -------------------------- ENERGY ----------------------------------------------------------------------------------------------------//
$lang['info'][4]['name'] = "Planta de Energía solar";
$lang['info'][4]['description'] = "Para proporcionar la energía necesaria para el funcionamiento de los edificios, se requieren enormes plantas de energía. Una planta solar es una forma de crear energía, puesto que utiliza semiconductores de células fotovoltaicas, que convierten los fotones en corriente eléctrica. Cuanto más se mejore la planta solar, mayor será el area para convertir la luz solar en energía y por tanto mayor será la generada. Las plantas de energía solar son la espina dorsal de cualquier infraestructura planetaria.";
$lang['info'][12]['name'] = "Planta de Fusion";
$lang['info'][12]['description'] = "En una planta de energía de fusión, los núcleos de hidrógeno son fusionados en núcleos de helio bajo una enorme temperatura y presión, despidiendo tremendas cantidades de energía. Por cada gramo de Deuterio consumido se pueden producir hasta 41,32*10^-13 Julios de energía; con 1 gramo eres capaz de producir 172MWh de energía.
<br><br>
Mayores complejos de reactores usan más deuterio y pueden producir más energía por hora. El efecto de energía puede ser aumentado investigando la tecnología de energía.
<br><br>

La producción de energía de las plantas de fusión se calcula de la siguiente forma:<br><br>
30 * [Nivel de la Planta de Fusión] * (1,05 + [Nivel de la Tecnología de Energía] * 0,01) ^ [Nivel de la Planta de Fusión]";

// -------------------------- BUILDINGS ----------------------------------------------------------------------------------------------------//
$lang['info'][14]['name'] = "Fábrica de Robots";
$lang['info'][14]['description'] = "Las fábricas de robots proporcionan unidades baratas y de fácil construcción que pueden ser usadas para mejorar o construir cualquier estructura planetaria. Cada nivel de mejora de la fábrica aumenta la eficiencia y el numero de unidades robóticas que ayudan en la construcción.";
$lang['info'][15]['name'] = "Fábrica de Nanobots";
$lang['info'][15]['description'] = "Los nanobots son realmente unidades robóticas minúsculas, con un tamaño medio de apenas unos pocos nanómetros. Estos microbios mecánicos son conectados en red y programados para una tarea de construcción, ofrecen una velocidad de producción anteriormente desconocida. Los nanobots operan en niveles moleculares, y son inmensamente útiles para construir naves, puesto que permanecen como parte de su estructura y de esta forma sus capacidades de reparación pueden ser usadas para el control de daño y reparar lo que fuera necesario si consiguen suficiente energía y recursos.";
$lang['info'][21]['name'] = "Hangar";
$lang['info'][21]['description'] = "El hangar planetario es responsable de la construcción de naves espaciales y sistemas de defensa. Según va aumentando, puede producir una mayor variedad de naves a velocidades más altas. Si además existe una fábrica de nanobots en el planeta, la velocidad a la que se completan las unidades, aumenta considerablemente.";
$lang['info'][22]['name'] = "Almacén de metal";
$lang['info'][22]['description'] = "Bodegas enormes para almacenar metal sin procesar. Mientras más grande sea el almacén, más aumentará la capacidad de almacenaje del planeta. La recolección de metal se detendrá cuando el almacén esté lleno.";
$lang['info'][23]['name'] = "Almacén de cristal";
$lang['info'][23]['description'] = "Bodegas enormes para almacenar cristal sin procesar. Mientras más grande sea el almacén, más aumentará la capacidad de almacenaje. La recolección de cristal se detendrá cuando el almacén esté lleno.";
$lang['info'][24]['name'] = "Contenedor de deuterio";
$lang['info'][24]['description'] = "Contenedores enormes para almacenar deuterio. Los contenedores se encuentran a menudo cerca del hangar. Los contenedores grandes son capaces de almacenar más deuterio. La recolección de deuterio se detendrá cuando el contenedor esté lleno.";
$lang['info'][31]['name'] = "Laboratorio de investigación";
$lang['info'][31]['description'] = "Para poder investigar en nuevas áreas de una tecnología, se necesita un laboratorio de investigación planetario. El nivel de mejoras de ese laboratorio, no solo incrementa la velocidad a la que se descubren nuevas tecnologías, sino que también abre nuevos campos para investigar. Para conducir una investigación en el menor tiempo posible, todos los científicos del imperio son enviados al planeta donde se inició el trabajo de investigación. En cuanto el trabajo se haya completado, volverán a sus planetas y llevarán con ellos la nueva tecnología descubierta. De este modo, el conocimiento sobre nuevas tecnologías puede ser fácilmente divulgado a través del imperio.";
$lang['info'][33]['name'] = "Terraformer";
$lang['info'][33]['description'] = "La pregunta sobre cómo disponer de más espacio para las estructuras en los planetas surgió durante el proceso de crecimiento de las infraestructuras de los mismos a través de las galaxias. Los métodos de construcción e ingenería tradicional eran insuficientes debido a la enorme necesidad de espacio edificable.
Un pequeño grupo de físicos de alta energía y nanotécnicos finalmente encontraron una solución: el Terraforming.<br>
Usando grandes cantidades de energía se pueden hacer incluso continentes enteros. En este edificio se producen nanobots diseñados especialmente para asegurar la calidad y usabilidad de las areas formadas.
<br><br>
Una vez construido, el terraformer no puede ser desmontado.";
$lang['info'][34]['name'] = "Depósito de la Alianza";
$lang['info'][34]['description'] = "El depósito de la alianza ofrece la posibilidad de repostar a las flotas aliadas que estén estacionadas en la órbita ayudando a defender. Cada mejora del depósito de alianza permite proveer de 10.000 unidades adicionales de deuterio, por hora, a las flotas estacionadas en la órbita.";
$lang['info'][41]['name'] = "Base Lunar";
$lang['info'][41]['description'] = "Dado que la luna no tiene atmósfera, se necesita una base lunar para generar espacio habitable. La base lunar no solo provee el oxígeno necesario, también la gravedad artificial, temperatura y protección necesarias. Cuanto más se mejore la base lunar, mayor es el área para construir estructuras. Cada nivel de la base lunar proporciona 3 campos lunares , hasta que la luna esté totalmente llena.
<br><br>
Una vez construida, la base lunar no puede ser desmontada.";
$lang['info'][42]['name'] = "Sensor Phalanx";
$lang['info'][42]['description'] = "Una cadena de sensores de alta resolución se usa para escanear un enorme espectro de frecuencia. Las unidades de proceso paralelo masivo analizan entonces las señales recibidas para detectar incluso la más mínima anomalía en la frecuencia o fortalecimiento, para detectar maniobras de flotas en imperios distantes. Debido a la complejidad del sistema, cada escaneo necesita una cantidad moderada de deuterio para proporcionar la energía necesaria.";
$lang['info'][43]['name'] = "Salto Cuantico";
$lang['info'][43]['description'] = "El Salto cuántico es un sistema de transceptores gigante capaz de enviar incluso las flotas más grandes a otro Salto cuántico en cualquier lugar del universo sin pérdida de tiempo. Este transmisor no necesita Deuterio, pero ha de pasar 1 hora entre dos saltos, de lo contrario se sobrecalentaría. Transportar recursos a través del salto no es posible. Toda la acción requiere tecnología muy desarrollada.";
$lang['info'][44]['name'] = "Silo";
$lang['info'][44]['description'] = "El silo es un lugar de almacenamiento y lanzamiento de misiles planetarios. Por cada nivel de tu silo, tienes espacio para 5 misiles interplanetarios o 10 misiles de intercepción. Es posible mezclar los tipos de misil; 1 interplanetario usa el espacio equivalente a 2 de intercepción.";

// -------------------------- TECHNOLOGY ----------------------------------------------------------------------------------------------------//
$lang['info'][106]['name'] = "Tecnología de espionaje";
$lang['info'][106]['description'] = "La tecnología de espionaje es, en primera instancia, un avance en la tecnología de sensores. Contra más avanzada está la tecnología más informarciín recibe el usuario de las actividades de su entorno. Las diferencias entre tu nivel de espionaje y el nivel de espionaje de tu oponente es crucial para las sondas. Contra más nivel de espionaje tengas más informaciín obtendrás de tus enemigos y menos lo harán ellos de ti. Además tendrás menos posibilidades que descubran tus sondas y tu tendrás más de descubrir las suyas. La tecnología de espionaje también mejora el reconocimiento de las flotas. El nivel es vital determinando esto. Desde el nivel 2 se muestra el número de naves atacantes a parte del mensaje normal, a partir del nivel 4 se muestra los tipos de naves y el total de naves atacantes y a partir del nivel 8 se muestran exactamente el numero de naves de cada tipo atacando. Esta tecnología es indispensable cuando hagamos un ataque ya que nos informa de la defensa y flota del jugador al que ataquemos. Este es el motivo por el cual esta tecnología debe ser investigada muy al principio.";
$lang['info'][108]['name'] = "Tecnología de computación";
$lang['info'][108]['description'] = "La tecnología de computación se usa para construir unidades de proceso y control de datos más potentes. Cada mejora aumenta la capacidad de proceso y el nivel de paralelismo. Cuanto más se mejore esta tecnología, más flotas pueden ser controladas al mismo tiempo. A mayor número de flotas, más actividad podrá desarrollar un imperio y generar más ingresos. Las flotas se usan para conformar grupos de naves militares, transportes de mercancías, o maniobras de espionaje. Es una buena idea incrementar constantemente la investigación en esta área para proporcionar la flexibilidad adecuada en el control de nuestras flotas.";
$lang['info'][109]['name'] = "Tecnología militar";
$lang['info'][109]['description'] = "La tecnología militar se ocupa de avanzar en el desarrollo de sistemas de armamento existentes. Principalmente está centrada en aumentar la efectividad de las armas y su eficiencia.De este modo, incrementando el nivel de esta tecnología, el mismo arma tiene más poder y provoca más daño; cada nivel aumenta la potencia en un 10% de la fuerza base del arma.Es importante mantener la tecnología militar actualizada contra tus enemigos. Es una buena idea aumentar el conocimiento en esta área continuamente.";
$lang['info'][110]['name'] = "Tecnología de defensa";
$lang['info'][110]['description'] = "La tecnología de defensa se usa para generar un escudo de partículas protectoras alrededor de tus estructuras. Cada nivel de esta tecnología aumenta el escudo efectivo en un 10% (basado en el nivel de una estructura dada). La mejora básicamente incrementa la cantidad de energía que un escudo puede absorber antes de colapsarse. Los dispositivos de escudo no se usan sólo en naves, sino también en escudos de protección planetaria.";
$lang['info'][111]['name'] = "Tecnología de blindaje";
$lang['info'][111]['description'] = "Las aleaciones complicadas son responsables del aumento del blindaje de las naves. Para una aleación que se ha demostrado ser efectiva, la estructura molecular puede ser alterada para manipular el comportamiento en situación de combate y para incorporar los últimos descubrimientos tecnológicos. Cada nivel de mejora de la tecnología de blindaje aumenta la fuerza del casco en un 10% sobre la fuerza base.";
$lang['info'][113]['name'] = "Tecnología de energía";
$lang['info'][113]['description'] = "La tecnología de energía se ocupa del conocimiento y refinamiento de las fuentes de energía, soluciones de almacenamiento, y tecnologías que proporcionan la cosa más básica hoy en día: Energía. Cuanto mayor sea el nivel desarrollado, más eficientes serán tus sistemas. Ciertos niveles de mejora son necesarios incluso para poder investigar otras tecnologías que dependen directamente del conocimiento de la energía.";
$lang['info'][114]['name'] = "Tecnología de hiperespacio";
$lang['info'][114]['description'] = "Incorporando la cuarta y quinta dimensión en la tecnología de propulsión, se puede disponer de un nuevo tipo de motor; que es más eficiente y usa menos combustible que los convencionales. Además, la tecnología de hiperespacio proporciona la base para viajes a través del hiperespacio, que son realizados por inmensas flotas de guerra así como en los saltos cuánticos. Es un nuevo y complicado tipo de tecnología, que necesita equipamiento de laboratorio y lugares de pruebas realmente caros.";
$lang['info'][115]['name'] = "Motor de combustión";
$lang['info'][115]['description'] = "Los motores de combustión pertenecen a los más antiguos en funcionamiento y se basan en la repulsión. Las partículas son aceleradas y abandonan el motor generando una fuerza de repuslión que mueve la nave en la dirección opuesta. La eficiencia de estos motores de combustión es baja, pero son de manufacturación barata y han demostrado ser fiables. Su tamaño es relativamente pequeño y no necesitan un proceso extenso de energía para controlar la operación. Investigar sobre niveles más altos proporciona siempre motores más rápidos ya que cada nivel aumenta un 10% la velocidad efectiva de viaje, basándose en la velocidad base.Dado que esta tecnología es una de las cosas más básicas para un imperio emergente, debería ser investigada tan pronto como sea posible.";
$lang['info'][117]['name'] = "Motor de impulso";
$lang['info'][117]['description'] = "El sistema del motor de impulso se basa en el principio de la repulsión de partículas. La materia repelida es basura generada por el reactor de fusión usado para proporcionar la energía necesaria para este tipo de motor de propulsión. Los motores de impulso son un desarrollo posterior a los simples motores de combustión, aumentan la eficiencia y reducen el consumo de combustible en relación a su velocidad.";
$lang['info'][118]['name'] = "Propulsor hiperespacial";
$lang['info'][118]['description'] = "A través de la curvatura del espacio-tiempo en el entorno inmediato de las naves viajantes, el espacio se comprime hasta tal grado que las distancias más grandes pueden ser cubiertas en un corto período de tiempo. Cuanto mayor sea el nivel del motor de hiperespacio, mayores serán las compresiones que se puedan obtener, y por tanto la velocidad de desplazamiento de las naves se incrementa en un 30% por cada nivel de mejora de esta investigación.Requisitos: Tecnología de hiperespacio (nivel 3) Laboratorio de investigación (nivel 7)";
$lang['info'][120]['name'] = "Tecnología láser";
$lang['info'][120]['description'] = "El láser (amplificación de luz por emisión estimulada de radiación), es un rayo de fotones monocromático coherente con excelentes capacidades de enfoque. Las unidades de láser se usan en un amplio rango de sistemas: Desde giroscopios de navegación a ordenadores ópticos o sistemas de armamento, la tecnología láser es un conocimiento fundamental para cualquier imperio. Requisitos: Laboratorio de investigación (nivel 1) Tecnología de energía (nivel 2)";
$lang['info'][121]['name'] = "Tecnología iónica";
$lang['info'][121]['description'] = "La tecnología iónica enfoca un rayo de iones acelerados en un objetivo, lo que puede provocar un gran daño debido a su naturaleza de electrones cargados de energía. Los rayos iónicos son superiores a los rayos láser, pero requieren un mayor coste de investigación. Aunque es bastante simple comparado con otras tecnologías, los rayos iónicos no son utilizados extensamente en la mayoría de planetas.";
$lang['info'][122]['name'] = "Tecnología de plasma";
$lang['info'][122]['description'] = "Las armas de plasma son incluso más peligrosas que cualquier otro sistema de armamento conocido, debido a la naturaleza agresiva del plasma. Es uno de los cuatro estados de la materia (sólido, líquido, gas, plasma), y consiste en un numero igual de partículas de gas cargadas positiva y negativamente. El plasma puede ser producido desde un gas si se añade la energía suficiente para provocar que los átomos eléctricamente neutrales se dividan en átomos cargados positivamente y electrones cargados negativamente. Esas partículas cargadas son embebidas en \"rayos\" usando tecnología magnética y pueden ser disparados.";
$lang['info'][123]['name'] = "Red de investigación intergaláctica";
$lang['info'][123]['description'] = "Los científicos de tus planetas pueden comunicarse entre ellos a través de esta red. Con cada nivel investigado, uno de tus laboratorios de investigación del nivel más alto, será enlazado a la red. Sus niveles se añadirán cuando la red se establezca. Cada laboratorio de investigación enlazado necesita el nivel requerido para la investigación programada para poder unirse a la investigación en red. Los niveles de todos los laboratorios de investigación que tomen parte serán añadidos conjuntamente.";
$lang['info'][124]['name'] = "Astrofísica";
$lang['info'][124]['description'] = "Conocimientos adicionales en la astrofísica posibilitan la construcción de laboratorios con los que se puede equipar las naves más y más. Esto hace posible que se realicen largas expediciones en áreas del espacio inexploradas. Además de esto, estos progresos pueden ser utilizados para una mayor colonización de la galaxia. Cada segundo nivel de esta tecnología permitirá colonizar un planeta adicional.";
$lang['info'][199]['name'] = "Tecnología de gravitón";
$lang['info'][199]['description'] = "Un gravitón es una partícula elemental responsable de los efectos de la gravedad. Es su propia antipartícula, tiene masa cero y carece de carga, también posee un giro de 2. A través del disparo de partículas concentradas de gravitón se genera un campo gravitacional artificial con suficiente potencia y poder de atracción para destruir no solo naves, sino lunas enteras. Para poder producir la cantidad necesaria de partículas de gravitón, el planeta tiene que ser capaz de generar una inmensa cantidad de energía. Requisitos: Laboratorio de investigación (nivel 12)";

// -------------------------- SHIPS ----------------------------------------------------------------------------------------------------//
$lang['info'][202]['name'] = "Nave chica de carga";
$lang['info'][202]['description'] = "Las naves pequeñas de carga son aproximadamente tan grandes como los cazadores, pero sin motores eficientes ni armamento para permitir más espacio de carga. La nave pequeña de carga tiene una capacidad de 5.000 unidades de recursos. La nave grande de transporte, es capaz de transportar cinco veces más. Simultaneamente, el casco, escudos y motores fueron mejorados. A causa de su bajo poder de ataque, las naves de carga son habitualmente escoltadas por otras naves.<br/><br/>La velocidad base de tus transportes pequeños se aumentará tan pronto como se haya investigado el nivel 5 del motor de impulso, ya que serán sustituidos en ese momento.";
$lang['info'][203]['name'] = "Nave grande de carga";
$lang['info'][203]['description'] = "Esta nave nunca debería ser enviada sola, puesto que apenas tiene armas u otras tecnologías, para permitir tanto espacio de carga como sea posible. La nave grande de carga sirve como un suministro rápido de recursos entre planetas gracias a su sofisticado motor de combustión. Naturalmente, acompaña a las flotas en ataques a planetas enemigos para recoger tantos recursos como sea posible.";
$lang['info'][204]['name'] = "Cazador ligero";
$lang['info'][204]['description'] = "Dado su relativamente débil escudo y sus simples sistemas de armamento, los cazadores ligeros pertenecen al grupo de naves de soporte cuando comienza la batalla. Su agilidad y velocidad se emparejan con el número en que suelen aparecer, convirtiéndose así en algo similar a un escudo muy duradero para naves más grandes y que no son tan maniobrables.";
$lang['info'][205]['name'] = "Cazador pesado";
$lang['info'][205]['description'] = "Durante el progreso del cazador ligero, los investigadores llegaron al punto en el que la tecnología convencional alcanzaba su límite. Para proporcionar más agilidad al nuevo cazador, se uso en primer momento un potente motor de impulso. A pesar de los costes adiciones y la complejidad, se abrieron nuevas posibilidades, en parte debido al uso general de materiales más costosos. A través del uso de la tecnología de impulso, se disponia de más energía para los sistemas de armas y escudos. La integridad estructural aumentada y más potencia de ataque hacen de este cazador una amenaza mucho mayor en combate que su predecesor.";
$lang['info'][206]['name'] = "Crucero";
$lang['info'][206]['description'] = "Con lásers pesados y cañones iónicos emergiendo en los campos de batalla, los cazadores estaban cada vez más y más obsoletos. A pesar de muchas modificaciones en el sistema de armamento y escudos, no se lograba aumentar lo suficiente para soportar ante los nuevos sistemas de defensa. <br/>Este es el motivo por el que se eligió desarrollar un nuevo tipo de nave que poseyera más blindaje y armás más potentes. Así nació el crucero. La nueva nave tiene un blindaje casi tres veces más fuerte que los cazadores pesados y una potencia de fuego dos veces más alta. Su velocidad de viaje también está por encima de las jamás vistas. No hay casi ninguna nave mejor para usar contra defensa planetaria de nivel bajo y medio, por lo que los cruceros han sido ampliamente adoptados a lo largo del universo durante cientos de años.<br/>Desafortunadamente, con la llegada de nuevos sistemas de defensa, como los cañones gauss y los cañones de plasma, el reinado de los cruceros tocó fin rápidamente. Hoy en día, todavía se usan para luchar contra grandes flotas de cazadores puesto que su sistema de armamento es muy efectivo contra este tipo de naves. ";
$lang['info'][207]['name'] = "Nave de batalla";
$lang['info'][207]['description'] = "Las naves de batalla son la espina dorsal de cualquier flota militar. Su pesado blindaje junto con un sistema de armamento impresionante y una velocidad de viaje relativamente alta hace que esta nave sea imprescindible para cualquier imperio. Además, su espacio de carga tiene la dimensión ideal para transportar los recursos conseguidos.";
$lang['info'][208]['name'] = "Colonizador";
$lang['info'][208]['description'] = "Con esta nave puedes conquistar nuevos planetas - ¡Algo muy imprescindible para un imperio floreciente! Si la nave aterriza en un planeta que todavía no ha sido descubierto, éste será transformado en su base. Provee de suficiente material para comenzar con la conquista de un nuevo mundo. El número máximo de planetas será determinado a través de los progresos en la investigacion de la astrofísica. Dos niveles nuevos de esta investigación permiten colonizar un planeta adicional.";
$lang['info'][209]['name'] = "Reciclador";
$lang['info'][209]['description'] = "Los combates espaciales parecen estar aumentando constantemente y en una simple batalla, pueden destruirse miles de naves, con los consiguientes escombros que se perderán para siempre. Las naves estándar de carga no tienen los medios para recolectar recursos útiles, ni siquiera para acercarse a ellos. <br/>Con el desarrollo de los recicladores, y gracias al avance del conocimiento sobre la tecnología de blindaje, finalmente se hizo posible recolectar estos campos de escombros. Los recicladores son similares en tamaño a las naves grandes de carga, pero las instalaciones adicionales, ocupan más espacio. Éste es el motivo por el que su capacidad está limitada a 20.000.";
$lang['info'][210]['name'] = "Sonda de espionaje";
$lang['info'][210]['description'] = "Las sondas de espionaje son pequeños droides no tripulados con un sistema de propulsión excepcionalmente rápido usado para espiar en planetas extranjeros. Con su avanzado sistema de comunicación, estas sondas pueden enviar de vuelta, a gran distancia, información inteligente. <br />Una vez que llegan a la órbita del planeta objetivo, las sondas permanecen allí para recoger los datos, y durante ese período, son relativamente fáciles de detectar. Debido a su tamaño y restricciones de peso, no se ha incorporado blindaje ni escudo de ningún tipo, ni sistemas de armamento. Una vez detectadas, las sondas normalmente son destruidas debido a su débil construcción.";
$lang['info'][211]['name'] = "Bombardero";
$lang['info'][211]['description'] = "El Bombardero es una nave de propósito especial, desarrollado para atravesar las defensas planetarias más pesadas. Gracias a un sistema de ataque guiado por láser, las bombas de plasma pueden ser lanzadas con gran precisión sobre el objetivo, causando una inmensa devastación en los sistemas de defensa planetaria. <br /><br />La velocidad básica de tus bombarderos aumentará tan pronto como el nivel 8 del propulsor hiperespacial haya sido investigado, momento en el que su motor será sustituido por este propulsor. ";
$lang['info'][212]['name'] = "Satélite solar";
$lang['info'][212]['description'] = "Los científicos descubrieron un método de transmitir energía eléctrica a la colonia usando satélites especialmente diseñados en una órbita geosincrónica. Los satélites solares recogen la energía solar y la transmiten a una estación de tierra usando una tecnología láser avanzada. La eficiencia de un satélite solar depende en la fuerza de la radiación solar recibida. En principio, la producción de energía en órbitas más cercanas al sol es mayor que en las órbitas distantes al sol. Debido a su buen coste/producción, los satélites solares pueden solucionar muchos problemas energéticos. Pero precaución: los satélites solares pueden ser destruidos fácilmente en combate.";
$lang['info'][213]['name'] = "Destructor";
$lang['info'][213]['description'] = "Con el destructor, la madre de todas las naves de batalla entra en escena. Su sistemas de armamento multi-phalanx consisten en cañones gauss, de plasma e iónicos armados en torretas de respuesta rápida, lo que permite eliminar a los cazadores operativos con una probabilidad del 99%. El tamaño de la nave es también su pega, ya que su maniobrabilidad es limitada, haciendo al destructor más parecido a una estación de combate que a una nave de guerra. El consumo de combustible de estos inmensos destructores es casi tan alto como su poder de ataque...<br/ ><br />Fuego rápido contra láser pequeño: 10";
$lang['info'][214]['name'] = "Estrella de la muerte";
$lang['info'][214]['description'] = "Las estrellas de la muerte estan equipadas con grandes cantidades de cañónes gauss, capaces de destruir cualquier cosa con un sólo disparo, sean destructores o lunas. Para proveer la energía necesaria a este arma, se utilizan grandes áreas de las estrellas de la muerte para generadores de energía. El tamaño de la nave también limita su velocidad de viaje, que es realmente baja. Se dice que el capitán ayuda frecuentemente a aumentar su velocidad. Sólo los imperios más grandes y avanzados tienen la suficiente mano de obra y las tecnologías más avanzadas necesarias para construir tales naves, del tamaño de una luna.";
$lang['info'][215]['name'] = "Acorazado";
$lang['info'][215]['description'] = "Esta nave ha sido diseñada totalmente para la batalla contra grandes confederaciones de flotas. Con sus cañones láseres altamente desarrollados es capaz de combatir a un gran número de naves al mismo tiempo. Gracias a su forma de construcción alargada y a gran sistema de armamento, la capacidad de carga es limitada pero esto se compensa con un menor consumo.";

// -------------------------- DEFENSES ----------------------------------------------------------------------------------------------------//
$lang['info'][401]['name'] = "Lanzamisiles";
$lang['info'][401]['description'] = "El lanzamisiles es un sistema de defensa sencillo, pero barato. Puede ser muy efectivo si se construye en grandes números, no necesita tecnología alguna puesto que es una sencilla arma balística. Su bajo coste de producción lo hace util contra pequeñas flotas, pero pierde efectividad una vez que se dispone de sistemas de defensa más grandes. En desarrollos posteriores solo se usa como bulto. <br /><br />En general, los sistemas de defensa se desactivan a sí mismos cuando se alcanzan parámetros de operación críticos para permitir una posibilidad de reparación. De promedio, el 70% de la defensa planetaria destruida puede ser reparada después del combate.";
$lang['info'][402]['name'] = "Láser pequeño";
$lang['info'][402]['description'] = "Para mantener el ritmo con el aumento notable de la velocidad de desarrollo en términos de tecnología especial, los científicos tuvieron que llegar a un nuevo sistema de defensa, capaz de aguantar contra naves y flotas más fuertes y mejor equipadas. <br/>De este modo, rápidamente nació el láser pequeño, que era capaz de disparar un rayo láser altamente concentrado contra el objetivo y provocar un daño mucho más elevado que el impacto de los mísiles balísticos. Por otro lado, el escudo de los cañones ha sido mejorado para aguantar la mayor potencia de fuego de las naves modernas. Debido a que un objetivo primordial era su bajo coste, la estructura base no ha sido mejorada comparándola con el lanzamisiles. <br/>Puesto que el láser pequeño ofrece más equilibrio, es el mejor sistema de defensa conocido y es usado tanto por pequeños imperios emergentes como por imperios multi-galácticos.";
$lang['info'][403]['name'] = "Láser grande";
$lang['info'][403]['description'] = "El láser grande es la evolución lógica del pequeño, en éste, la integridad estructural ha sido aumentada y se han adoptado nuevos materiales. De esta manera el blindaje podía ser mejorado, con la nueva energía y sistemas de ordenadores a bordo, se libera mucha más potencia sobre un objetivo que usando un láser pequeño.";
$lang['info'][404]['name'] = "Cañón Gauss";
$lang['info'][404]['description'] = "Las armas de proyectiles estaban consideradas como desfasadas, debido a la tecnología moderna de fusión nuclear, nuevas fuentes de energía, el descubrimiento de la tecnología de hiperespacio y una tecnología de aleaciones mejoradas. Pese a todo, era la misma tecnología que una vez ocupó su lugar, la que ahora llama de nuevo para entrar el próximo siglo; El principio subyacente es largamente sabido, y data de los siglos 20 y 21: El acelerador de partículas. Un Cañón Gauss actualmente no es nada más que un acelerador de partículas masivo de gran tamaño, donde los proyectiles con un peso de varias toneladas son acelerados usando enormes bobinas electromagnéticas. La velocidad de salida de estas enormes partículas es tan grande que las partículas de polvo en el aire circundante se queman y la repulsión del disparo sacude la tierra. Incluso las nuevas aleaciones de blindaje y tecnologías de escudo tienen un duro trago al sopotar el impacto del proyectil, a menudo, el disparo simplemente atraviesa la estructura del objetivo.";
$lang['info'][405]['name'] = "Cañón iónico";
$lang['info'][405]['description'] = "En el siglo 21 había una tecnología, denominada EMP, relacionada con los impulsos electromagnéticos. Tal impulso de energía es peligroso principalmente para los sistemas que usan energía eléctrica o son sensibles a él. En aquellos días, estas armas eran transportadas en bombas o misiles, pero con el desarrollo continuado del area del EMP es actualmente posible montar estas unidades en cañones sencillos. El cañón iónico, es de lejos, el mejor equipado con estas armas. El rayo iónico concentrado destruye cualquier sistema eléctrico desprotegido en el objetivo y desestabiliza el circuito del escudo de la nave. En combinación con otras armas, esto significa la destrucción total pese a que directamente no consigue dañar las estructuras. <br />La única nave conocida que usa cañones iónicos, es el crucero, por sus altas necesidades de energía de estos cañones y la realidad de que, normalmente, el combate requiere la destrucción del objetivo, no su paralización.";
$lang['info'][406]['name'] = "Cañón de plasma";
$lang['info'][406]['description'] = "La tecnología láser había sido llevada casi a la perfección, la tecnología iónica parecía haber alcanzado su tope, y en general, no había una visión sobre como llegar a conseguir mejorar los sistemas de armamento existentes. Pero esto cambió cuando nació la idea de unir estas dos tecnologías, mientras que el láser se utiliza para calentar las partículas de deuterio varios millones de grados, la tecnología iónica, entonces, carga esas partículas sobrecalentadas eléctricamente, el conocimiento de la electromagnética era imprescindible para contener este peligroso plasma. El aspecto azulado del rayo de plasma parece muy vistoso cuando está en camino hacia su objetivo, pero desde el punto de vista de la tripulación de las naves, este atractivo aspecto de la esfera de plasma significa una dolorosa destrucción. Se dice del armamento de plasma que es una de las amenazas más peligrosas, pero también tiene su precio.";
$lang['info'][407]['name'] = "Cúpula pequeña de protección";
$lang['info'][407]['description'] = "Mucho antes de que los generadores de escudos fueran integrados y portátiles, había grandes y viejos generadores en la superficie de los planetas. Estos eran capaces de crear un enorme escudo alrdedor de la superficie del planeta, capaz de absorber grandes cantidades de energía cuando eran atacados. Incluso ahora, y antes, un convoy de combate pequeño cae derrotado por estas cúpulas. Usando una tecnología de escudo más avanzada, éstas pueden ser aumentadas considerablemente, por lo que su habilidad para absorber energía, es incluso mayor. Por supuesto, solo una cúpula de cada tipo puede ser construida en el planeta.";
$lang['info'][408]['name'] = "Cúpula grande de protección";
$lang['info'][408]['description'] = "Esta es una versión avanzada de la cúpula de protección, y su característica principal es el aumento de su capacidad para absorber energia. Está basado en el mismo conocimiento tecnológico que la cúpula pequeña. Pero, los generadores son menos ruidosos al estar en funcionamiento.";
$lang['info'][502]['name'] = "Misiles antibalísticos";
$lang['info'][502]['description'] = "Un misil antibalístico puede proteger a tu planeta de un misíl interplanetario que se está aproximando.";
$lang['info'][503]['name'] = "Misil interplanetario";
$lang['info'][503]['description'] = "Los misiles interplanetarios destruyen los sistemas de defensa del enemigo. Los sistemas de defensa destruidos por los misiles interplanetarios no serán reparados.";

//----------------------------------------------------------------------------//
//SYSTEM
$lang['sys_attacker_lostunits'] = "El atacante ha perdido un total de";
$lang['sys_defender_lostunits'] = "El defensor ha perdido un total de";
$lang['sys_units'] = "unidades";
$lang['debree_field_1'] = "Un campo de escombros de";
$lang['debree_field_2'] = "flotan en la orbita del planeta.";
$lang['sys_gcdrunits'] = "En estas coordenadas flotan %s unidades de %s y %s unidades de %s";
$lang['sys_moonproba'] = "La probabilidad de que surja una luna de los escombros es de:";
$lang['sys_moonbuilt'] = "Las enormes cantidad de metal y cristal se fusionan y forman un satélite lunar en la orbita del planeta %s [%d:%d:%d]!";
$lang['sys_attack_title'] = "Las flotas se enfrentan en ";
$lang['sys_attack_round'] = "Ronda";
$lang['sys_attack_attacker_pos'] = "Atacante";
$lang['sys_attack_techologies'] = "Armas: %d %% Escudo: %d %% Blindaje: %d %% ";
$lang['sys_attack_defender_pos'] = "Defensor";
$lang['sys_ship_type'] = "Tipo";
$lang['sys_ship_count'] = "Cantidad";
$lang['sys_ship_weapon'] = "Armas";
$lang['sys_ship_shield'] = "Escudo";
$lang['sys_ship_armour'] = "Blindaje";
$lang['sys_destroyed'] = "Destruido";
$lang['fleet_attack_1'] = "La flota atacante dispara con una fuerza total de";
$lang['fleet_attack_2'] = "sobre el defensor. Los escudos del defensor absorben";
$lang['fleet_defs_1'] = "La flota defensora dispara con una fuerza total de";
$lang['fleet_defs_2'] = "sobre el atacante. Los escudos del atacante absorben";
$lang['damage'] = "puntos de daño,";
$lang['with'] = 'con';
$lang['shots'] = 'disparos';
$lang['sys_attacker_won'] = "El atacante ha ganado la batalla";
$lang['sys_defender_won'] = "El defensor ha ganado la batalla";
$lang['sys_both_won'] = "La batalla termino en empate";
$lang['sys_stealed_ressources'] = "obteniendo";
$lang['sys_and'] = "y";
$lang['sys_mess_tower'] = "Torre de control";
$lang['sys_mess_attack_report'] = "Reporte de batalla";
$lang['sys_spy_maretials'] = "Recursos en";
$lang['sys_spy_fleet'] = "Flota";
$lang['sys_spy_defenses'] = "Defensas";
$lang['sys_mess_qg'] = "Cuartel General";
$lang['sys_mess_spy_report'] = "Reporte de espionaje";
$lang['sys_mess_spy_lostproba'] = "La probabilidad de contra-espionaje es del %d %%";
$lang['sys_mess_spy_control'] = "Control del espacio";
$lang['sys_mess_spy_activity'] = "Acción de espionaje";
$lang['sys_mess_spy_ennemyfleet'] = "Se ha detectado una flota del planeta";
$lang['sys_mess_spy_seen_at'] = "cerca de tu planeta";
$lang['sys_mess_spy_destroyed'] = "Tu flota de espionaje ha sido destruida";
$lang['sys_stay_mess_stay'] = "Estacionamiento de flota";
$lang['sys_stay_mess_start'] = "Tu flota llega al planeta";
$lang['sys_stay_mess_end'] = " y ofrece los siguientes recursos: ";
$lang['sys_adress_planet'] = "[%s:%s:%s]";
$lang['sys_stay_mess_goods'] = "%s: %s, %s: %s, %s: %s";
$lang['sys_colo_mess_from'] = "Colonización";
$lang['sys_colo_mess_report'] = "Reporte de colonización";
$lang['sys_colo_defaultname'] = "Colonia";
$lang['sys_colo_arrival'] = "El colonizador llega a las coordenadas ";
$lang['sys_colo_maxcolo'] = ", pero, por desgracia, es imposible colonizar, no puede tener más de ";
$lang['sys_colo_allisok'] = ", 	los colonos están comenzando a construir una nueva colonia.";
$lang['sys_colo_notfree'] = ", 	los colonos no han encontrado un planeta con estos detalles. Ellos se ven obligados a dar marcha atrás completamente desmoralizados...";
$lang['sys_colo_astro_level'] = ", pero no es posible colonizar debido a que no tienes el nivel suficiente de astrofísica.";
$lang['sys_colo_planet'] = " planetas";
$lang['sys_expe_report'] = "Reporte de expedicion";
$lang['sys_recy_report'] = "Reporte de reciclaje";
$lang['sys_expe_blackholl_1'] = "La flota fue arrastrada hacia un agujero negro, esta parcialmente destruida.";
$lang['sys_expe_blackholl_2'] = "La flota fue arrastrada hacia un agujero negro, y fue completamente destruida!";
$lang['sys_expe_nothing_1'] = "Tus exploradores tomaron grandes fotos. Pero no han encontrado recursos";
$lang['sys_expe_nothing_2'] = "Tus exploradores han pasado todo el tiempo en el área seleccionada. Pero no han encontrado nada.";
$lang['sys_expe_found_goods'] = "La flota ha descubierto una nave no tripulada! <br> Tus exploradores han recuperado %s de %s, %s de %s, %s de %s y %s de %s.";
$lang['sys_expe_found_ships'] = "Tus exploradores han encontrado una flota abandonada, la dominaron y vienen de regreso. <br> Escuadron:";
$lang['sys_expe_back_home'] = "Tu expedición regresó al hangar.";
$lang['sys_mess_transport'] = "Flota de Transporte";
$lang['sys_tran_mess_owner'] = "Una de tus flota llega a %s %s y entrega su mercancía: %s de %s, %s de %s y %s de %s.";
$lang['sys_tran_mess_user'] = "Una flota proveniente de %s %s llega a tu planeta y entrega su mercancía: %s de %s, %s de %s y %s de %s.";
$lang['sys_mess_fleetback'] = "Vuelta de la flota";
$lang['sys_tran_mess_back'] = "Una flota regreso al planeta %s %s. La flota no entrego recursos.";
$lang['sys_recy_gotten'] = "Tu flota llego a las coordenadas indicadas y recolecto %s unidades de %s y %s unidades de %s.";
$lang['sys_gain'] = "Beneficios";
$lang['sys_fleet_won'] = "Una de tus flotas regresa del planeta %s %s y entrega %s de %s, %s de %s y %s de %s";
$lang['sys_perte_attaquant'] = "Parte Atacante";
$lang['sys_perte_defenseur'] = "Parte Defensor";
$lang['sys_debris'] = "Escombros";
$lang['sys_destruc_title'] = "Probabilidad de destruir luna %s :";
$lang['sys_mess_destruc_report'] = "Reporte de Destruccion";
$lang['sys_destruc_lune'] = "La probabilidad de destruir la luna es de: %d %% ";
$lang['sys_destruc_rip'] = "La probabilidad de que sean destruidas las estrellas de la muerte es de: %d %% ";
$lang['sys_destruc_stop'] = "El defensor ha conseguido detener la destrucción de la luna";
$lang['sys_destruc_mess1'] = "Las estrellas de la muerte disparan el graviton a la órbita de la luna";
$lang['sys_destruc_mess'] = "Una flota del planeta %s [%d:%d:%d] se dirige a la luna del planeta [%d:%d:%d]";
$lang['sys_destruc_moon'] = ". Los temblores empiezan a sacudir la superficie de la luna, despues de unos instantes la luna no soporta mas y vuela en mil pedazos, mision cumplida, la flota vuelve al planeta de origen.";
$lang['sys_destruc_ds'] = ". Los temblores empiezan a sacudir la superficie de la luna, pero algo sale mal, el graviton en las estrellas de la muerte también provoca temblores y las estrellas de la muerte vuelan en mil pedazos.";
$lang['sys_destruc_none'] = ". Las estrellas de la muerte no generan el poder necesario, la mision falla y las naves vuelven de regreso.";
$lang['sys_the'] = " el ";
$lang['sys_stay_mess_back'] = "Una de sus flotas vuelve de ";
$lang['sys_stay_mess_bend'] = " y entrega su mercancía ";
$lang['sys_missile_attack'] = 'Ataque con misiles';
$lang['sys_all_destroyed'] = 'Todos los misiles interplanetarios han sido destruidos por los misiles de intercepción.';
$lang['sys_planet_without_defenses'] = 'Planeta sin defensa.';
$lang['sys_some_destroyed'] = ' han sido destruidos por los misiles de intercepción.';
$lang['sys_missile_string'] = 'Un ataque con misiles (%1%) de %2% llega al planeta %3% <br><br>';

//----------------------------------------------------------------------------//
//class.CheckSession.php
$lang['css_account_banned_message'] = 'SU CUENTA HA SIDO SUSPENDIDA';
$lang['css_account_banned_expire'] = 'Expiraci&oacute;n:';

//----------------------------------------------------------------------------//
// FLEET MOVEMENTS
$lang['cff_no_fleet_data'] = 'No hay datos de la flota';
$lang['cff_aproaching'] = 'Se aproximan ';
$lang['cff_ships'] = ' naves';
$lang['cff_from_the_planet'] = 'desde el planeta ';
$lang['cff_from_the_moon'] = 'desde la luna ';
$lang['cff_the_planet'] = 'el planeta ';
$lang['cff_debris_field'] = 'campo de escombros ';
$lang['cff_to_the_moon'] = 'a la luna ';
$lang['cff_the_position'] = 'la posición ';
$lang['cff_to_the_planet'] = ' a el planeta ';
$lang['cff_the_moon'] = ' la luna ';
$lang['cff_from_planet'] = 'del planeta ';
$lang['cff_from_debris_field'] = 'del campo de escombros ';
$lang['cff_from_the_moon'] = 'de la luna ';
$lang['cff_from_position'] = 'de la posición ';
$lang['cff_missile_attack'] = 'Ataque con misiles';
$lang['cff_to'] = ' a ';
$lang['cff_one_of_your'] = 'Una de tus ';
$lang['cff_a'] = 'Una ';
$lang['cff_of'] = ' de ';
$lang['cff_goes'] = ' se dirige ';
$lang['cff_toward'] = ' hacia ';
$lang['cff_with_the_mission_of'] = '. Con la misión de: ';
$lang['cff_to_explore'] = ' a explorar ';
$lang['cff_comming_back'] = ' vuelve ';
$lang['cff_back'] = 'Regresando';
$lang['cff_to_destination'] = 'Llendo a destino';
$lang['cff_flotte'] = ' flotas';

//----------------------------------------------------------------------------//
// EXTRA LANGUAGE FUNCTIONS
$lang['fcm_moon'] = 'Luna';
$lang['fcp_colony'] = 'Colonia';
$lang['fgp_require'] = 'Requiere: ';
$lang['fgf_time'] = 'Tiempo: ';

//----------------------------------------------------------------------------//
// CombatReport.php
$lang['cr_lost_contact'] = 'Se perdió el contacto con la flota atacante.';
$lang['cr_first_round'] = '(La flota fue destruida en la primer ronda)';
$lang['cr_type'] = 'Tipo';
$lang['cr_total'] = 'Total';
$lang['cr_weapons'] = 'Armas';
$lang['cr_shields'] = 'Escudos';
$lang['cr_armor'] = 'Blindaje';
$lang['cr_destroyed'] = '¡Destruida!';
$lang['cr_no_access'] = '¡El reporte solicitado no existe!';

/* end of INGAME.php */
