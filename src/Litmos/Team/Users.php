<?php

namespace Litmos\Team;

use Litmos\PagingSearch;
use Litmos\Service;
use Litmos\Team;
use Litmos\User\Basic as UserBasic;

class Users
{

    /**
     * @var Service
     */
    private $service;

    /**
     * @var string
     */
    private $team_id;

    /**
     * @param Service           $service
     * @param string|Basic|Team $team_id
     */
    public function __construct(Service $service, $team_id)
    {
        $this->service = $service;
        if ($team_id instanceof Basic) {
            $team_id = $team_id->getId();
        } elseif ($team_id instanceof Team) {
            $team_id = $team_id->getId();
        }

        $this->team_id = $team_id;
    }

    /**
     * @param UserBasic[]|string[] $users
     */
    public function add(array $users)
    {
        if (empty($users)) {
            return;
        }

        $user_ids = array();
        foreach ($users as $user) {
            if (is_string($user)) {
                $user_ids[] = $user;
            } elseif ($user instanceof UserBasic) {
                $user_ids[] = $user->getUserId();
            }
        }

        $xml = new \SimpleXMLElement('<Users/>');

        foreach ($user_ids as $user_id) {
            $user_node = $xml->addChild('User');
            $user_node->addChild('Id', $user_id);
        }

        $xml_str = $xml->asXML();

        $this->service->post("/teams/{$this->team_id}/users", $xml_str);
    }

    /**
     * @param string|UserBasic $user_id
     * @return void
     */
    public function remove($user_id)
    {
        if ($user_id instanceof UserBasic) {
            $user_id = $user_id->getUserId();
        }

        $this->service->delete("/teams/{$this->team_id}/users/{$user_id}");
    }

    /**
     * @param PagingSearch $ps
     *
     * @return UserBasic[]
     */
    public function getAll(PagingSearch $ps = null)
    {
        $response = $this->service->get("/teams/{$this->team_id}/users", $ps);

        $xml = new \SimpleXMLElement($response);

        $users      = array();
        $user_nodes = $xml->children();

        foreach ($user_nodes as $user_node) {
            $id         = (string)$user_node->Id;
            $user_name  = (string)$user_node->UserName;
            $first_name = (string)$user_node->FirstName;
            $last_name  = (string)$user_node->LastName;

            $user    = new UserBasic($this->service, $id, $user_name, $first_name, $last_name);
            $users[] = $user;
        }

        return $users;
    }
}
