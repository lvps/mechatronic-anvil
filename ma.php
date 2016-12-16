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

$output->recursiveWalkCallback(function(File $file) use ($parsers) {
	$parsers->tryParse($file);
});
onParsed($output);

$metadataStack = [];
$currentMetadata = new Metadata();
$currentMetadata->setInheritable(true);
$output->recursiveWalkCallback(function(File $file) use (&$currentMetadata) {
	// we're doing everything "in reverse", but basically: global is overwritten by local.
	$file->addMetadataOnBottom($currentMetadata);
}, function(Directory $entering) use (&$metadataStack, &$currentMetadata) {
	$metadataStack[] = $entering->getMetadata();
	$currentMetadata->rebuildFromStack($metadataStack);
}, function(Directory $leaving) use (&$metadataStack, &$currentMetadata, $output) {
	array_pop($metadataStack);
	$currentMetadata->rebuildFromStack($metadataStack);
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