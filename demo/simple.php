<?php
/**
 * Created by PhpStorm.
 * User: heirelance
 * Date: 2018/9/25
 * Time: 14:45
 */

require __DIR__ . '/../vendor/autoload.php';

//build map
$map = new \Irelance\Sensitive\SensitiveMap();
//add words
$map->addWords(['remove', 'sensitive']);
//remove word
$map->removeWord('remove');

//build worker
$worker = new \Irelance\Sensitive\Sensitive();
//add map
$worker->setTrieTree($map);
//add disturb
$worker->setDisturbList('`');

//filter words
var_dump($worker->escape('remove, sensi`tive', $scan));
var_dump($scan);