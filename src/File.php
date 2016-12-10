<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class File implements HasParent {
	use Stat;

	/** @var string|NULL */
	private $name;
	/** @var string|NULL */
	private $hash;
	/** @var File|NULL */
	private $renderFrom;
	/** @var Directory */
	private $parent;
	/** @var Metadata|NULL */
	private $metadata = NULL;
	/** @var Parser|NULL */
	private $parser = NULL;

	/**
	 * File constructor.
	 *
	 * @param string $name file name
	 * @param Directory $parent Directory where file is located
	 * @param File|NULL $from input file to be rendered\copied into this output file
	 */
	function __construct(string $name, Directory &$parent, File &$from = NULL) {
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

	//public function __clone() {
		// don't clone renderFrom, it points to a File in another Directory tree and should stay that way.
	//}

	public function stat() {
		$filename = $this->getFilename();
		$this->doStat($filename);
	}

	public function getContents(): string {
		return file_get_contents($this->getFilename());
	}

	public function getFilename(): string {
		return $this->parent->getPath() . DIRECTORY_SEPARATOR . $this->name;
	}

	public function setParent(Directory &$parent) {
		$this->parent = $parent;
	}

	public function getParent(): Directory {
		return $this->parent;
	}

	/**
	 * @return File|NULL
	 */
	public function getRenderFrom() {
		return $this->renderFrom;
	}

	/**
	 * @param File|NULL $renderFrom
	 */
	public function setRenderFrom($renderFrom) {
		$this->renderFrom = $renderFrom;
	}

	/**
	 * @return Parser|NULL
	 */
	public function getParser() {
		return $this->parser;
	}

	/**
	 * @param Parser|NULL $parser
	 */
	public function setParser($parser) {
		$this->parser = $parser;
	}

	public function addMetadataOnTop(Metadata $other) {
		if($this->metadata === NULL) {
			$this->metadata = $other;
			return;
		}

		$this->metadata = $other->merge($this->metadata);
	}

	public function addMetadataOnBottom(Metadata $other) {
		if($this->metadata === NULL) {
			$this->metadata = $other;
			return;
		}

		$this->metadata = $this->metadata->merge($other);
	}

	public function __toString(): string {
		return $this->getFilename();
	}
}