<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 16:15
 * Desc: 红黑树节点
 */

require_once 'BinaryTreeNode.php';

class RBTreeNode extends BinaryTreeNode
{

    const RED   = 1;
    const BLACK = 2;

    public $color;

    public function __construct($value, $parent, $color = self::RED, $left = null, $right = null)
    {
        $this->color = $color;
        parent::__construct($value, $parent, $left, $right);
    }

}