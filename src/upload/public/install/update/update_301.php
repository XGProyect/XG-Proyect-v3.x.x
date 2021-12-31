<?php

$queries[] = "ALTER TABLE `" . FLEETS . "`  ADD `fleet_fuel` BIGINT(11) NOT NULL DEFAULT '0'  AFTER `fleet_resource_deuterium`;";
