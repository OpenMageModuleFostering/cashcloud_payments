<?php namespace CashCloud\Api\Rest;

/**
 * Class Auth
 * @package CashCloud\Api
 */
/**
 * Class Auth
 * @package CashCloud\Api\Rest
 */
class Auth
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $deviceId = null;

    /**
     * Construct authentication
     *
     * @param string $email
     * @param string $password
     * @param string|null $deviceId
     */
    function __construct($email, $password, $deviceId = null)
    {
        $this->email = $email;
        $this->password = $password;
        if(is_null($deviceId)) {
            $this->deviceId = $this->generateDeviceId();
        } else {
            $this->deviceId = $deviceId;
        }
    }

    /**
     * Authorizes request
     *
     * @throws \CashCloud\Api\Exception\AuthException
     */
    public function authorizeRequest(Client $client, Request $request)
    {
        $request->setHeader("Authorization", $this->getAuthorizationToken($client));
    }

    /**
     * Returns authorization token
     *
     * @param Client $client
     * @throws \CashCloud\Api\Exception\AuthException
     * @return string
     */
    private function getAuthorizationToken(Client $client)
    {
        $deviceId = $this->getDeviceId();
        $username = $this->getUsername();
        $salt = $client->getSalt($client->createRequest());

        $passwordHash = md5(sha1($this->password, true), true);
        $hash = md5(sha1($username . $salt . $passwordHash, true));
        $token = 'Token ' . base64_encode("{$username}|{$deviceId}|" . $hash);

        return $token;
    }

    /**
     * Generates random device ID
     *
     * @return string
     */
    private function generateDeviceId()
    {
        return md5("cashcloud-api-".time());
    }

    /**
     * Returns username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Returns device ID
     *
     * @return string
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }
}
