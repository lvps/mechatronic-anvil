<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class File {
	/** @var \DateTime|NULL */
	private $dateTime;
	/** @var string|NULL */
	private $path;
	/** @var string|NULL */
	private $hash;
	/** @var bool */
	private $io;

	// binary "enum"
	const INPUT = true;
	const OUTPUT = false;

	/**
	 * File constructor.
	 *
	 * @param bool $io File::INPUT or File::OUTPUT
	 */
	function __construct(boolean $io) {
		$this->io;
	}

	public function __clone() {
		// TODO: if(!NULL)?
		$this->dateTime = clone $this->dateTime;
	}

	/**
	 * If it's an input file: clone and return an output file.
	 * If it's an output file: clone and return an input file.
	 *
	 * @return File
	 */
	public function cloneToInputOutput(): File {
		$file = clone $this;
		$file->io = !($file->io);
		return $file;
	}
}
