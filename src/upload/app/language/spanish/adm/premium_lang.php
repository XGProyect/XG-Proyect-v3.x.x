<?php
$lang = [
    'pr_title' => 'Ajustes de Premium',
    'pr_sub_title' => 'Todo lo que implique materia oscura puede ser administrado desde aquí. Desde oficiales hasta el mercader.',
    'pr_general' => 'General',
    'pr_pay_url' => 'URL de pago',
    'pr_registration_dark_matter' => 'Materia Oscura al comienzo',
    'pr_trader' => 'Mercado de recursos - Cambiar recursos',
    'pr_trader_price' => 'Costo para llamar al Mercader (Materia Oscura)',
    'pr_merchant_base_min_exchange_rate' => 'Tasa de cambio mínima',
    'pr_merchant_base_max_exchange_rate' => 'Tasa de cambio máxima',
    'pr_merchant_metal_multiplier' => 'Tasa base de metal',
    'pr_merchant_crystal_multiplier' => 'Tasa base de cristal',
    'pr_merchant_deuterium_multiplier' => 'Tasa base de deuterio',
    'pr_merchant_explanation' => 'Las tasas del mercader varían en una proporción de 3/2/1 (valores por defecto), dados por los 3 valores base. Con cada llamada al mercader el precio fluctua entre un 0.7% y un 1%. Entonces por ejemplo, si vendemos metal, el precio base es 3 pero el cristal y deuterio serán calculados en base a un numero aleatorio entre 0.7 (valor por defecto) y 1 (valor por defecto).<br><br>Ejemplo:<br>aleatorio(0.7,1)=0.88;<br>precio metal = 3<br>precio cristal = 2 * 0.88 (3 metal = 1.76 cristal)<br>precio deuterio = 1 * 0.88 (3 metal = 0.88 deuterio)<br><br>Esto se repite por igual con los 3 recursos, siendo siempre el que se vende constante.',
    'pr_save_changes' => 'Guardar cambios',
    'pr_all_ok_message' => '¡Cambios guardados con éxito!',
];
