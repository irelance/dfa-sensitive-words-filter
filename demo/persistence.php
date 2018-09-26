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
$map->addWords(['add', 'addition']);
$txt = serialize($map);
//save to storage
unset($map);

//load form storage
$mapLoad = unserialize($txt);

//build worker
$worker = new \Irelance\Sensitive\Sensitive();
$worker->setTrieTree($mapLoad);
$worker->setGreedMode(true);
var_dump($worker->escape('test for addition word', $scan));
var_dump($scan);