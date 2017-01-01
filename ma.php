<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;
define('VERSION', '0'); // TODO: version 1.0.0 and git tag when stable enough

function println($text) {
	echo $text . PHP_EOL;
}

println('Mechatronic anvil '.VERSION.' started.');

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
if(!isset($parsers)) {
	$parsers = ['UnderscoreDotYaml', 'YamlForMarkdown', 'MarkdownWithYAMLFrontMatter', 'Markdown'];
}
if(is_array($parsers)) {
	$i = count($parsers);
	$parsers_new = new ParserCollection();
	while($i--) {
		// There are no words to describe this.
		$parserName = __NAMESPACE__ . '\\Parsers\\' . $parsers[$i];
		if(!class_exists($parserName)) {
			throw new \Exception($parserName . ' does not exist!');
		}
		$parsers_new->pushParser(new $parserName());
	}
	$parsers = $parsers_new;
	unset($parsers_new);
}
if(!($parsers instanceof ParserCollection)) {
	throw new \Exception('$parsers should be a ParserCollection object or an array!');
}
if(!function_exists('onRead')) {
	function onRead(Directory $output) {}
}
if(!function_exists('onParsed')) {
	function onParsed(Directory $output) {}
}
if(!function_exists('onMerged')) {
	function onMerged(Directory $output) {}
}
if(!function_exists('onPruned')) {
	function onPruned(Directory $output) {}
}
if(!function_exists('onCleaned')) {
	function onCleaned(Directory $output) {}
}
if(!function_exists('onRendered')) {
	function onRendered(Directory $output) {}
}

$inputTree = new Directory(INPUT);
$inputTree->buildTree();
$output = $inputTree->buildOutputTree(new Directory(OUTPUT));
onRead($output);
println('Done reading input tree');

$stats = ['parsers' => [], 'renderers' => [], 'filesRead' => 0, 'filesRendered' => 0];
$output->recursiveWalkCallback(function(File $file) use ($parsers, &$stats) {
	$theParser = $parsers->tryParse($file);
	$stats['filesRead']++;
	if(isset($stats['parsers'][$theParser])) {
		$stats['parsers'][$theParser]++;
	} else {
		$stats['parsers'][$theParser] = 1;
	}
});
onParsed($output);
println('Done parsing');

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
println('Done merging metadata');

$output->recursiveDeleteOnCondition(function(File $file) {
	return !$file->getDoRender();
},
	NULL,

	function(Directory $leaving) {
	if($leaving->countContent() === 0) {
		return true;
	} else {
		return false;
	}
});
onPruned($output);
println('Done pruning non-renderable files');

// TODO: clean output directory from files/directories that don't exist anymore
onCleaned($output);
//println('Done cleaning output directory');

$output->recursiveWalkCallback(function(File $file) use (&$stats) {
	$theParser = $file->render();
	$file->applyMode();
	$file->applyMtime();
	$stats['filesRendered']++;
	if(isset($stats['renderers'][$theParser])) {
		$stats['renderers'][$theParser]++;
	} else {
		$stats['renderers'][$theParser] = 1;
	}
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
println('Done rendering');

println('');
arsort($stats['parsers']);
arsort($stats['renderers']);
$prefix = __NAMESPACE__ . '\\Parsers\\';
$prefixLen = strlen($prefix);
println($stats['filesRead'] . ' files read by ' . count($stats['parsers']) . ' parsers:');
foreach($stats['parsers'] as $parser => $count) {
	if(substr($parser, 0, $prefixLen) === $prefix) {
		println('-> ' . substr($parser, $prefixLen) . ': ' . $count);
	} else {
		println('-> ' . $parser . ': ' . $count);
	}
}
println('');
println($stats['filesRendered'] . ' files rendered by ' . count($stats['renderers']) . ' parsers:');
foreach($stats['renderers'] as $parser => $count) {
	if(substr('-> ' . $parser, 0, $prefixLen) === $prefix) {
		println(substr($parser, $prefixLen) . ': ' . $count);
	} else {
		println('-> ' . $parser . ': ' . $count);
	}
}
