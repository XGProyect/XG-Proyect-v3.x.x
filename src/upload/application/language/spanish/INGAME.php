<?php
$lang = [
    // system
    'sys_building_queue_build_order' => 'Orden de construcción',
    'sys_building_queue_destroy_order' => 'Orden de destrucción',
    'sys_building_queue_not_enough_resources' => 'La %s de tu %s de nivel %s en %s no pudo ejecutarse.<br><br>Recursos insuficientes: %s.',
    'sys_building_queue_not_enough_resources_from' => 'Mensaje del sistema',
    'sys_building_queue_not_enough_resources_subject' => 'Producción cancelada',
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
$lang['Crystal'] = 'Cristal';
$lang['Deuterium'] = 'Deuterio';
$lang['Darkmatter'] = 'Materia Oscura';
$lang['Energy'] = 'Energía';
$lang['write_message'] = 'Escribir mensaje'; //FleetsLib.php

// TimingLib.php
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
//BUILDINGS - RESEARCH - SHIPYARD - DEFENSES
$lang['bd_cancel'] = 'Cancelar';
$lang['bd_remaining'] = 'Restantes';
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
/* end of INGAME.php */
