<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 10:45
 * Desc: 二叉搜索树
 */

include_once 'BinaryTree.php';

class BinarySearchTree extends BinaryTree
{

    /**
     * 获取最大节点
     * @param $root
     * @return BinaryTreeNode
     */
    public function getMax($root)
    {
        while (!$this->checkNullChild($root) && !$this->checkNullChild($root->right)) {
            $root = $root->right;
        }
        return $root;
    }

    /**
     * 获取最小节点
     * @param $root
     * @return mixed
     */
    public function getMin($root)
    {
        if ($this->checkNullChild($root) || $this->checkNullChild($root->left)) {
            return $root;
        } else {
            return $this->getMin($root->left);
        }
    }

    /**
     * 查找节点
     * @param $root
     * @param $key
     * @return bool|BinaryTreeNode|RBTreeNode
     */
    public function search($root, $key)
    {
        if ($this->checkNullChild($root)) {
            return false;
        }
        if ($root->value == $key) {
            return $root;
        } elseif ($root->value > $key) {
            return $this->search($root->left, $key);
        } else {
            return $this->search($root->right, $key);
        }
    }

    /**
     * @param $key
     * @return BinaryTreeNode
     */
    public function insert($key)
    {
        $parent = null;
        $root   = $this->root;
        while (!$this->checkNullChild($root)) {
            $parent = $root;
            if ($root->value > $key) {
                $root = $root->left;
            } else {
                $root = $root->right;
            }
        }
        $node = new BinaryTreeNode($key, $parent);
        if ($this->checkNullParent($parent)) {  // 根节点
            $this->root = $node;
        } elseif ($parent->value > $key) {
            $parent->left = $node;
        } else {
            $parent->right = $node;
        }
    }

    /**
     * 删除节点
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        $target_node = $this->search($this->root, $key);
        if ($target_node === false) {
            return false;
        }
        if ($this->checkNullChild($target_node->left)) {   // 左节点为空
            $this->replaceNode($target_node, $target_node->right);
        } elseif ($this->checkNullChild($target_node->right)) {    // 右节点为空
            $this->replaceNode($target_node, $target_node->left);
        } else {
            $replace = $this->getMin($target_node->right);  // 获取前驱节点
            if ($replace->parent != $target_node) { // 如果不是target的右子树
                $this->replaceNode($replace, $replace->right);
                $replace->right         = $target_node->right;
                $replace->right->parent = $replace;
            }
            $this->replaceNode($target_node, $replace);
            $replace->left         = $target_node->left;
            $replace->left->parent = $replace;
        }
    }

    /**
     * 替换目标节点
     * @param $target
     * @param $replace
     */
    public function replaceNode($target, $replace)
    {
        if ($this->checkNullParent($target->parent)) {
            $this->root = $replace;
        } elseif ($target == $target->parent->left) {
            $target->parent->left = $replace;
        } else {
            $target->parent->right = $replace;
        }
        if (!$this->checkNullChild($replace)) {
            $replace->parent = $target->parent;
        }
    }

    public function main()
    {
        echo PHP_EOL . '--------------BinarySearchTree--------------' . PHP_EOL;
        $pre        = [4, 2, 1, 3, 6, 5, 7];
        $mid        = [1, 2, 3, 4, 5, 6, 7];
        $this->root = $this->recoverByPreAndMid($pre, $mid, null);
        $node       = $this->getMax($this->root);
        echo $node->value . PHP_EOL;
        $node = $this->getMin($this->root);
        echo $node->value . PHP_EOL;
        $this->insert(1.1);
        $this->insert(5.1);
        $this->midOrder($this->root);
        echo PHP_EOL;
        $this->delete(1.1);
        $this->delete(4);
        $this->midOrder($this->root);
    }

}