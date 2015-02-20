<?php

namespace Litmos;

class Course
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $active;

    /**
     * @param string $course_id
     * @param string $course_code
     * @param string $course_name
     * @param bool   $course_active
     */
    public function __construct($course_id, $course_code, $course_name, $course_active)
    {
        $this->id     = $course_id;
        $this->code   = $course_code;
        $this->name   = $course_name;
        $this->active = $course_active;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
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
