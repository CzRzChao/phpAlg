<?php
/**
 * Copyright © czrzchao.com
 * User: czrzchao
 * Date: 2018/9/5 16:47
 * Desc: 大根堆
 */

class MaxHeap
{

    public function getParent($i)
    {
        return intval(ceil($i / 2 - 1));
    }

    public function getLeft($i)
    {
        return 2 * $i + 1;
    }

    public function getRight($i)
    {
        return 2 * ($i + 1);
    }

    public function exchange($heap, $i, $j)
    {
        $tmp      = $heap[$i];
        $heap[$i] = $heap[$j];
        $heap[$j] = $tmp;
        return $heap;
    }

    /**
     * 维护大根堆 O(lgN)
     * @param $heap
     * @param $i
     * @return mixed
     */
    public function maxHeapify($heap, $i)
    {
        $left  = $this->getLeft($i);
        $right = $this->getRight($i);
        $large = $i;
        if ($left <= count($heap) && $heap[$left] > $heap[$i]) {
            $large = $left;
        }

        if ($right <= count($heap) && $heap[$right] > $heap[$large]) {
            $large = $right;
        }

        if ($large != $i) { // 如果子节点比自己大 进行交换
            $heap = $this->exchange($heap, $large, $i);
            $heap = $this->maxHeapify($heap, $large);  // 递归维护
        }
        return $heap;
    }

    /**
     * 通过维护构建大根堆 O(N)
     * 返回一个堆数组，本质是一个完全二叉树
     * @param $origin_array
     * @return array
     */
    public function buildMaxHeapByHeapify($origin_array)
    {
        $mid  = $this->getParent(count($origin_array));
        $heap = $origin_array;
        for ($i = $mid; $i >= 0; $i--) {
            $heap = $this->maxHeapify($heap, $i);
        }
        return $heap;
    }

    /**
     * 堆排序 O(NlgN)
     * @param $origin_array
     * @return array
     */
    public function maxSort($origin_array)
    {
        $heap       = $this->buildMaxHeapByHeapify($origin_array);
        $sort_array = [];
        while (count($heap)) {
            // 将最大元素和最后一个元素互换，然后出堆
            $heap         = $this->exchange($heap, 0, count($heap) - 1);
            $tmp          = array_pop($heap);
            $sort_array[] = $tmp;
            // 重新维护最大堆
            $heap = $this->maxHeapify($heap, 0);
        }
        return $sort_array;
    }

    /**
     * 大根堆插入数据 O(lgN)
     * @param $heap
     * @param $key
     * @return array|mixed
     */
    public function heapInsert($heap, $key)
    {
        $heap[] = $key;
        $i      = count($heap) - 1;
        while ($i > 0 && $heap[$i] > $heap[$this->getParent($i)]) {
            $heap = $this->exchange($heap, $i, $this->getParent($i));
            $i    = $this->getParent($i);
        }
        return $heap;
    }

    /**
     * 弹出最大元素 O(lgN)
     * @param $heap
     * @return mixed
     * @throws Exception
     */
    public function popMax(&$heap)
    {
        if (empty($heap)) {
            throw new Exception('heap underflow');
        }
        $max     = $heap[0];
        $heap[0] = $heap[count($heap) - 1];
        array_pop($heap);
        $heap = $this->maxHeapify($heap, 0);
        return $max;
    }

    protected function outputHeap($heap)
    {
        foreach ($heap as $value) {
            echo $value . ' ';
        }
        echo PHP_EOL;
    }

    public function main()
    {
        echo PHP_EOL . '--------------MaxHeap--------------' . PHP_EOL;
        $origin_array = [1, 2, 3, 4, 5, 6];
        $heap         = $this->buildMaxHeapByHeapify($origin_array);
        $this->outputHeap($heap);
        $sort = $this->maxSort($origin_array);
        $this->outputHeap($sort);
        $heap = [];
        foreach ($origin_array as $value) {
            $heap = $this->heapInsert($heap, $value);
            $this->outputHeap($heap);
        }
        while ($heap) {
            echo $this->popMax($heap) . ' ';
        }
        echo PHP_EOL;
    }

}