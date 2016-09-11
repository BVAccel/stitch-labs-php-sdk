<?php

namespace StitchLabs;

use StitchLabs\Http\ArrayLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class StitchLabs
{
    /**
     * @var string URI all requests are sent to
     */
    protected $baseURI = 'https://api-pub.stitchlabs.com';

    /**
     * @var string URI a user visits to authorize an access token
     */
    protected $authUri = 'https://api-pub.stitchlabs.com/authorize';

    /**
     * @var string URI used to request an access token
     */
    protected $tokenUri = 'https://api-pub.stitchlabs.com/oauth/token';

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var boolean Determines if API calls should be logged
     */
    protected $debug = false;

    /**
     * @param array $config
     */
    public function __construct($config = array())
    {
        if (isset($config['clientId'])) $this->clientId = $config['clientId'];

        if (isset($config['clientSecret'])) $this->clientSecret = $config['clientSecret'];

        if (isset($config['redirectUri'])) $this->redirectUri = $config['redirectUri'];

        if (isset($config['debug'])) $this->debug = $config['debug'];
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->baseURI;
    }

    /**
     * @param string $url
     * @return string
     */
    public function setUrl($url)
    {
        $this->baseURI = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuth()
    {
        return $this->authURI;
    }

    /**
     * @param string $auth
     * @return string
     */
    public function setAuth($auth)
    {
        $this->authURI = $auth;
        return $this;
    }

    /**
     * @return string
     */
    public function getTokenUri()
    {
        return $this->tokenUri;
    }

    /**
     * @param string $tokenUri
     */
    public function setTokenUri($tokenUri)
    {
        $this->tokenUri = $tokenUri;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return string
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     * @return string
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    /**
     * @param string $redirectUri
     * @return string
     */
    public function setRedirectUri($redirectUri)
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationUrl()
    {
        $params = array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => 'full'
        );

        return $this->authURI . '?' . http_build_query($params);
    }

    /**
     * @param string $code
     * @return array
     * @throws InfusionsoftException
     */
    public function requestAccessToken($code)
    {
        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
        );

        $client = $this->getHttpClient();

        $tokenInfo = $client->request('POST', $this->tokenUri, ['body' => http_build_query($params), 'headers' => ['Content-Type' => 'application/x-www-form-urlencoded']]);

        $this->setToken(new Token(json_decode($tokenInfo, true)));

        return $this->getToken();
    }

    /**
     * @return Http\ClientInterface
     */
    public function getHttpClient()
    {
        if (!$this->httpClient) {
            return new Http\GuzzleHttpClient($this->debug, $this->getHttpLogAdapter());
        }

        return $this->httpClient;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param Http\ClientInterface $client
     */
    public function setHttpClient($client)
    {
        $this->httpClient = $client;
    }

    /**
     * @return Http\SerializerInterface
     */
    public function getSerializer()
    {
        if (!$this->serializer) {
            return new Http\InfusionsoftSerializer();
        }

        return $this->serializer;
    }

    /**
     * @param Http\SerializerInterface $serializer
     */
    public function setSerializer(Http\SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return LoggerInterface
     */
    public function getHttpLogAdapter()
    {
        // If a log adapter hasn't been set, we default to the null adapter
        if (!$this->httpLogAdapter) {
            $this->httpLogAdapter = new ArrayLogger();
        }

        return $this->httpLogAdapter;
    }

    /**
     * @param LoggerInterface $httpLogAdapter
     * @return \Infusionsoft\Infusionsoft
     */
    public function setHttpLogAdapter(LoggerInterface $httpLogAdapter)
    {
        $this->httpLogAdapter = $httpLogAdapter;

        return $this;
    }

    /**
     * @return array
     */
    public function getLogs()
    {
        if (!$this->debug) return array();

        $logger = $this->getHttpLogAdapter();
        if (!$logger instanceof ArrayLogger) return array();

        return $logger->getLogs();
    }

    /**
     * Checks if the current token is null or expired
     *
     * @return boolean
     */
    public function isTokenExpired()
    {
        $token = $this->getToken();

        if ( ! is_object($token)) {
            return true;
        }

        return $token->isExpired();
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $params
     * @throws TokenExpiredException
     * @return mixed
     */
    public function restfulRequest($method, $url, $params = array())
    {
        // Before making the request, we can make sure that the token is still
        // valid by doing a check on the end of life.
        $token = $this->getToken();
        if ($this->isTokenExpired())
        {
            throw new TokenExpiredException;
        }

        $client = $this->getHttpClient();
        $full_params = [];

        $full_params['headers'] = array(
            'Content-Type'  => 'application/json',
            'access_token' => $token->getAccessToken()
        );

        $response = (string) $client->request($method, $url, $full_params);
        return json_decode($response, true);
    }

    /**
     * @param boolean $debug
     * @return \Infusionsoft\Infusionsoft
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;

        return $this;
    }
}
