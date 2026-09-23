<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_4_2_4($object)
{
    return true; // Tell main module file to run runUpgradeModule() function (translations were added)
}
