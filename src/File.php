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
	private $name;
	/** @var string|NULL */
	private $hash;
	/** @var File|NULL */
	private $renderFrom;
	/** @var Directory */
	private $parent;

	/**
	 * File constructor.
	 *
	 * @param string $name file name
	 * @param Directory $parent Directory where file is located
	 * @param File|NULL $from input file to be rendered\copied into this output file
	 */
	function __construct(string $name, Directory $parent, File &$from = NULL) {
		if($name === NULL || $name === '') {
			throw new \InvalidArgumentException('File names cannot be empty!');
		}
		if(!($parent instanceof Directory)) {
			throw new \InvalidArgumentException('$parent must be a Directory object!');
		}
		$this->renderFrom = $from;
		$this->parent = $parent;
		$this->name = $name;
	}

	public function __clone() {
		// TODO: if(!NULL)?
		$this->dateTime = clone $this->dateTime;
		$this->parent = clone $this->parent;
	}

	public function getContents(): string {
		return file_get_contents($this->getFilename());
	}

	public function getFilename(): string {
		return $this->parent->getPath() . DIRECTORY_SEPARATOR . $this->name;
	}
}
