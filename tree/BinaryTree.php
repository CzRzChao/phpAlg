<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 10:15
 * Desc: 入口测试文件
 */

require_once 'BinaryTreeNode.php';

class BinaryTree
{

    /**
     * @var BinaryTreeNode
     */
    public $root;


    /**
     * 前序遍历
     * @param $node
     */
    public function preOrder($node)
    {
        if ($this->checkNullChild($node)) {
            return;
        }
        $this->outputNode($node);
        $this->preOrder($node->left);
        $this->preOrder($node->right);
    }

    public function midOrder($node)
    {
        if ($this->checkNullChild($node)) {
            return;
        }
        $this->midOrder($node->left);
        $this->outputNode($node);
        $this->midOrder($node->right);
    }

    public function backOrder($node)
    {
        if ($this->checkNullChild($node)) {
            return;
        }
        $this->backOrder($node->left);
        $this->backOrder($node->right);
        $this->outputNode($node);
    }

    /**
     * 根据前序和中序恢复二叉树
     * @param array $pre
     * @param array $mid
     * @param       $parent
     * @return BinaryTreeNode|null
     */
    public function recoverByPreAndMid(array &$pre, array $mid, $parent)
    {
        if (empty($mid)) {
            return null;
        }

        // 中间节点
        $mid_value       = array_shift($pre);
        $mid_node        = new BinaryTreeNode($mid_value, $parent);
        $mid_index       = array_search($mid_value, $mid);
        $mid_left        = array_slice($mid, 0, $mid_index);
        $mid_right       = array_slice($mid, $mid_index + 1);
        $mid_node->left  = $this->recoverByPreAndMid($pre, $mid_left, $mid_node);
        $mid_node->right = $this->recoverByPreAndMid($pre, $mid_right, $mid_node);
        return $mid_node;
    }

    protected function checkNullChild($node)
    {
        return $node == null;
    }

    protected function checkNullParent($node)
    {
        return $node == null;
    }

    protected function outputNode($node)
    {
        echo $node->value . ' ';
    }

    public function main()
    {
        echo PHP_EOL . '--------------BinaryTree--------------' . PHP_EOL;
        $pre        = [1, 2, 4, 5, 3, 6, 7];
        $mid        = [4, 2, 5, 1, 6, 3, 7];
        $this->root = $this->recoverByPreAndMid($pre, $mid, null);
        $this->preOrder($this->root);
        echo PHP_EOL;
        $this->midOrder($this->root);
        echo PHP_EOL;
        $this->backOrder($this->root);
        echo PHP_EOL;
    }

}
