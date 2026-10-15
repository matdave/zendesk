<?php
namespace Zendesk\API;

abstract class API
{
    /** @var \MODX\Revolution\modX $modx */
    public $modx;

    /** @var \Zendesk */
    protected $zendesk;

    /** @var string|null OAuth access token shared by API instances in this request */
    private static $accessToken;

    /** @var int Unix timestamp when the cached access token expires */
    private static $accessTokenExpires = 0;

    /**
     * Endpoint constructor.
     * @param \Zendesk $zendesk
     */
    public function __construct(\Zendesk &$zendesk)
    {
        $this->zendesk =& $zendesk;
        $this->modx =& $zendesk->modx;
    }

    /**
     * Obtain an OAuth access token using the client credentials flow, reusing the
     * cached token until it expires.
     */
    private function getAccessToken(): ?string
    {
        if (self::$accessToken !== null && self::$accessTokenExpires > time()) {
            return self::$accessToken;
        }
        $domain = $this->zendesk->getOption('domain');
        $payload = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $this->zendesk->getOption('client_id'),
            'client_secret' => $this->zendesk->getOption('client_secret'),
            'scope' => $this->zendesk->getOption('scope', [], 'read write'),
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://$domain.zendesk.com/oauth/tokens");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode((string) $result, true);
        if (empty($data['access_token'])) {
            $this->modx->log(\xPDO::LOG_LEVEL_ERROR,
                'Zendesk: unable to obtain OAuth access token (HTTP ' . $httpCode . ', curl: ' . $curlError . '). Response: ' . substr((string) $result, 0, 500));
            return null;
        }
        $expiresIn = isset($data['expires_in']) ? (int) $data['expires_in'] : 1800;
        self::$accessToken = $data['access_token'];
        self::$accessTokenExpires = time() + $expiresIn;
        return self::$accessToken;
    }

    private function getURL($endpoint): string
    {
        $domain = $this->zendesk->getOption('domain');
        $endpoint = ltrim($endpoint, '/');
        return "https://$domain.zendesk.com/api/v2/$endpoint";
    }

    public function curl(string $endpoint, string $type, array $fields = []): array
    {
        $token = $this->getAccessToken();
        if ($token === null) {
            return [
                'result' => null,
                'error' => 'Unable to obtain OAuth access token',
                'code' => 0,
            ];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getURL($endpoint));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($fields)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        }
        $response = [
            'result' => curl_exec($ch),
            'error' => curl_error($ch),
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE)
        ];
        curl_close($ch);
        if (!empty($response['result'])) {
            $response['result'] = json_decode($response['result']);
        }
        return $response;
    }
}
