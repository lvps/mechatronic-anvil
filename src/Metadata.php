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

	private function mergeOtherOverThis(Metadata $other) {
		$this->metadata = array_merge($this->metadata, $other->metadata);
	}

	/**
	 * Very ad hoc method. Does THINGS.
	 *
	 * @param array $metadataStack array of Metadata or NULL.
	 */
	public function replaceFromInheritableStack(array $metadataStack) {
		$this->metadata = [];
		foreach($metadataStack as $metadata) {
			if($metadata !== NULL && $metadata instanceof Metadata) {
				if($metadata->isInheritable) {
					$this->mergeOtherOverThis($metadata);
				}
			}
		}
	}
}

