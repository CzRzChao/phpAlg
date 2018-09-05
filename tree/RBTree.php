<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 16:15
 * Desc: 红黑树
 */

require_once 'RBTreeNode.php';
require_once 'BinarySearchTree.php';

class RBTree extends BinarySearchTree
{

    /**
     * @var RBTreeNode
     */
    public $root;

    protected $child_nil;
    protected $parent_nil;

    public function __construct()
    {
        $this->child_nil  = new RBTreeNode(null, null, RBTreeNode::BLACK);
        $this->parent_nil = new RBTreeNode(null, null, RBTreeNode::BLACK);
        $this->root       = $this->child_nil;
    }

    protected function checkNullChild($node)
    {
        return $node === $this->child_nil;
    }

    protected function checkNullParent($node)
    {
        return $node === $this->parent_nil;
    }

    protected function outputNode($node)
    {
        echo $node->value . '[' . ($node->color == RBTreeNode::RED ? '红' : '黑') . '] ';
    }

    /**
     * 左旋
     * @param $node
     */
    public function leftRotate($node)
    {
        // node右孩子的左孩子变成node的右孩子
        $right       = $node->right;
        $node->right = $right->left;
        if (!$this->checkNullChild($right->left)) {
            $right->left->parent = $node;
        }
        // 将node变成左孩子的左孩子
        $right->left   = $node;
        $right->parent = $node->parent;
        if ($this->checkNullParent($node->parent)) { // node节点为根节点
            $this->root = $right;
        } elseif ($node->parent->left == $node) {
            $node->parent->left = $right;
        } else {
            $node->parent->right = $right;
        }
        $node->parent = $right;
    }

    /**
     * 右旋
     * @param $node
     */
    public function rightRotate($node)
    {
        // node左孩子的右孩子变成node的左孩子
        $left       = $node->left;
        $node->left = $left->right;
        if (!$this->checkNullChild($left->right)) {
            $left->right->parent = $node;
        }
        $left->right  = $node;
        $left->parent = $node->parent;
        if ($this->checkNullParent($node->parent)) {
            $this->root = $left;
        } elseif ($node->parent->left == $node) {
            $node->parent->left = $left;
        } else {
            $node->parent->right = $left;
        }
        $node->parent = $left;
    }

    /**
     * 插入一个节点
     * @param $key
     * @return void
     */
    public function insert($key)
    {
        $parent = $this->parent_nil;
        $root   = $this->root;
        while (!$this->checkNullChild($root)) {
            $parent = $root;
            if ($root->value > $key) {
                $root = $root->left;
            } else {
                $root = $root->right;
            }
        }
        $node = new RBTreeNode($key, $parent, RBTreeNode::RED, $this->child_nil, $this->child_nil);
        if ($this->checkNullParent($parent)) {  // 根节点
            $this->root = $node;
        } elseif ($parent->value > $key) {
            $parent->left = $node;
        } else {
            $parent->right = $node;
        }
        $node->color = RBTreeNode::RED;
        $this->_insertFixUp($node);
    }

    /**
     * 插入后恢复红黑树性质
     * @param RBTreeNode $node
     */
    private function _insertFixUp(RBTreeNode $node)
    {
        while ($node->parent->color == RBTreeNode::RED) {  // 父节点为红
            if ($node->parent->parent->left == $node->parent) { // 父节点为祖父的左节点
                $uncle = $node->parent->parent->right;
                if ($uncle->color == RBTreeNode::RED) { // case1:叔叔节点为红
                    $uncle->color                = RBTreeNode::BLACK;
                    $node->parent->color         = RBTreeNode::BLACK;
                    $node->parent->parent->color = RBTreeNode::RED;
                    $node                        = $node->parent->parent; // 将控制转移到祖父节点
                } else {
                    if ($node == $node->parent->right) {    // case2:当前节点为父节点的右节点
                        $node = $node->parent;  // 控制转移到父节点
                        $this->leftRotate($node);   // 左旋 转为case3
                    }
                    // case3:当前节点为父节点的左节点 变色加右旋不会改变红黑树性质
                    $node->parent->color         = RBTreeNode::BLACK;   // 父节点变黑
                    $node->parent->parent->color = RBTreeNode::RED; // 祖父节点变红
                    $this->rightRotate($node->parent->parent);  // 右旋祖父节点
                }
            } else {    // 对称逻辑
                $uncle = $node->parent->parent->left;
                if ($uncle->color == RBTreeNode::RED) {
                    $uncle->color                = RBTreeNode::BLACK;
                    $node->parent->color         = RBTreeNode::BLACK;
                    $node->parent->parent->color = RBTreeNode::RED;
                    $node                        = $node->parent->parent;
                } else {
                    if ($node == $node->parent->left) {
                        $node = $node->parent;
                        $this->rightRotate($node);
                    }
                    $node->parent->color         = RBTreeNode::BLACK;
                    $node->parent->parent->color = RBTreeNode::RED;
                    $this->leftRotate($node->parent->parent);
                }
            }
        }
        $this->root->color = RBTreeNode::BLACK;
    }

