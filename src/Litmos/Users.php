<?php

namespace Litmos;

use Litmos\User\Basic as UserBasic;

class Users
{

    /**
     * @var Service
     */
    private $service;

    public function __construct(Service $litmos_service)
    {
        $this->service = $litmos_service;
    }

    /**
     * @param PagingSearch $ps
     *
     * @return UserBasic[]
     */
    public function getAll(PagingSearch $ps = null)
    {
        $response = $this->service->get('/users', $ps);

        $xml         = new \SimpleXMLElement($response);
        $users_nodes = $xml->children();

        $users = array();
        foreach ($users_nodes as $user_node) {
            $id         = (string)$user_node->Id;
            $username   = (string)$user_node->UserName;
            $first_name = (string)$user_node->FirstName;
            $last_name  = (string)$user_node->LastName;
            $users[]    = new UserBasic($this->service, $id, $username, $first_name, $last_name);
        }

        return $users;
    }

    /**
     * @param string|UserBasic $user_id
     * @return User
     */
    public function get($user_id)
    {
        if ($user_id instanceof UserBasic) {
            $user_id = $user_id->getUserId();
        }

        if (empty($user_id)) {
            throw new Exception\InvalidArgumentException('No User Id was specified.');
        }

        $response = $this->service->get("/users/{$user_id}");

        return User::FromXml($this->service, $response);
    }

    /**
     * @param string $user_name
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @param string $skype
     * @param string $phone_work
     * @param string $phone_mobile
     * @param bool   $skip_first_login
     *
     * @return User
     */
    public function create(
        $user_name,
        $first_name,
        $last_name,
        $email = '',
        $skype = '',
        $phone_work = '',
        $phone_mobile = '',
        $skip_first_login = false
    ) {
        $user = new User(
            $this->service,
            '',
            $user_name,
            $first_name,
            $last_name,
            '',
            $email,
            'Learner',
            false,
            true,
            $skype,
            $phone_work,
            $phone_mobile,
            new \DateTime(),
            '',
            $skip_first_login
        );

        $req_xml = $user->toXml();

        $rep_xml = $this->service->post('/users', $req_xml);

        return User::FromXml($this->service, $rep_xml);
    }

    /**
     * @param User $user
     */
    public function update(User $user)
    {
        $xml = $user->toXml();

        $this->service->put("/users/{$user->getUserId()}", $xml);
    }
}
