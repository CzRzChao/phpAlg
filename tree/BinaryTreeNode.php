<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 10:46
 * Desc: 入口测试文件
 */

class BinaryTreeNode
{

    public $value;
    public $parent;
    public $left;
    public $right;

    public function __construct($value, $parent, $left = null, $right = null)
    {
        $this->parent = $parent;
        $this->left   = $left;
        $this->right  = $right;
        $this->value  = $value;
    }

}