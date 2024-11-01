<?php

namespace Warquia\Pix\resources\matera;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Warquia\Pix\Constants;
use Warquia\Pix\Psp;
use Warquia\Pix\resources\matera\Model\ExternalIdentifier;
use Warquia\Pix\resources\matera\Model\LegalPerson;
use Warquia\Pix\ResponseDTO;

/**
 *
 */
class Matera
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
            $uriAuth = ConstantsMatera::CONNECTION_URL["AUTHENTICATION"]["PRODUCTION"];
        }

        return new Client([
            'base_uri' => $uriAuth,
        ]);
    }

    /**
     * @return array
     */
    public function initOptionsRequest()
    {
        return [
            'headers' => [],
            'cert' => $this->psp->config['certificate'],
            'ssl_key' => $this->psp->config['certificateKey'],
        ];
    }

    /**
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateToken()
    {
        $this->psp->resetOptionsRequest();
        $this->psp->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->psp->setFormParams('grant_type', 'client_credentials');
        $this->psp->setFormParams('client_id', $this->psp->config['client_id']);
        $this->psp->setFormParams('client_secret', $this->psp->config['client_secret']);

        try {
            $response = $this->psp->client->post(ConstantsMatera::URI_TOKEN, $this->psp->optionsRequest);
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
     * @param LegalPerson $pessoaJuridica
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createAccount(LegalPerson $pessoaJuridica): ResponseDTO
    {
        $hmacData = $pessoaJuridica->externalIdentifier . $pessoaJuridica->client->taxIdentifier->taxId;
        $hash = self::generateHmacSHA256($hmacData);
        try {
            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Accept', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);
            $this->psp->setHeader('Transaction-Hash', $hash);
            $this->psp->setBody(json_encode($pessoaJuridica));

            $response = $this->psp->client->post(ConstantsMatera::URI_ACCOUNTS, $this->psp->optionsRequest);
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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
        }
    }

    /**
     * @param string $accountId
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryAccount(string $accountId): ResponseDTO
    {
        $uri = ConstantsMatera::URI_ACCOUNTS . '/' . $accountId;
        $hash = self::generateHmacSHA256($accountId);
        try {
            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Accept', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);
            $this->psp->setHeader('Transaction-Hash', $hash);

            $response = $this->psp->client->get($uri, $this->psp->optionsRequest);
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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
        }
    }

    /**
     * @param string $accountId
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function inactiveAccount(string $accountId): ResponseDTO
    {
        $uri = ConstantsMatera::URI_ACCOUNTS . '/' . $accountId;
        $hash = self::generateHmacSHA256($accountId);
        try {
            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Accept', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);
            $this->psp->setHeader('Transaction-Hash', $hash);

            $response = $this->psp->client->delete($uri, $this->psp->optionsRequest);

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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
        }
    }

    /**
     * @param string $endPoint
     * @param string $accountId
     * @return string
     */
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

        $hmacData = 'post:/' . $uri . ':';
        $hash = self::generateHmacSHA256($hmacData);

        $body = ["externalIdentifier" => $externalIdentifier->externalIdentifier, "alias" => ["type" => $externalIdentifier->type]];
        try {

            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Accept', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);
            $this->psp->setHeader('Transaction-Hash', $hash);
            $this->psp->setBody(json_encode($body));
            $response = $this->psp->client->post($uri, $this->psp->optionsRequest);
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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
        }
    }

    /**
     * @param string $accountId
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function queryAliasAssociated(string $accountId): ResponseDTO
    {
        $uri = self::mountUriAlias(ConstantsMatera::URI_ACCOUNTS, $accountId);
        try {
            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Accept', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);

            $response = $this->psp->client->get($uri, $this->psp->optionsRequest);
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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
        }
    }

    /**
     * @param ExternalIdentifier $externalIdentifier
     * @return ResponseDTO
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteAlias(ExternalIdentifier $externalIdentifier): ResponseDTO
    {
        $uri = self::mountUriAlias(ConstantsMatera::URI_ACCOUNTS, $externalIdentifier->accountId) . '/' . $externalIdentifier->name;
        $hmacData = 'delete:/'. $uri;

        $hash = self::generateHmacSHA256($hmacData);
        try {
            $token = $this->psp->getToken();
            $this->psp->resetOptionsRequest();
            $this->psp->setHeader('Content-Type', 'application/json');
            $this->psp->setHeader('Authorization', 'Bearer ' . $token);
            $this->psp->setHeader('Transaction-Hash', $hash);

            $response = $this->psp->client->delete($uri, $this->psp->optionsRequest);
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
            return (new ResponseDTO(-1, $e->getMessage(), $e->getMessage()));
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
    private function generateHmacSHA256(string $data): string
    {
        $secretKey = $this->psp->config['secret_key'];
        return hash_hmac('sha256', $data, $secretKey, false);
    }
}
