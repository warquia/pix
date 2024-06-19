<?php

namespace Warquia\Pix\resources\matera;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Warquia\Pix\Constants;
use Warquia\Pix\Psp;
use Warquia\Pix\resources\matera\Model\ExternalIdentifier;
use Warquia\Pix\resources\matera\Model\PessoaJuridica;
use Warquia\Pix\ResponseDTO;

class Matera extends Psp
{
    /**
     * @var Psp
     */
    private $psp;

    /**
     * @param Psp $psp
     */
    public function __construct(Psp $psp)
    {
        $this->psp = $psp;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if ($this->psp->config["environment"] == Constants::ENVIRONMENT_TEST) {
            $uriAuth = ConstantsMatera::CONNECTION_URL["AUTHENTICATION"]["HOMOLOGATION"];
        } elseif ($this->psp->config["environment"] == Constants::ENVIRONMENT_PRODUCTION) {
            $uriAuth = ConstantsMatera::CONNECTION_URL["AUTHENTICATION"]["ENVIRONMENT_PRODUCTION"];
        }

        return new Client([
            'base_uri' => $uriAuth,
        ]);
    }

    /**
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateToken()
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->psp->config['client_id'],
                'client_secret' => $this->psp->config['client_secret'],
            ],
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];

        try {
            $response = $this->psp->client->post(ConstantsMatera::URI_TOKEN, $options);

            return (array)json_decode($response->getBody()->getContents());
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = json_decode($response->getBody()->getContents());
            if ($responseBodyAsString == '') {
                return ($response);
            }
            return (array)($responseBodyAsString);
        } catch (\Exception $e) {
            $response = $e->getMessage();
            return ['error' => $response];
        }
    }

    /**
     * @param PessoaJuridica $pessoaJuridica
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createAccount(PessoaJuridica $pessoaJuridica): ResponseDTO
    {
        $hmacData = $pessoaJuridica->externalIdentifier . $pessoaJuridica->client->taxIdentifier->taxId;
        $hash = self::generateHmacSHA256($hmacData);
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->psp->getToken(),
                'Transaction-Hash' => $hash,
            ],
            'body' => json_encode($pessoaJuridica),
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];

        try {
            $response = $this->psp->client->post(ConstantsMatera::URI_ACCOUNTS, $options);
            return (new ResponseDTO($response->getStatusCode(),
                                    $response->getReasonPhrase(),
                                    (array)json_decode($response->getBody()->getContents())));

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = json_decode($response->getBody()->getContents());

            if ($responseBodyAsString <> '') {
                $response = $responseBodyAsString;
            }
            return (new ResponseDTO($e->getCode(), $e->getMessage(), $response));
        } catch (\Exception $e) {
            return (new ResponseDTO(-1, 'error', $e->getMessage()));
        }
    }

    private function mountUriAlias(string $endPoint, string $accountId): string
    {
        return $endPoint . '/' . $accountId . '/aliases';
    }

    /**
     * @param ExternalIdentifier $externalIdentifier
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function aliasRegistration(ExternalIdentifier $externalIdentifier): ResponseDTO
    {
        $uri = self::mountUriAlias(ConstantsMatera::URI_ACCOUNTS, $externalIdentifier->accountId);

        $hmacData = 'post:/'. $uri . ':';
        $hash = self::generateHmacSHA256($hmacData);

        $body = ["externalIdentifier" => $externalIdentifier->externalIdentifier, "alias" => ["type"=>$externalIdentifier->type]];
        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->psp->getToken(),
                'Transaction-Hash' => $hash,
            ],
            'body' => json_encode($body),
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];

        try {
            $response = $this->psp->client->post($uri, $options);
            return (new ResponseDTO($response->getStatusCode(),
                $response->getReasonPhrase(),
                (array)json_decode($response->getBody()->getContents())));

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = json_decode($response->getBody()->getContents());

            if ($responseBodyAsString <> '') {
                $response = $responseBodyAsString;
            }
            return (new ResponseDTO($e->getCode(), $e->getMessage(), $response));
        } catch (\Exception $e) {
            return (new ResponseDTO(-1, 'error', $e->getMessage()));
        }
    }
    public function queryAliasAssociated(string $accountId): ResponseDTO
    {
        $uri = self::mountUriAlias(ConstantsMatera::URI_ACCOUNTS, $accountId);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->psp->getToken(),
            ],
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];

        try {
            $response = $this->psp->client->get($uri, $options);
            return (new ResponseDTO($response->getStatusCode(),
                $response->getReasonPhrase(),
                (array)json_decode($response->getBody()->getContents())));

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = json_decode($response->getBody()->getContents());

            if ($responseBodyAsString <> '') {
                $response = $responseBodyAsString;
            }
            return (new ResponseDTO($e->getCode(), $e->getMessage(), $response));
        } catch (\Exception $e) {
            return (new ResponseDTO(-1, 'error', $e->getMessage()));
        }
    }

    public function deleteAlias(ExternalIdentifier $externalIdentifier): ResponseDTO
    {
        $uri = self::mountUriAlias(ConstantsMatera::URI_ACCOUNTS, $externalIdentifier->accountId);
        //$hmacData = 'delete:/'. $uri . '/' . $externalIdentifier->externalIdentifier;
        //$hash = self::generateHmacSHA256($hmacData);

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->psp->getToken(),
                //'Transaction-Hash' => $hash,
            ],
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];

        try {
            $response = $this->psp->client->delete($uri, $options);
            return (new ResponseDTO($response->getStatusCode(),
                $response->getReasonPhrase(),
                (array)json_decode($response->getBody()->getContents())));

        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = json_decode($response->getBody()->getContents());

            if ($responseBodyAsString <> '') {
                $response = $responseBodyAsString;
            }
            return (new ResponseDTO($e->getCode(), $e->getMessage(), $response));
        } catch (\Exception $e) {
            return (new ResponseDTO(-1, 'error', $e->getMessage()));
        }
    }

    /**
     * @param $file
     * @return string
     */
    public static function fileToBase64($file): string
    {
        if (!file_exists($file)) {
            return '';
        }

        return base64_encode(file_get_contents($file));
    }

    /**
     * @param string $data
     * @param string $secretKey
     * @return string
     */
    private function generateHmacSHA256(string $data) : string
    {
        $secretKey = $this->psp->config['secret_key'];
        return hash_hmac('sha256', $data, $secretKey, false);
    }

}