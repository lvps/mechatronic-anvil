<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class File implements HasParent {
	use Stat, HasMetadata;

	/** @var string|NULL */
	private $name;
	/** @var string|NULL */
	private $hash;
	/** @var File|NULL */
	private $renderFrom;
	/** @var Directory */
	private $parent;
	/** @var Parser|NULL */
	private $parser = NULL;
	/** @var bool */
	private $doRender = true;

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

	public function getBasename(): string {
		return $this->name;
	}

	public function getBasenameWithoutExtension(): string {
		$pieces = explode(".", $this->name, 2);
		return $pieces[0];
	}

	public function getExtension(): string {
		$pieces = explode(".", $this->name, 2);
		if($pieces > 0) {
			return $pieces[1];
		} else {
			return '';
		}
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
	 * @param Parser|NULL $parser
	 */
	public function setParser($parser) {
		$this->parser = $parser;
	}

	public function __toString(): string {
		return $this->getFilename();
	}

	public function getDoRender(): bool {
		return $this->doRender;
	}

	public function doNotRender() {
		$this->doRender = false;
	}

	public function render() {
		$this->makesSenseToRender();
		$this->parser->renderToFile($this);
	}

	private function makesSenseToRender() {
		if(!$this->getDoRender()) {
			throw new \LogicException($this.' isn\'t a renderizable file!');
		}
		if(!($this->parser instanceof Parser)) {
			throw new \LogicException('No valid parser set for '.$this.'!');
		}
	}
}
