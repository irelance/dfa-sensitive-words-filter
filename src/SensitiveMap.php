<?php
/**
 * Created by PhpStorm.
 * User: heirelance
 * Date: 2018/9/25
 * Time: 14:29
 */

namespace Irelance\Sensitive;

class SensitiveMap
{
    protected $map = [];
    protected $depth = 0;

    public function addWord($word)
    {
        $thisChar = &$this->map;
        $length = mb_strlen($word);
        $this->depth = max($length, $this->depth);//更新树深
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($word, $i, 1);
            if (!isset($thisChar[$char])) {
                $thisChar[$char] = ['is_end' => $i + 1 == $length];//是否一个匹配词
            }
            $thisChar = &$thisChar[$char];
        }
    }

    public function removeWord($word)
    {
        $thisChar = &$this->map;
        $length = mb_strlen($word);
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($word, $i, 1);
            if (!isset($thisChar[$char])) {
                return;
            }
            $thisChar = &$thisChar[$char];
        }
        if ($thisChar['is_end']) {
            $thisChar['is_end'] = false;
        }
    }

    public function addWords(array $words)
    {
        foreach ($words as $word) {
            $this->addWord($word);
        }
    }

    public function removeWords(array $words)
    {
        foreach ($words as $word) {
            $this->removeWord($word);
        }
    }

    public function depth()
    {
        return $this->depth;
    }

    public function map()
    {
        return $this->map;
    }
}