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
if(!defined('REBASE')) {
	define('REBASE', '');
}
if(!isset($parsers)) {
	$parsers = ['UnderscoreDotYaml', 'YamlForMarkdown', 'MarkdownWithYAMLFrontMatter', 'Markdown'];
}
if(is_array($parsers)) {
	$i = count($parsers);
	$parsers_new = new ParserCollection();
	while($i--) {
		// There are no words to describe this.
		// TODO: something to allow users to add their own parsers without sending a pull request?
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
$currentMetadata = [];
function buildMetadataFromStack(array $metadataStack): array {
	$result = [];
	if(($count = count($metadataStack)) > 0) {
		for($i = 0; $i < $count; $i++) {
			if($metadataStack[$i] !== NULL && is_array($metadataStack[$i]) && !empty($metadataStack[$i])) {
				$result = array_merge($result, $metadataStack[$i]);
			}
		}
	}

	return $result;
}
$output->recursiveWalkCallback(function(File $file) use (&$currentMetadata) {
	// we're doing everything "in reverse", but basically: global is overwritten by local.
	$file->addMetadataOnBottom($currentMetadata);
}, function(Directory $entering) use (&$metadataStack, &$currentMetadata) {
	$metadataStack[] = $entering->getMetadata();
	$currentMetadata = buildMetadataFromStack($metadataStack);
}, function(Directory $leaving) use (&$metadataStack, &$currentMetadata, $output) {
	array_pop($metadataStack);
	$currentMetadata = buildMetadataFromStack($metadataStack);
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

$output->deleteDeletedFiles();
onCleaned($output);
println('Done cleaning output directory');

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
	$path = $entering->getFilename();
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
function arrayUnprefix($arr) {
	$prefix = __NAMESPACE__ . '\\Parsers\\';
	$prefixLen = strlen($prefix);
	$rebuilt = [];
	foreach($arr as $parser => $count) {
		if(substr($parser, 0, $prefixLen) === $prefix) {
			$rebuilt[substr($parser, $prefixLen)] = $count;
		} else {
			$rebuilt[$parser] = $count;
		}
	}
	return $rebuilt;
}
$stats['parsers'] = arrayUnprefix($stats['parsers']);
$stats['renderers'] = arrayUnprefix($stats['renderers']);
// Result is sorted by value (number of files parsed) descending, then key (parser name) ascending.
// This has to be done in "reverse" (ksort before arsort).
ksort($stats['parsers']);
arsort($stats['parsers']);
ksort($stats['renderers']);
arsort($stats['renderers']);

println($stats['filesRead'] . ' files read by ' . count($stats['parsers']) . ' parsers:');
foreach($stats['parsers'] as $parser => $count) {
		println('-> ' . $parser . ': ' . $count);
}
println('');
println($stats['filesRendered'] . ' files rendered by ' . count($stats['renderers']) . ' parsers:');
foreach($stats['renderers'] as $parser => $count) {
	println('-> ' . $parser . ': ' . $count);
}
