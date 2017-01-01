<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */

namespace lvps\MechatronicAnvil;


class Rebase {
	public static function Rebase(string $page): string {
		if(REBASE === '') {
			return $page;
		}

		$page = preg_replace_callback('#(href|src)=(("(?<!/)/(?!/)[^"]*")|(\'(?<!/)/(?!/)[^\']*\'))#i', __CLASS__ . '::rebaseCallback', $page);
		$page = preg_replace_callback('#srcset=(("/[^"]*")|(\'/[^\']*\'))#i', __CLASS__ . '::rebaseCallbackSrcset', $page);
		return $page;
	}


	private static function rebaseCallback(array $matches): string {
		$delimiter = substr($matches[2], 0, 1);
		$value = substr($matches[2], 1, strlen($matches[2])-2);
		return $matches[1] . '=' . $delimiter . REBASE . $value . $delimiter;
	}

	private static function rebaseCallbackSrcset(array $matches): string {
		$delimiter = substr($matches[2], 0, 1);
		$value = substr($matches[2], 1, strlen($matches[2])-2);
		$value = preg_replace('#, /(?!/)#i', ', '.REBASE.'/', $value);

		if(substr($value, 0, 2) !== '//') {
			return 'srcset=' . $delimiter . REBASE . $value . $delimiter;
		} else {
			return 'srcset=' . $delimiter . $value . $delimiter;
		}
	}
}