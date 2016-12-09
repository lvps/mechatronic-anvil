<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class AbstractOutputFile {
	/** @var \DateTime|NULL */
	private $inputDateTime = NULL;
	/** @var string|NULL */
	private $inputHash = NULL;
	/** @var \DateTime|NULL */
	private $outputDateTime = NULL;
	/** @var string|NULL */
	private $outputHash = NULL;
	/** @var string|NULL */
	private $content = NULL;
	/** @var string|NULL */
	private $inputFile = NULL;
	/** @var string|NULL */
	private $outputFile = NULL;
	/** @var Metadata|NULL */
	private $metadata = NULL;
	/** @var Parser|NULL */
	private $parser = NULL;

	public function __clone() {
		$aof = new AbstractOutputFile();
		// TODO: if(!NULL)?
		$aof->inputDateTime = clone $this->inputDateTime;
		$aof->outputDateTime = clone $this->outputDateTime;
		$aof->metadata = clone $this->metadata;
		$aof->parser = clone $this->parser;
	}
}
