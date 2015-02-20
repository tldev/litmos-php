<?php

namespace Litmos;

use Litmos\Exception;

class PagingSearch
{

    const DIRECTION_ASCENDING  = 'ASC';
    const DIRECTION_DESCENDING = 'DESC';

    /**
     * @var array
     */
    public static $directions = array(
        self::DIRECTION_ASCENDING,
        self::DIRECTION_DESCENDING
    );

    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var string
     */
    private $sort;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var string
     */
    private $search;

    /**
     * @param int    $start
     * @param int    $limit
     * @param string $sort
     * @param string $direction
     * @param null   $search
     */
    public function __construct($start, $limit, $sort, $direction = self::DIRECTION_ASCENDING, $search = null)
    {
        if (!is_int($start)) {
            throw new Exception\InvalidArgumentException('start must be an integer.');
        }
        if (!is_int($limit)) {
            throw new Exception\InvalidArgumentException('limit must be an integer.');
        }
        if ($limit > 1000) {
            throw new Exception\InvalidArgumentException('The upper limit is 1000.');
        }
        if (!in_array($direction, self::$directions)) {
            throw new Exception\InvalidArgumentException(sprintf('Invalid sort direction provided: %s', $direction));
        }
        if (!is_null($search)) {
            if (!is_string($search)) {
                throw new Exception\InvalidArgumentException('search must be a string');
            }
            if (strlen($search) > 50) {
                throw new Exception\InvalidArgumentException('Search string cannot be longer than 50 characters.');
            }
        }

        $this->start     = $start;
        $this->limit     = $limit;
        $this->sort      = $sort;
        $this->direction = $direction;
        $this->search    = $search;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getStart()
    {
        return $this->start;
    }
}
