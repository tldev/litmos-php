<?php

namespace Litmos;

class Service
{

    /**
     * @var string
     */
    private $api_key;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $service_url;

    /**
     * @var Users
     */
    private $users;

    /**
     * @var Courses
     */
    private $courses;

    /**
     * @var Teams
     */
    private $teams;

    /**
     * @param string $api_key
     * @param string $source
     * @param string $service_url
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($api_key, $source = null, $service_url = null)
    {
        if (!is_string($api_key) || $api_key === '') {
            throw new Exception\InvalidArgumentException('An api key is required!');
        }
        if (!is_string($source) || $source === '') {
            throw new Exception\InvalidArgumentException('A source is required!');
        }
        if (!is_string($service_url) || $service_url === '') {
            throw new Exception\InvalidArgumentException('A service url is required');
        }

        $this->api_key     = $api_key;
        $this->source      = $source;
        $this->service_url = $service_url;

        $this->users   = new Users($this);
        $this->courses = new Courses($this);
        $this->teams   = new Teams($this);
    }

    /**
     * @param string       $url
     * @param PagingSearch $ps
     *
     * @return mixed
     */
    public function get($url, PagingSearch $ps = null)
    {
        return $this->_doRequest($url, 'GET', null, $ps);
    }

    /**
     * @param string $url
     * @param string $body
     *
     * @return mixed
     */
    public function put($url, $body)
    {
        return $this->_doRequest($url, 'PUT', $body);
    }

    /**
     * @param string $url
     * @param string $body
     *
     * @return mixed
     */
    public function post($url, $body)
    {
        return $this->_doRequest($url, 'POST', $body);
    }

    /**
     * @param string $url
     *
     * @return mixed
     */
    public function delete($url)
    {
        return $this->_doRequest($url, 'DELETE');
    }

    /**
     * @param string       $url
     * @param string       $method
     * @param null|string  $body
     * @param PagingSearch $ps
     *
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     * @throws Exception\Request
     */
    private function _doRequest($url, $method, $body = null, PagingSearch $ps = null)
    {
        $full_url = $this->_generateUrl($url, $ps);
        $ch       = curl_init($full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "Content-Type: text/xml"
            )
        );
        curl_setopt($ch, CURLOPT_USERAGENT, 'Litmos-API/1.0');

        if (empty($method)) {
            $method = 'GET';
        }

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case 'DELETE':
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
            default:
                throw new Exception\InvalidArgumentException(sprintf('Unrecognized http method: %s', $method));
        }

        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $output = curl_exec($ch);
        if (false === $output) {
            throw new Exception\RuntimeException(sprintf('Failed to execute cURL request, url: %s', $full_url));
        }
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!in_array($http_status, array(200, 201))) {
            throw new Exception\Request(
                sprintf('Bad response received from Litmos! http status: %s url: %s', $http_status, $full_url)
            );
        }

        return $output;
    }

    /**
     * @param string       $stub
     * @param PagingSearch $ps
     *
     * @return string
     * @throws Exception\RuntimeException
     */
    private function _generateUrl($stub, PagingSearch $ps = null)
    {
        $url = "{$this->service_url}{$stub}";

        $parsed_url = parse_url($url);
        if (!isset($parsed_url['scheme'])) {
            throw new Exception\RuntimeException('No http scheme was specified in the service_url');
        }
        if (!isset($parsed_url['host'])) {
            throw new Exception\RuntimeException('Host could not be determined from given service_url');
        }
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';

        $query_params = array();
        if (isset($parsed_url['query'])) {
            parse_str($parsed_url['query'], $query_params);
        }

        $base_url = "{$parsed_url['scheme']}://{$parsed_url['host']}{$path}";

        if (!$this->api_key) {
            throw new Exception\RuntimeException("You must specify an API key.");
        }

        $query_params['apikey'] = $this->api_key;
        $query_params['source'] = $this->source;

        if (!is_null($ps)) {
            $query_params['start']  = $ps->getStart();
            $query_params['limit']  = $ps->getLimit();
            $query_params['sort']   = $ps->getSort();
            $query_params['dir']    = $ps->getDirection();
            $query_params['search'] = $ps->getSearch();
        }

        $url = $base_url . '?' . http_build_query($query_params);

        return $url;
    }

    /**
     * @return Teams
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @return Courses
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @return Users
     */
    public function getUsers()
    {
        return $this->users;
    }
}
