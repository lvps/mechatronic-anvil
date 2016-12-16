<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;

require __DIR__ . '/vendor/autoload.php';

if(!defined('INPUT')) {
	define('INPUT', 'input');
}
if(!is_dir(INPUT)) {
	throw new \Exception('Input directory ('.INPUT.') is not a directory!');
}
if(!defined('OUTPUT')) {
	define('OUTPUT', 'output');
}
if(!is_dir(OUTPUT)) {
	throw new \Exception('Output directory ('.OUTPUT.') is not a directory!');
}
if(!defined('TEMPLATES')) {
	define('TEMPLATES', 'templates');
}
if(!is_dir(TEMPLATES)) {
	throw new \Exception('Templates directory ('.TEMPLATES.') is not a directory!');
}
if(!isset($parsers) || !($parsers instanceof ParserCollection)) {
	$parsers = new ParserCollection();
	$parsers->pushParser(new Parsers\Markdown());
	$parsers->pushParser(new Parsers\MarkdownWithYAMLFrontMatter());
	$parsers->pushParser(new Parsers\YamlForMarkdown());
	$parsers->pushParser(new Parsers\UnderscoreDotYaml());
	// TODO: set some defaults
}
if(!function_exists('onRead')) {
	function onRead(Directory &$output) {}
}
if(!function_exists('onParsed')) {
	function onParsed(Directory &$output) {}
}
if(!function_exists('onMerged')) {
	function onMerged(Directory &$output) {}
}
if(!function_exists('onRendered')) {
	function onRendered(Directory &$output) {}
}

$inputTree = new Directory(INPUT);
$inputTree->buildTree();
$output = $inputTree->buildOutputTree(new Directory(OUTPUT));
onRead($output);

$output->recursiveWalkCallback(function(File $file) use ($parsers, $output) {
	$parsers->tryParse($file);
});
onParsed($output);

$metadataStack = [];
$currentInheritableMetadata = new Metadata();
$currentInheritableMetadata->setInheritable(true);
$currentGlobalMetadata = NULL;
$output->recursiveWalkCallback(function(File $file) use (&$currentInheritableMetadata, &$currentGlobalMetadata) {
	// we're doing everything "in reverse", but basically: inheritable is overwritten by global which is overwritten by local.
	$file->addMetadataOnBottom($currentGlobalMetadata);
	$file->addMetadataOnBottom($currentInheritableMetadata);
}, function(Directory $entering) use (&$metadataStack, &$currentInheritableMetadata, &$currentGlobalMetadata) {
	$currentGlobalMetadata = $entering->getMetadata();
	$metadataStack[] = $currentGlobalMetadata;
	$currentInheritableMetadata->replaceFromInheritableStack($metadataStack);
}, function(Directory $leaving) use (&$metadataStack, &$currentInheritableMetadata, &$currentGlobalMetadata, $output) {
	// root directory has no parent
	if($leaving === $output) {
		$currentGlobalMetadata = NULL;
	} else {
		$currentGlobalMetadata = $leaving->getParent()->getMetadata();
	}
	array_pop($metadataStack);
	$currentInheritableMetadata->replaceFromInheritableStack($metadataStack);
});
onMerged($output);

$output->recursiveWalkCallback(function(File $file) {
	if(!$file->getDoRender()) {
		return;
	}
	$file->render();
	$file->applyMode();
	$file->applyMtime();
}, function(Directory $entering) {
	$path = $entering->getPath();
	if(!is_dir($path)) {
		mkdir($path);
	}
	$entering->applyMode();
}, function(Directory $leaving) {
	$leaving->applyMtime();
});
onRendered($output);