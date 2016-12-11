<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


trait HasMetadata {
	/** @var Metadata|NULL */
	private $metadata = NULL;

	/**
	 * @param Metadata|NULL $other
	 */
	public function addMetadataOnTop($other) {
		if($other === NULL) {
			return;
		}

		if(!($other instanceof Metadata)) {
			return;
		}

		if($this->metadata === NULL) {
			$this->metadata = $other;
			return;
		}

		$this->metadata = $other->merge($this->metadata);
	}

	/**
	 * @param Metadata|NULL $other
	 */
	public function addMetadataOnBottom($other) {
		if($other === NULL) {
			return;
		}

		if(!($other instanceof Metadata)) {
			return;
		}

		if($this->metadata === NULL) {
			$this->metadata = $other;
			return;
		}

		$this->metadata = $this->metadata->merge($other);
	}

	public function setMetadata(Metadata $metadata) {
		$this->metadata = $metadata;
	}

	/**
	 * @return Metadata|NULL
	 */
	public function getMetadata(): Metadata {
		return $this->metadata;
	}
}