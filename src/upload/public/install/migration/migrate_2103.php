<?php

$queries[] = "INSERT INTO `{prefix}statpoints` (`id_owner`, `id_ally`) SELECT `id`, '0' FROM `{prefix}users` LEFT JOIN `{prefix}statpoints` ON `id_owner` = `id` WHERE `id_owner` IS NULL";
$queries[] = "INSERT INTO `{prefix}statpoints` (`id_owner`, `id_ally`) SELECT '0', `id` FROM `{prefix}alliance` LEFT JOIN `{prefix}statpoints` ON `id_ally` = `id` WHERE `id_ally` IS NULL";
