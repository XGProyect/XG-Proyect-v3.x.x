<?php

$lang = [
    'pr_title' => 'Premium settings',
    'pr_sub_title' => 'Everything that involves dark matter can be managed here. From officers to merchants.',
    'pr_general' => 'General',
    'pr_pay_url' => 'Payment URL',
    'pr_registration_dark_matter' => 'Initial Dark Matter',
    'pr_trader' => 'Resource Market - Exchange resources',
    'pr_trader_price' => 'Price to call the merchant (Dark Matter)',
    'pr_merchant_base_min_exchange_rate' => 'Minimum exchange rate',
    'pr_merchant_base_max_exchange_rate' => 'Maximum exchange rate',
    'pr_merchant_metal_multiplier' => 'Metal base rate',
    'pr_merchant_crystal_multiplier' => 'Crystal base rate',
    'pr_merchant_deuterium_multiplier' => 'Deuterium base rate',
    'pr_merchant_explanation' => 'Merchant rates vary in a 3/2/1 ratio (default values), given by the 3 base rates. With each call to the merchant, the price fluctuates between 0.7% and 1%. So, for example, if we sell metal, the base price is 3 but the crystal and deuterium will be calculated based on a random number between 0.7 (default) and 1 (default).<br><br>Example:<br>random(0.7,1)=0.88;<br>metal price = 3 <br>crystal price = 2 * 0.88 (3 metal = 1.76 crystal)<br>deuterium price = 1 * 0.88 (3 metal = 0.88 deuterium)<br><br>This is repeated equally with the 3 resources, being always the resource being sold a constant.',
    'pr_save_changes' => 'Save changes',
    'pr_all_ok_message' => 'Changes saved successfully!',
];
