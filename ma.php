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
$metadataStack = [];
$currentInheritableMetadata = new Metadata();
$currentInheritableMetadata->setInheritable(true);
$currentGlobalMetadata = NULL;
$output->recursiveWalkCallback(function(File $file) use ($metadataStack, $currentInheritableMetadata, $currentGlobalMetadata) {
	// we're doing everything "in reverse", but basically: inheritable is overwritten by global which is overwritten by local.
	$file->addMetadataOnBottom($currentGlobalMetadata);
	$file->addMetadataOnBottom($currentInheritableMetadata);
}, function(Directory $entering) use ($metadataStack, $currentInheritableMetadata, $currentGlobalMetadata) {
	$currentGlobalMetadata = $entering->getMetadata();
	$metadataStack[] = $currentGlobalMetadata;
	$currentInheritableMetadata->replaceFromInheritableStack($metadataStack);
}, function(Directory $leaving) use ($metadataStack, $currentInheritableMetadata, $currentGlobalMetadata, $output) {
	// root directory has no parent
	if($leaving === $output) {
		$currentGlobalMetadata = NULL;
	} else {
		$currentGlobalMetadata = $leaving->getParent()->getMetadata();
	}
	array_pop($metadataStack);
	$currentInheritableMetadata->replaceFromInheritableStack($metadataStack);
});
onParsed($output);

//onRendered($output);