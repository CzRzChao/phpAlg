<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/4 10:48
 * Desc: 入口测试文件
 */

require_once 'BinaryTree.php';
require_once 'BinarySearchTree.php';
require_once 'RBTree.php';

(new BinaryTree())->main();
(new BinarySearchTree())->main();
(new RBTree())->main();