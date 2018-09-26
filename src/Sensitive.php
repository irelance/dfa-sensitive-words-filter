<?php
/**
 * Created by PhpStorm.
 * User: heirelance
 * Date: 2018/9/25
 * Time: 15:07
 */

namespace Irelance\Sensitive;

class Sensitive
{
    protected $trieTree;
    protected $disturbList = [];
    protected $escapeChar = '*';
    protected $isGreed = false;

    /**
     * 设置dfa树。
     *
     * @param SensitiveMap $tree
     */
    public function setTrieTree($tree)
    {
        $this->trieTree = $tree;
    }

    /**
     * 设置干扰字符集合。
     *
     * @param string $string
     */
    public function setDisturbList($string)
    {
        $length = mb_strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $this->disturbList[] = mb_substr($string, $i, 1);
        }
    }

    /**
     * 设置敏感词替换字符。
     *
     * @param string $char
     */
    public function setEscapeChar($char)
    {
        $this->escapeChar = $char;
    }

    /**
     * 设置敏感词替换字符。
     *
     * @param boolean $bool
     */
    public function setGreedMode(bool $bool)
    {
        $this->isGreed = $bool;
    }

    /**
     * 将文本中的敏感词使用替代字符替换，返回替换后的文本。
     *
     * @param string $text
     * @param array $scan
     * @return mixed
     */
    public function escape($text, &$scan = null)
    {
        $scan = $this->scan($text);
        if (empty($scan)) return $text;
        usort($scan, function ($a, $b) {
            return $a <=> $b;
        });
        return str_ireplace(array_column($scan, 'word'), array_column($scan, 'replace'), $text);
    }

    /**
     * 扫描并返回检测到的敏感词。
     *
     * @param string $text 要扫描的文本。
     * @return array 返回敏感词组成的数组。
     */
    public function scan($text)
    {
        $result = array();
        $textLength = mb_strlen($text);
        for ($i = 0; $i < $textLength; $i++) {
            $wordLength = $this->check($text, $i);
            if ($wordLength > 0) {
                $word = mb_substr($text, $i, $wordLength);
                if (!isset($result[$word])) {
                    $result[$word] = ['word' => $word, 'count' => 1, 'length' => mb_strlen($word)];
                    $result[$word]['replace'] = str_repeat($this->escapeChar, $result[$word]['length']);
                } else {
                    $result[$word]['count']++;
                }
                $i += $wordLength - 1;
            }
        }
        return $result;
    }

    /**
     * 从指定位置开始逐一扫描文本，如果扫描到敏感词，则返回敏感词长度。
     * 如果扫描的第一个字符不是敏感词头，则直接返回0。
     *
     * @param $text
     * @param $beginIndex
     * @return int
     */
    protected function check($text, $beginIndex)
    {
        $flag = false;
        $result = 0;
        $triePointer = $this->trieTree->map();
        $length = $beginIndex + $this->trieTree->depth();
        for ($i = $beginIndex; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1);
            if ($char === '') {
                break;
            }
            if (in_array($char, $this->disturbList)) { // 检查是不是干扰字，是的话指针往前走一步。
                $result++;
                $length++;
                continue;
            }
            if (!isset($triePointer[$char])) { // 一旦发现没有匹配敏感词，则直接跳出。
                break;
            }
            $result++;
            if ($triePointer[$char]['is_end']) { // 看看是否到达词尾。
                $flag = true;
                if ($this->isGreed) {
                    $triePointer = $triePointer[$char]; // 往深层引用，继续检索。
                } else {
                    break;
                }
            } else {
                $triePointer = $triePointer[$char]; // 往深层引用，继续检索。
            }
        }
        $flag || $result = 0; // 如果检查到最后一个字条还没有匹配到词尾，则当作没有匹配到。
        return $result;
    }
}