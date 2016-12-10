<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class Directory {
	private $name;
	private $parent;

	function __construct(string $name, Directory &$parent = NULL) {
		$this->setName($name);
		$this->parent = $parent;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name) {
		if($name === NULL || $name === '') {
			throw new \InvalidArgumentException('Directories cannot have an empty name!');
		}
		$this->name = $name;
	}

	public function getPath(): string {
		if($this->parent instanceof Directory) {
			return $this->parent->getPath() . DIRECTORY_SEPARATOR . $this->name;
		} else {
			return $this->name;
		}
	}
}
