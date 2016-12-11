<?php
/*
 * Copyright (c) 2016 Ludovico Pavesi
 * Released under The MIT License (see LICENSE)
 */
namespace lvps\MechatronicAnvil\Parsers;

use Symfony\Component\Yaml\Yaml;

trait YamlParserWrapper {
    public static function yamlParse($str) {
        try {
            return Yaml::parse($str);
        } catch(\Exception $e) { // ParseException
            throw new \Exception("Unable to parse the YAML string: " . $e->getMessage());
        }
    }
}