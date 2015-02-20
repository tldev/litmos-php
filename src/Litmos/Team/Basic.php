<?php

namespace Litmos\Team;

use Litmos\Service;

class Basic
{

    /**
     * @var Service
     */
    private $service;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @param Service      $service
     * @param string|Basic $team_id
     * @param string       $team_name
     */
    public function __construct(Service $service, $team_id, $team_name)
    {
        if ($team_id instanceof self) {
            $team_name = $team_id->getName();
            $team_id   = $team_id->getId();
        }

        $this->service = $service;
        $this->id      = $team_id;
        $this->name    = $team_name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
