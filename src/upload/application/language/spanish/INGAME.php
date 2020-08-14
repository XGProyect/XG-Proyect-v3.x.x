<?php
$lang = [
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
$lang['online'] = 'Conectado';
$lang['minutes'] = '15 min';
$lang['offline'] = 'Desconectado';

// used by FleetsLib.php and GalaxyLib.php
// TODO: refactor and remove
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
$lang['fgp_require'] = 'Requiere: ';
$lang['fgf_time'] = 'Tiempo: ';

/* end of INGAME.php */
