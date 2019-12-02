<?php


namespace Guolei\Php\Components;


class Paginator
{
    //current page index
    protected $current = 1;
    //first page index
    protected $first = 1;
    //last page index
    protected $last = 1;
    //previous page index
    protected $previous = 1;
    //next page index
    protected $next = 1;
    //offset
    protected $offset = 0;
    //page size
    protected $size = 10;
    //total page quantity
    protected $pages = 1;
    //total quantity
    protected $total = 1;
    //split
    protected $split = 10;
    //current splits
    protected $currents = [];
    //previous split
    protected $previousSplit = 0;
    //next split
    protected $nextSplit = 0;

    /**
     * @return int
     */
    public function getCurrent()
    {
        if (intval($this->current) < 1) {
            $this->current = 1;
        }
        if (intval($this->current) >= intval($this->getPages())) {
            $this->current = $this->getPages();
        }
        return $this->current;
    }

    /**
     * @param int $current
     */
    public function setCurrent($current)
    {
        if (intval($current) < 1) {
            $current = 1;
        }
        if (intval($current) >= intval($this->getPages())) {
            $current = $this->getPages();
        }
        $this->current = $current;
    }

    /**
     * @return int
     */
    public function getFirst()
    {
        $this->first = 1;
        return $this->first;
    }

    /**
     * @return int
     */
    public function getLast()
    {
        $this->last = intval($this->getPages());
        return $this->last;
    }

    /**
     * @return int
     */
    public function getPrevious()
    {
        $current = intval($this->getCurrent());
        $previous = $current - 1;
        if ($previous < 1) {
            $previous = 1;
        }
        $this->previous = $previous;
        return $this->previous;
    }

    /**
     * @return int
     */
    public function getNext()
    {
        $current = intval($this->getCurrent());
        $pages = intval($this->getPages());
        $next = $current + 1;
        if ($next > $pages) {
            $next = $pages;
        }
        $this->next = $next;
        return $this->next;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        $current = intval($this->current);
        $size = $this->getSize();
        $offset = ($current - 1) * $size;
        if ($offset < 0) {
            $offset = 0;
        }
        $this->offset = $offset;
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        if (intval($size) < 1) {
            $size = 10;
        }
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getPages()
    {
        $pages = 1;
        $total = intval($this->getTotal());
        $size = intval($this->getSize());
        if ($total % $size == 0) {
            $pages = $total / $size;
        } else {
            $pages = intval($total / $size) + 1;
        }
        if ($pages < 1) {
            $pages = 1;
        }
        $this->pages = $pages;
        return $this->pages;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        if (intval($total) < 0) {
            $total = 0;
        }
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getSplit()
    {
        return $this->split;
    }

    /**
     * @param int $split
     */
    public function setSplit($split)
    {
        if (intval($split) < 1) {
            $split = 10;
        }
        $this->split = $split;
    }

    /**
     * @return array
     */
    public function getCurrents()
    {
        $currents = [];
        if (intval($this->getPages()) <= intval($this->getSplit())) {
            for ($i = 0; $i < intval($this->getPages()); $i++) {
                $currents[] = $i + 1;
            }
        } else {
            if (intval($this->getCurrent()) <= intval($this->getSplit())) {
                for ($i = 0; $i < intval($this->getSplit()); $i++) {
                    $currents[] = $i + 1;
                }
            } else {
                if (intval($this->getCurrent()) == intval($this->getPreviousSplit())) {
                    for ($i = intval($this->getPreviousSplit()) - intval($this->getSplit()); $i < intval($this->getPreviousSplit()); $i++) {
                        $currents[] = $i + 1;
                    }
                } else {
                    if (intval($this->getPreviousSplit()) == intval($this->getNextSplit())) {
                        for ($i = intval($this->getPages()) - intval($this->getSplit()); $i < intval($this->getPages()); $i++) {
                            $currents[] = $i + 1;
                        }
                    } else {
                        for ($i = intval($this->getPreviousSplit()); $i < intval($this->getNextSplit()); $i++) {
                            $currents[] = $i + 1;
                        }
                    }
                }
            }
        }
        $this->currents = $currents;
        return $this->currents;
    }

    /**
     * @return int
     */
    public function getPreviousSplit()
    {
        if (intval($this->getCurrent()) < intval($this->getSplit())) {
            $this->previousSplit = 1;
        } else {
            $this->previousSplit = intval((intval($this->getCurrent()) / intval($this->getSplit()))) * intval($this->getSplit());
        }
        return $this->previousSplit;
    }

    /**
     * @return int
     */
    public function getNextSplit()
    {
        if (intval($this->getCurrent()) < intval($this->getSplit())) {
            $this->nextSplit = intval($this->getSplit()) + 1;
        } else {
            $this->nextSplit = intval((intval($this->getCurrent()) / intval($this->getSplit()))) * intval($this->getSplit()) + intval($this->getSplit());
        }
        if (intval($this->nextSplit) >= intval($this->getPages())) {
            $this->nextSplit = intval((intval($this->getPages()) / intval($this->getSplit()))) * intval($this->getSplit());
        }
        return $this->nextSplit;
    }

    public function __construct($total = 1, $size = 10, $current = 1, $split = 10)
    {
        $this->setTotal($total);
        $this->setSize($size);
        $this->setCurrent($current);
        $this->setSplit($split);
        $this->pages = $this->getPages();
        $this->first = $this->getFirst();
        $this->last = $this->getLast();
        $this->previous = $this->getPrevious();
        $this->next = $this->getNext();
        $this->offset = $this->getOffset();
        $this->currents = $this->getCurrents();
        $this->previousSplit = $this->getPreviousSplit();
        $this->nextSplit = $this->getNextSplit();
    }

    public function toArray()
    {
        $result = [
            "current" => $this->getCurrent(),
            "first" => $this->getFirst(),
            "last" => $this->getLast(),
            "previous" => $this->getPrevious(),
            "next" => $this->getNext(),
            "offset" => $this->getOffset(),
            "size" => $this->getSize(),
            "pages" => $this->getPages(),
            "total" => $this->getTotal(),
            "split" => $this->getSplit(),
            "currents" => $this->getCurrents(),
            "previousSplit" => $this->getPreviousSplit(),
            "nextSplit" => $this->getNextSplit(),
        ];
        return $result;
    }
}