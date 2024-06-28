<?php

namespace Warquia\Pix;

use Warquia\Pix\resources\matera\Matera;

/**
 *
 */
class Psp
{
    /**
     * @var array
     */
    public $config;

    /**
     * @var \GuzzleHttp\Client
     */
    public $client;

    /**
     * @var Matera|null
     */
    public $pspClass;

    /**
     * @var array|\Psr\Http\Message\ResponseInterface
     */
    protected $retornoToken;

    /**
     * @var mixed
     */
    protected $pspName;

    /**
     * @var
     */
    public $optionsRequest;

    /**
     * @param array $config
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->pspName = $this->config['psp_name'];

        switch ($this->pspName) {
            case Constants::PSP_NAME_MATERA:
                $this->pspClass = new Matera($this);
                break;
            case Constants::PSP_NAME_SICOOB:
                $this->pspClass = null;
                break;
            default:
                throw new \Exception('Configuração não reconhecida');
        }

        $this->client = $this->pspClass->getClient();
        $optionsRequest = $this->pspClass->initOptionsRequest();

        if (is_null($this->retornoToken) or ($this->retornoToken == '')) {
            $this->retornoToken = $this->pspClass->generateToken();
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setHeader(string $key, string $value) {
        $this->optionsRequest['headers'][$key] = $value;
    }

    /**
     * @param bool $initToDefault
     * @return void
     */
    public function resetOptionsRequest(bool $initToDefault = true) {
        if ($initToDefault) {
            $this->optionsRequest = $this->pspClass->initOptionsRequest();
        } else {
            $this->optionsRequest = [];
        }
    }

    /**
     * @param string $body
     * @return void
     */
    public function setBody(string $body) {
        $this->optionsRequest['body'] = $body;
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setFormParams(string $key, string $value) {
        $this->optionsRequest['form_params'][$key] = $value;
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getToken()
    {
        if (is_null($this->retornoToken) or ($this->retornoToken == '')) {
            $this->retornoToken = $this->pspClass->generateToken();
        }

        if (isset($this->retornoToken['error'])) {
            throw new \Exception($this->retornoToken['error'] . " : " . ( isset($this->retornoToken['error_description']) ? $this->retornoToken['error_description'] : ""));
        }

        return $this->retornoToken['access_token'];
    }

    /**
     * @return Matera|null
     */
    public function getClass()
    {
        return $this->pspClass;
    }

    /**
     * @return mixed
     */
    public function getPspName()
    {
        return $this->pspName;
    }

    /**
     * @param bool $clearIfen
     * @return string
     */
    public static function generateTxId(bool $clearIfen = true) : string
    {
        if (function_exists('com_create_guid') === true) {
            $txId = trim(com_create_guid(), '{}');
            return preg_replace('/[^a-zA-Z0-9]/', '', $txId);
        }

        $txId = sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );

        if ($clearIfen) {
            $txId = preg_replace('/[^a-zA-Z0-9]/', '', $txId);
        }

        return $txId;
    }
}