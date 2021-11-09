<?php

namespace App\Helpers;

use App\Http\Controllers\Api\V1\Blackbaud\AuthController;
use App\Models\BlackbaudToken;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GuzzleClient
{
    protected $header;
    protected $auth_type;

    public function __construct($header, $auth_type)
    {
        $this->header = $header;
        $this->auth_type = $auth_type;
    }

    /**
     * @param $url
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url)
    {
        $requestData = '';
        try {
            $client = new Client();
            Log::channel('migration')->info('client header');
            $requestData = $client->request('GET', $url, [
                'headers' => $this->header,

            ]);
        } catch (RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                /**
                 * if token expire than refresh the token
                 */
                AuthController::refreshAccessToken($this->auth_type);
                $client = new Client();
                $token = BlackbaudToken::where('auth_env', $this->auth_type)->first();
                if ($token) {
                    $this->header = array(
                        'bb-api-subscription-key' => config('project.blackbaud_auth_subscription_key'),
                        'Authorization' => 'Bearer '.$token->access_token
                    );
                }
                $requestData = $client->request('GET', $url, [
                    'headers' => $this->header,

                ]);
            }
        }
        return $requestData;
    }

}
