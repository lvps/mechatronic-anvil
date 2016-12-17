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
		return $this->offsetExists($offset) ? $this->metadata[$offset] : NULL;
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
	 * Merge this object over other metadata
	 *
	 * @param Metadata $other
	 * @return NULL
	 */
	public function mergeOver(Metadata $other) {
		$this->metadata = array_merge($other->metadata, $this->metadata);
		return NULL;
	}

	/**
	 * Merge this object under other metadata
	 *
	 * @param Metadata $other
	 * @return NULL
	 */
	public function mergeUnder(Metadata $other) {
		$this->metadata = array_merge($this->metadata, $other->metadata);
		return NULL;
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
						$this->mergeUnder($metadataStack[$i]);
					}
				}
			}
		}
	}
}

