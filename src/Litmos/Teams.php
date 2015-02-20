<?php

namespace Litmos;

use Litmos\Team\Basic as TeamBasic;
use Litmos\Team\Basic;

class Teams
{

    /**
     * @var Service
     */
    private $service;

    /**
     * @param Service $litmos_service
     */
    public function __construct(Service $litmos_service)
    {
        $this->service = $litmos_service;
    }

    /**
     * @param PagingSearch $ps
     *
     * @return Basic[]
     */
    public function getAll(PagingSearch $ps = null)
    {
        $response = $this->service->get('/teams', $ps);

        $xml = new \SimpleXMLElement($response);

        $teams      = array();
        $team_nodes = $xml->children();

        foreach ($team_nodes as $team_node) {
            $id   = (string)$team_node->Id;
            $name = (string)$team_node->Name;

            $team    = new Basic($this->service, $id, $name);
            $teams[] = $team;
        }

        return $teams;
    }

    /**
     * @param string|Basic $team_id
     *
     * @return Team
     * @throws Exception\InvalidArgumentException
     */
    public function get($team_id)
    {
        if ($team_id instanceof TeamBasic) {
            $team_id = $team_id->getId();
        }

        if (!is_string($team_id) || $team_id === '') {
            throw new Exception\InvalidArgumentException('No Team Id was specified.');
        }

        $response = $this->service->get("/teams/{$team_id}");

        return Team::FromXml($this->service, $response);
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Team
     */
    public function create($name, $description)
    {
        $team    = new Team($this->service, '', $name, $description);
        $req_xml = $team->toXml();

        $rep_xml = $this->service->post('/teams', $req_xml);

        return Team::FromXml($this->service, $rep_xml);
    }

    /**
     * @param Team $team
     */
    public function update(Team $team)
    {
        $xml = $team->toXml();

        $this->service->put("/teams/{$team->getId()}", $xml);
    }
}
