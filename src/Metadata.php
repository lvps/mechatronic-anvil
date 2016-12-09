<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class Metadata implements \ArrayAccess {
	/** @var array */
	private $metadata = [];
	/** @var bool */
	private $global = false;
	/** @var bool */
	private $inheritable = false;

	public function offsetExists($offset): bool {
		return isset($this->metadata[$offset]);
	}

	public function offsetGet($offset) {
		return $this->offsetExists($this->metadata[$offset]) ? $this->metadata[$offset] : NULL;
	}

	public function offsetSet($offset, $value) {
		if($offset === NULL) {
			$this->metadata[] = $value;
		} else {
			$this->metadata[$offset] = $value;
		}
	}

	public function offsetUnset($offset) {
		unset($this->metadata[$offset]);
	}

	/**
	 * @param boolean $global
	 */
	public function setGlobal(bool $global) {
		$this->global = $global;
	}

	/**
	 * @return boolean
	 */
	public function isGlobal(): bool {
		return $this->global;
	}

	/**
	 * @param boolean $inheritable
	 */
	public function setInheritable(bool $inheritable) {
		$this->inheritable = $inheritable;
	}

	/**
	 * @return boolean
	 */
	public function isInheritable(): bool {
		return $this->inheritable;
	}

	/**
	 * Merge this object over other metadata (or merge other metadata under this)
	 *
	 * @param Metadata $other
	 */
	public function merge(Metadata $other) {
		$this->metadata = array_merge($other->metadata, $this->metadata);
	}
}

