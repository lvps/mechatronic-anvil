<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;

require __DIR__ . '/vendor/autoload.php';

// TODO: move to configuration file
define('INPUT', '../input');
define('OUTPUT', '../output');

$inputTree = new Directory(INPUT);
$inputTree->buildTree();
