<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


interface HasParent {
	public function setParent(Directory $parent);
	public function getParent(): Directory;
}