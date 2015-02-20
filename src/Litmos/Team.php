<?php

namespace Litmos;

use Litmos\Team\Basic as TeamBasic;
use Litmos\Team\Users as TeamUsers;

class Team
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
     * @var string
     */
    private $description;

    /**
     * @var TeamUsers
     */
    private $users;

    /**
     * @param Service $service
     * @param string  $team_id
     * @param string  $name
     * @param string  $desc
     */
    public function __construct(Service $service, $team_id, $name = null, $desc = null)
    {
        $this->service = $service;
        if ($team_id instanceof TeamBasic) {
            $team = $this->service->getTeams()->get($team_id->getId());
            $this->_copy($team);
        } elseif ($team_id instanceof Team) {
            $this->_copy($team_id);
        } elseif (is_string($team_id) && is_null($name)) {
            $team = $this->service->getTeams()->get($team_id);
            $this->_copy($team);
        } else {
            $this->id          = $team_id;
            $this->name        = $name;
            $this->description = $desc;
        }

        $this->users = new TeamUsers($this->service, $this);
    }

    /**
     * @param Service $service
     * @param string  $xml
     *
     * @return Team
     */
    public static function FromXml(Service $service, $xml)
    {
        $team_node = new \SimpleXMLElement($xml);

        $id   = (string)$team_node->Id;
        $name = (string)$team_node->Name;
        $desc = (string)$team_node->Description;

        $team = new Team($service, $id, $name, $desc);

        return $team;
    }

    /**
     * @return string
     * @throws Exception\RuntimeException
     */
    public function toXml()
    {
        $team_nodes = array(
            'Id'          => $this->id,
            'Name'        => $this->name,
            'Description' => $this->description
        );
        $xml        = new \SimpleXMLElement('<Team/>');
        array_walk_recursive($team_nodes, array($xml, 'addChild'));
        $xml_str = $xml->asXML();
        if (false === $xml_str) {
            throw new Exception\RuntimeException('Failed to convert team to xml');
        }
        return $xml_str;
    }

    /**
     * @return void
     */
    public function refresh()
    {
        $team = $this->service->getTeams()->get($this->id);
        $this->_copy($team);
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->service->getTeams()->update($this);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $desc
     * @return void
     */
    public function setDescription($desc)
    {
        if (is_string($desc) && strlen($desc) > 1000) {
            throw new Exception\InvalidArgumentException(
                'The description specified is too long, maximum 1000 characters.'
            );
        }
        $this->description = $desc;
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

    /**
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        if (!is_string($name) || $name === '') {
            throw new Exception\RuntimeException('You cannot set an empty team name.');
        }
        if (strlen($name) > 100) {
            throw new Exception\RuntimeException('The team name specified is too long, maximum 100 characters.');
        }
        $this->name = $name;
    }

    /**
     * @return TeamUsers
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Team $team
     * @return void
     */
    private function _copy(Team $team)
    {
        $this->id          = $team->getId();
        $this->name        = $team->getName();
        $this->description = $team->getDescription();
    }
}
