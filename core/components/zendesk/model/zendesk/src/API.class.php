<?php
namespace Zendesk;

abstract class API
{
    /** @var \MODX\Revolution\modX $modx */
    public $modx;

    /** @var \Zendesk */
    protected $zendesk;

    /**
     * Endpoint constructor.
     * @param \Zendesk $zendesk
     */
    public function __construct(\Zendesk &$zendesk)
    {
        $this->zendesk =& $zendesk;
        $this->modx =& $zendesk->modx;
    }
    private function getToken(): string
    {
        $user = $this->zendesk->getOption('user');
        $token = $this->zendesk->getOption('token');
        return base64_encode($user.'/token:'.$token);
    }
    private function getURL($endpoint): string
    {
        $domain = $this->zendesk->getOption('domain');
        $endpoint = ltrim($endpoint, '/');
        return "https://$domain.zendesk.com/api/v2/$endpoint";
    }
    public function curl(string $endpoint, string $type, array $fields = []): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getURL($endpoint));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic '. $this->getToken(),
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
