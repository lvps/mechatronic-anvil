<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;

require __DIR__ . '/vendor/autoload.php';

if(!defined('INPUT')) {
	throw new \Exception('INPUT not defined!');
}
if(!defined('OUTPUT')) {
	throw new \Exception('OUTPUT not defined!');
}
if(!isset($parsers) || !($parsers instanceof ParserCollection)) {
	$parsers = new ParserCollection();
	// TODO: set some defaults
}
if(!function_exists('onRead')) {
	function onRead(Directory &$output) {}
}
if(!function_exists('onParsed')) {
	function onParsed(Directory &$output) {}
}
if(!function_exists('onRendered')) {
	function onRendered(Directory &$output) {}
}

$inputTree = new Directory(INPUT);
$inputTree->buildTree();
$output = $inputTree->buildOutputTree(new Directory(OUTPUT));
onRead($output);
$output->recursiveWalkCallback(function(File $file) use ($parsers) {
	$parsers->tryMatch($file);
});
//onParsed($output);

//onRendered($output);