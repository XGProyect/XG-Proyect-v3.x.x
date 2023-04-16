<?php
$lang = [
    'pr_title' => 'Premium Einstellungen',
    'pr_sub_title' => 'Alles, was mit dunkler Materie zu tun hat, kann hier verwaltet werden. Vom Offizier zum Händler.',
    'pr_general' => 'Allgemein',
    'pr_pay_url' => 'Bezahl-URL',
    'pr_registration_dark_matter' => 'Anfängliche Dunkle Materie',
    'pr_trader' => 'Ressourcenmarkt - Handle mit Ressourcen',
    'pr_trader_price' => 'Preis, um den Händler zu rufen (Dunkle Materie)',
    'pr_merchant_base_min_exchange_rate' => 'Mindestwechselkurs',
    'pr_merchant_base_max_exchange_rate' => 'Maximaler Wechselkurs',
    'pr_merchant_metal_multiplier' => 'Basistarif Metall',
    'pr_merchant_crystal_multiplier' => 'Basistarif Kristall',
    'pr_merchant_deuterium_multiplier' => 'Basistarif Deuterium',
    'pr_merchant_explanation' => 'Händlertarife variieren im Verhältnis 3/2/1 (Standardwerte), gegeben durch die 3 Basistarife. Bei jedem Ruf beim Händler schwankt der Preis zwischen 0,7 % und 1 %. Wenn wir beispielsweise Metall verkaufen, beträgt der Basispreis 3, aber Kristall und Deuterium werden basierend auf einer Zufallszahl zwischen 0,7 (Standard) und 1 (Standard) berechnet.<br><br>Beispiel:<br> zufällig(0,7,1)=0,88;<br>Metallpreis = 3 <br>Kristallpreis = 2 * 0,88 (3 Metall = 1,76 Kristall)<br>Deuteriumpreis = 1 * 0,88 (3 Metall = 0,88 Deuterium)<br><br>Dies wiederholt sich gleichermaßen mit den 3 Ressourcen, wobei die verkaufte Ressource immer eine Konstante ist.',
    'pr_save_changes' => 'Änderungen speichern',
    'pr_all_ok_message' => 'Änderungen erfolgreich gespeichert!',
];
