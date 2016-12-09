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
	/** @var string */
	private $inputDirectory;
	/** @var string */
	private $outputDirectory;

	// binary "enum"
	const INPUT = true;
	const OUTPUT = false;

	/**
	 * File constructor.
	 *
	 * @param bool $io File::INPUT or File::OUTPUT
	 * @param string $inputDirectory
	 * @param string $outputDirectory
	 */
	function __construct(boolean $io, string $inputDirectory, string $outputDirectory) {
		$this->io = $io;
		$this->inputDirectory = $inputDirectory;
		$this->outputDirectory = $outputDirectory;
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

	public function getContents(): string {
		return file_get_contents($this->getFilename());
	}

	public function getFilename(): string {
		if($this->io === self::INPUT) {
			return $this->inputDirectory . DIRECTORY_SEPARATOR . $this->path;
		} else {
			return $this->outputDirectory . DIRECTORY_SEPARATOR . $this->path;
		}
	}
}
