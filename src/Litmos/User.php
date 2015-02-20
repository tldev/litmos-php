<?php

namespace Litmos;

use Litmos\Exception;
use Litmos\User\Basic as UserBasic;
use Litmos\User\Courses as UserCourses;

class User
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
     * @var string
     */
    private $full_name;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $access_level;

    /**
     * @var bool
     */
    private $disable_messages;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $skype;

    /**
     * @var string
     */
    private $phone_work;

    /**
     * @var string
     */
    private $phone_mobile;

    /**
     * @var \DateTime
     */
    private $last_login;

    /**
     * @var string
     */
    private $login_key;

    /**
     * @var bool
     */
    private $skip_first_login;

    /**
     * @var bool
     */
    private $is_custom_username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var UserCourses
     */
    private $courses;

    /**
     * @param Service               $litmos_service
     * @param string|UserBasic|User $user_id
     * @param string                $user_name
     * @param string                $first_name
     * @param string                $last_name
     * @param string                $full_name
     * @param string                $email
     * @param string                $access_level
     * @param bool                  $disable_messages
     * @param bool                  $active
     * @param string                $skype
     * @param string                $phone_work
     * @param string                $phone_mobile
     * @param \DateTime             $last_login
     * @param string                $login_key
     * @param bool                  $skip_first_login
     */
    public function __construct(
        Service $litmos_service,
        $user_id,
        $user_name = null,
        $first_name = null,
        $last_name = null,
        $full_name = null,
        $email = null,
        $access_level = null,
        $disable_messages = null,
        $active = null,
        $skype = null,
        $phone_work = null,
        $phone_mobile = null,
        \DateTime $last_login = null,
        $login_key = null,
        $skip_first_login = null
    ) {

        if ($user_id instanceof UserBasic) {
            $user = $litmos_service->getUsers()->get($user_id->getUserId());
            $this->_copy($user);
        } elseif ($user_id instanceof User) {
            $this->_copy($user_id);
        } elseif (is_string($user_id) && is_null($user_name)) {
            $user = $litmos_service->getUsers()->get($user_id);
            $this->_copy($user);
        } else {
            $this->service          = $litmos_service;
            $this->user_id          = $user_id;
            $this->user_name        = $user_name;
            $this->first_name       = $first_name;
            $this->last_name        = $last_name;
            $this->full_name        = $full_name;
            $this->email            = $email;
            $this->access_level     = $access_level;
            $this->disable_messages = $disable_messages;
            $this->active           = $active;
            $this->skype            = $skype;
            $this->phone_work       = $phone_work;
            $this->phone_mobile     = $phone_mobile;
            $this->last_login       = $last_login;
            $this->login_key        = $login_key;
            $this->skip_first_login = $skip_first_login;
            $this->courses          = new UserCourses($litmos_service, $this);
        }

        // Check if user_name is an email address.
        $is_email                 = preg_match(
                "^\s*([a-zA-Z0-9_\-\.\+']+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)" .
                "|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)\s*$^",
                $user_name
            ) === 1;
        $this->is_custom_username = !$is_email;
    }

    /**
     * @param Service $service
     * @param string  $xml
     *
     * @return User
     */
    public static function FromXml(Service $service, $xml)
    {
        $xml = new \SimpleXMLElement($xml);

        $id               = (string)$xml->Id;
        $username         = (string)$xml->UserName;
        $first_name       = (string)$xml->FirstName;
        $last_name        = (string)$xml->LastName;
        $full_name        = (string)$xml->FullName;
        $email            = (string)$xml->Email;
        $access_level     = (string)$xml->AccessLevel;
        $disable_messages = filter_var((string)$xml->DisableMessages, FILTER_VALIDATE_BOOLEAN);
        $active           = filter_var((string)$xml->Active, FILTER_VALIDATE_BOOLEAN);
        $skype            = (string)$xml->Skype;
        $phone_work       = (string)$xml->PhoneWork;
        $phone_mobile     = (string)$xml->PhoneMobile;
        $last_login       = (string)$xml->LastLogin ? new \DateTime((string)$xml->LastLogin) : new \DateTime(-1);
        $login_key        = (string)$xml->LoginKey;
        $skip_first_login = filter_var((string)$xml->SkipFirstLogin, FILTER_VALIDATE_BOOLEAN);

        $user = new User(
            $service,
            $id,
            $username,
            $first_name,
            $last_name,
            $full_name,
            $email,
            $access_level,
            $disable_messages,
            $active,
            $skype,
            $phone_work,
            $phone_mobile,
            $last_login,
            $login_key,
            $skip_first_login
        );

        return $user;
    }

    /**
     * @return string
     */
    public function toXml()
    {
        $user_nodes = array(
            'Id'                 => $this->user_id,
            'UserName'           => $this->user_name,
            'FirstName'          => $this->first_name,
            'LastName'           => $this->last_name,
            'FullName'           => $this->full_name,
            'Email'              => $this->email,
            'AccessLevel'        => $this->access_level,
            'DisablesMessages'   => $this->disable_messages,
            'Active'             => $this->active,
            'Skype'              => $this->skype,
            'PhoneWork'          => $this->phone_work,
            'PhoneMobile'        => $this->phone_mobile,
            'LastLogin'          => $this->last_login->format(DATE_ISO8601),
            'LoginKey'           => $this->login_key,
            'IsCustomerUsername' => $this->is_custom_username,
            'Password'           => $this->password ? $this->password : '',
            'SkipFirstLogin'     => $this->skip_first_login
        );
        $xml        = new \SimpleXMLElement('<User/>');
        foreach ($user_nodes as $key => $value) {
            $xml->addChild($key, $value);
        }
        $xml_str = $xml->asXML();
        if (false === $xml_str) {
            throw new Exception\RuntimeException('Failed to convert user to xml');
        }
        return $xml_str;
    }

    /**
     * @return void
     */
    public function refresh()
    {
        $user = $this->service->getUsers()->get($this->user_id);
        $this->_copy($user);
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->service->getUsers()->update($this);
    }

    /**
     * @return string
     */
    public function getAccessLevel()
    {
        return $this->access_level;
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return UserCourses
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @return boolean
     */
    public function getDisableMessages()
    {
        return $this->disable_messages;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName($first_name)
    {
        if (!is_string($first_name) || $first_name === '') {
            throw new Exception\InvalidArgumentException('You cannot set an empty first name.');
        }
        if (strlen($first_name) > 100) {
            throw new Exception\InvalidArgumentException(
                'The first name specified is too long, maximum 100 characters.'
            );
        }
        $this->first_name = $first_name;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * @return boolean
     */
    public function getIsCustomUsername()
    {
        return $this->is_custom_username;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->last_login;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setLastName($last_name)
    {
        if (!is_string($last_name) || $last_name === '') {
            throw new Exception\InvalidArgumentException('You cannot set an empty last name.');
        }
        if (strlen($last_name) > 100) {
            throw new Exception\InvalidArgumentException(
                'The last name specified is too long, maximum 100 characters.'
            );
        }
        $this->last_name = $last_name;
    }

    /**
     * @return string
     */
    public function getLoginKey()
    {
        return $this->login_key;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPhoneMobile()
    {
        return $this->phone_mobile;
    }

    /**
     * @param string $phone_mobile
     * @throws Exception\InvalidArgumentException
     */
    public function setPhoneMobile($phone_mobile)
    {
        if (!is_null($phone_mobile) && strlen($phone_mobile) > 50) {
            throw new Exception\InvalidArgumentException('The value specified is too long, maximum 50 characters.');
        }

        $this->phone_mobile = $phone_mobile;
    }

    /**
     * @return string
     */
    public function getPhoneWork()
    {
        return $this->phone_work;
    }

    /**
     * @param string $phone_work
     * @throws Exception\InvalidArgumentException
     */
    public function setPhoneWork($phone_work)
    {
        if (!is_null($phone_work) && strlen($phone_work) > 50) {
            throw new Exception\InvalidArgumentException('The value specified is too long, maximum 50 characters.');
        }

        $this->phone_work = $phone_work;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return boolean
     */
    public function getSkipFirstLogin()
    {
        return $this->skip_first_login;
    }

    public function setSkipFirstLogin($skip_first_login)
    {
        $this->skip_first_login = (bool)$skip_first_login;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string $skype
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setSkype($skype)
    {
        if (!is_null($skype) && strlen($skype) > 100) {
            throw new Exception\InvalidArgumentException('The value specified is too long, maximum 100 characters.');
        }
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

    /**
     * @param string $user_name
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setUserName($user_name)
    {
        if (!is_string($user_name) || $user_name === '') {
            throw new Exception\InvalidArgumentException('You cannot set an empty user name.');
        }
        if (strlen($user_name) > 255) {
            throw new Exception\InvalidArgumentException(
                'The user name specified is too long, maximum 255 characters.'
            );
        }
    }

    /**
     * @param User $user
     * @return void
     */
    private function _copy(User $user)
    {
        $this->user_id          = $user->getUserId();
        $this->user_name        = $user->getUserName();
        $this->first_name       = $user->getFirstName();
        $this->last_name        = $user->getLastLogin();
        $this->full_name        = $user->getFullName();
        $this->email            = $user->getEmail();
        $this->access_level     = $user->getAccessLevel();
        $this->disable_messages = $user->getDisableMessages();
        $this->active           = $user->getActive();
        $this->skype            = $user->getSkype();
        $this->phone_work       = $user->getPhoneWork();
        $this->phone_mobile     = $user->getPhoneMobile();
        $this->last_login       = $user->getLastLogin();
        $this->login_key        = $user->getLoginKey();
        $this->skip_first_login = $user->getSkipFirstLogin();
        $this->courses          = new UserCourses($this->service, $this);
    }
}
