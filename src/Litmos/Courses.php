<?php

namespace Litmos;

class Courses
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
     * @return Course[]
     */
    public function getAll(PagingSearch $ps = null)
    {
        $response = $this->service->get('/courses', $ps);

        $xml = new \SimpleXMLElement($response);

        $courses      = array();
        $course_nodes = $xml->children();

        foreach ($course_nodes as $course_node) {
            $id     = (string)$course_node->Id;
            $code   = (string)$course_node->Code;
            $name   = (string)$course_node->Name;
            $active = filter_var((string)$course_node->Active, FILTER_VALIDATE_BOOLEAN);

            $course    = new Course($id, $code, $name, $active);
            $courses[] = $course;
        }

        return $courses;
    }
}
