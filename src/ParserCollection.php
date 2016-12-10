<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;

class ParserCollection {
	private $parsers;

	function __construct() {
		$this->parsers = [new Parsers\PlainCopy()];
	}

	public function pushParser(Parser $parser) {
		$this->parsers[] = $parser;
	}

	public function popParser(): Parser {
		if(count($this->parsers) > 1) {
			return array_pop($this->parsers);
		} else {
			throw new \UnderflowException('Tried to remove Parser from empty list (default parser cannot be removed)');
		}
	}

	public function tryMatch(File &$file) {
		$i = count($this->parsers);
		while($i--) {
			if($this->parsers[$i]->canParse($file)) {
				$this->parsers[$i]->parse($file);
				return;
			}
		}

		throw new \RuntimeException($file.' cannot be parsed!');
	}
}