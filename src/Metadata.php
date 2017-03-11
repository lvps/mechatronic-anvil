<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class Metadata implements \ArrayAccess {
	/** @var array */
	private $metadata = [];

	/**
	 * Metadata constructor.
	 *
	 * @param array|NULL $metadata
	 */
	public function __construct(array $metadata = NULL) {
		if(is_array($metadata)) {
			$this->metadata = $metadata;
		}
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
	 * @return Metadata
	 */
	public static function buildFromStack(array $metadataStack): Metadata {
		$result = new Metadata();
		if(($count = count($metadataStack)) > 0) {
			for($i = 0; $i < $count; $i++) {
				if($metadataStack[$i] !== NULL && $metadataStack[$i] instanceof Metadata) {
					$result->mergeUnder($metadataStack[$i]);
				}
			}
		}

		return $result;
	}
}

