<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class AbstractOutputFile {
	/** @var File|NULL */
	private $input = NULL;
	/** @var File|NULL */
	private $output = NULL;
	/** @var Metadata|NULL */
	private $metadata = NULL;
	/** @var Parser|NULL */
	private $parser = NULL;

	public function __clone() {
		// TODO: if(!NULL)?
		$this->input = clone $this->input;
		$this->output = clone $this->output;
		$this->metadata = clone $this->metadata;
		$this->parser = clone $this->parser;
	}
}
