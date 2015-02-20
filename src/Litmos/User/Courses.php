<?php

namespace Litmos\User;

use Litmos\Course;
use Litmos\Exception;
use Litmos\PagingSearch;
use Litmos\Service;
use Litmos\User;

class Courses
{

    /**
     * @var Service
     */
    private $service;

    /**
     * @var string
     */
    private $user_id;

    /**
     * @param Service           $litmos_service
     * @param string|Basic|User $user_id
     */
    public function __construct(Service $litmos_service, $user_id)
    {
        if ($user_id instanceof Basic) {
            $user_id = $user_id->getUserId();
        } elseif ($user_id instanceof User) {
            $user_id = $user_id->getUserId();
        }

        $this->service = $litmos_service;
        $this->user_id = $user_id;
    }

    /**
     * @param Course[]|string[] $courses
     * @throws Exception\InvalidArgumentException
     */
    public function add(array $courses)
    {
        if (empty($courses)) {
            return;
        }

        $course_ids = array();
        foreach ($courses as $course) {
            if (is_string($course)) {
                $course_ids[] = $course;
            } elseif ($course instanceof Course) {
                $course_ids[] = $course->getId();
            } else {
                throw new Exception\InvalidArgumentException('Unexpected type given for course id');
            }
        }

        $xml = new \SimpleXMLElement('<Courses/>');
        foreach ($course_ids as $course_id) {
            $course_node = $xml->addChild('Course');
            $course_node->addChild('Id', $course_id);
        }

        $xml_str = $xml->asXML();
        $this->service->post("/users/{$this->user_id}/courses", $xml_str);
    }

    /**
     * @param string|Course $course
     */
    public function remove($course)
    {
        if ($course instanceof Course) {
            $course = $course->getId();
        }

        $this->service->delete("/users/{$this->user_id}/courses/{$course}");
    }

    /**
     * @param $course_id
     */
    public function reset($course_id)
    {
        $this->service->put("/users/{$this->user_id}/courses/{$course_id}/reset", '');
    }

    /**
     * @param PagingSearch $ps
     *
     * @return Course[]
     */
    public function getAll(PagingSearch $ps = null)
    {
        $response = $this->service->get("/users/{$this->user_id}/courses", $ps);

        $xml = new \SimpleXMLElement($response);

        $courses      = array();
        $course_nodes = $xml->children();
        foreach ($course_nodes as $course_node) {
            $id        = (string)$course_node->Id;
            $code      = (string)$course_node->Code;
            $name      = (string)$course_node->Name;
            $active    = filter_var((string)$course_node->Active, FILTER_VALIDATE_BOOLEAN);
            $course    = new Course($id, $code, $name, $active);
            $courses[] = $course;
        }

        return $courses;
    }
}
