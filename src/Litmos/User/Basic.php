<?php

namespace Litmos\User;

use Litmos\Service;

class Basic
{

    /**
     * @var string
     */
    private $user_id;

    /**
     * @var string
     */
    private $user_name;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;

    /**
     * @var Courses
     */
    private $courses;

    /**
     * @param Service $service
     * @param string  $user_id
     * @param string  $user_name
     * @param string  $first_name
     * @param string  $last_name
     */
    public function __construct(Service $service = null, $user_id, $user_name, $first_name, $last_name)
    {
        if ($user_id instanceof self) {
            $user_name  = $user_id->getUserName();
            $first_name = $user_id->getFirstName();
            $last_name  = $user_id->getLastName();
            $user_id    = $user_id->getUserId();
        }

        $this->user_id    = $user_id;
        $this->user_name  = $user_name;
        $this->first_name = $first_name;
        $this->last_name  = $last_name;

        $this->courses = isset($service) ? new Courses($service, $this) : null;
    }

    /**
     * @return Courses
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }
}