    /**
     * 删除某个节点
     * @param $key
     * @return bool
     */
    public function delete($key)
    {
        $target_node = $this->search($this->root, $key);
        if ($target_node === false) {
            return false;
        }
        $del_color = $target_node->color;
        if ($this->checkNullChild($target_node->left)) {   // 左节点为空
            $fix_node = $target_node->right;    // 记录替换节点，也就是可能需要修正的节点
            $this->replaceNode($target_node, $target_node->right);
        } elseif ($this->checkNullChild($target_node->right)) {    // 右节点为空
            $fix_node = $target_node->left;
            $this->replaceNode($target_node, $target_node->left);
        } else {
            $replace          = $this->getMin($target_node->right);  // 获取前驱节点
            $del_color        = $replace->color;
            $fix_node         = $replace->right;
            $fix_node->parent = $replace;   // 帮助nil叶子节点找到父节点
            if ($replace->parent != $target_node) { // 如果不是target的右子树
                $this->replaceNode($replace, $replace->right);
                $replace->right         = $target_node->right;
                $replace->right->parent = $replace;
            }
            $this->replaceNode($target_node, $replace);
            $replace->left         = $target_node->left;
            $replace->left->parent = $replace;
            $replace->color        = $target_node->color;
        }
        if ($del_color == RBTreeNode::BLACK) {  // 当替换的节点为黑色时，需要进行修正
            $this->_deleteFixUp($fix_node);
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
        $replace->parent = $target->parent; // 因为红黑树有nil叶子节点，可以直接将parent赋予替换节点
    }

    /**
     * 删除后恢复红黑树性质
     * @param $node
     */
    private function _deleteFixUp($node)
    {
        while ($node !== $this->root && $node->color == RBTreeNode::BLACK) {
            if ($node == $node->parent->left) {
                $bro = $node->parent->right;
                if ($bro->color == RBTreeNode::RED) { // case1:兄弟节点为红色
                    $bro->color          = RBTreeNode::BLACK;
                    $node->parent->color = RBTreeNode::RED;
                    $this->leftRotate($node->parent);
                    $bro = $node->parent->right;
                }

                if ($bro->left->color == RBTreeNode::BLACK && $bro->right->color == RBTreeNode::BLACK) {    // case2:兄弟节点的孩子都是黑色
                    $bro->color = RBTreeNode::RED;    // 将兄弟节点染成黑色
                    $node       = $node->parent;  // 控制转移给父节点
                } else {
                    if ($bro->right->color == RBTreeNode::BLACK) { // case3:兄弟节点右孩子为黑色
                        $bro->left->color = RBTreeNode::BLACK;    // 染色 为右旋做准备
                        $bro->color       = RBTreeNode::RED;
                        $this->rightRotate($bro);
                        $bro = $node->parent->right;
                    }
                    // case4:
                    $bro->color          = $node->parent->color;
                    $node->parent->color = RBTreeNode::BLACK;
                    $bro->right->color   = RBTreeNode::BLACK;
                    $this->leftRotate($node->parent);
                    $node = $this->root;
                }
            } else {    // 对称逻辑
                $bro = $node->parent->left;
                if ($bro->color == RBTreeNode::RED) {
                    $bro->color          = RBTreeNode::BLACK;
                    $node->parent->color = RBTreeNode::RED;
                    $this->rightRotate($node->parent);
                    $bro = $node->parent->left;
                }

                if ($bro->right->color == RBTreeNode::BLACK && $bro->left->color == RBTreeNode::BLACK) {
                    $bro->color = RBTreeNode::RED;
                    $node       = $node->parent;
                } else {
                    if ($bro->left->color == RBTreeNode::BLACK) {
                        $bro->right->color = RBTreeNode::BLACK;
                        $bro->color        = RBTreeNode::RED;
                        $this->leftRotate($bro);
                        $bro = $node->parent->left;
                    }
                    $bro->color          = $node->parent->color;
                    $node->parent->color = RBTreeNode::BLACK;
                    $bro->left->color    = RBTreeNode::BLACK;
                    $this->rightRotate($node->parent);
                    $node = $this->root;
                }
            }
        }
        $node->color             = RBTreeNode::BLACK;
        $this->child_nil->parent = null;    // 恢复nil节点
    }

    /**
     * 校验红黑树
     * @return bool
     */
    public function isRBTree()
    {
        if ($this->checkNullChild($this->root)) {
            return true;
        }
        if ($this->root->color == RBTreeNode::RED) {
            return false;
        }
        $root       = $this->root;
        $black_high = 0;
        while (!$this->checkNullChild($root)) {
            if ($root->color == RBTreeNode::BLACK) {
                $black_high++;
            }
            $root = $root->left;
        }
        return $this->_isRBTree($this->root, $black_high, 0);
    }

    public function _isRBTree($node, $black_high, $count)
    {
        // 空节点说明通过前面的校验，直接返回true
        if ($this->checkNullChild($node)) {
            return true;
        }
        // 连续红节点不满足红黑树性质
        if ($node->color == RBTreeNode::RED && $node->parent->color == RBTreeNode::RED) {
            throw new Exception('存在连续红节点');
        }
        // 黑节点计数
        if ($node->color == RBTreeNode::BLACK) {
            $count++;
        }
        // 到了某个叶子节点校验黑高
        if ($this->checkNullChild($node->left) || $this->checkNullChild($node->right)) {
            if ($count != $black_high) {
                throw new Exception('黑高不同');
            }
        }
        // 递归校验左右子树
        return $this->_isRBTree($node->left, $black_high, $count) && $this->_isRBTree($node->right, $black_high, $count);
    }

    public function main()
    {
        echo PHP_EOL . '--------------RBTree--------------' . PHP_EOL;
        $mid = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 9.1];
        // 随机插入测试
        shuffle($mid);
        foreach ($mid as $value) {
            $this->insert($value);
            $this->preOrder($this->root);
            echo PHP_EOL;
            if ($this->isRBTree()) {
                echo '是红黑树:)' . PHP_EOL;
            }
        }
        // 随机删除测试
        shuffle($mid);
        foreach ($mid as $value) {
            $this->delete($value);
            $this->preOrder($this->root);
            echo PHP_EOL;
            if ($this->isRBTree()) {
                echo '是红黑树:)' . PHP_EOL;
            }
        }
    }

}