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

	/**
	 * Metadata constructor.
	 *
	 * @param array|NULL $metadata
	 * @param bool $inheritable if this metadata applies to subdirectories too (makes sense only on a directory), default false
	 */
	public function __construct(array $metadata = NULL, bool $inheritable = false) {
		if(is_array($metadata)) {
			$this->metadata = $metadata;
		}
		$this->inheritable = (bool) $inheritable;
	}

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
	public function rebuildFromStack(array $metadataStack) {
		$this->metadata = [];
		if(($count = count($metadataStack)) > 0) {
			for($i = 0; $i < $count; $i++) {
				if($metadataStack[$i] !== NULL && $metadataStack[$i] instanceof Metadata) {
					// merge all inheritable + last (current directory) regardless
					if($metadataStack[$i]->isInheritable() || $i === ($count-1)) {
						$this->mergeOtherOverThis($metadataStack[$i]);
					}
				}
			}
		}
	}
}

