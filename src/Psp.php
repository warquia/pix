<?php

namespace Warquia\Pix;

use Warquia\Pix\resources\matera\Matera;

class Psp /*extends PspAbstract*/
{
    protected $config;

    protected $client;

    protected $pspClass;

    protected $retornoToken;

    protected $pspName;

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

        if (is_null($this->retornoToken) or ($this->retornoToken == '')) {
            $this->retornoToken = $this->pspClass->generateToken();
        }
    }
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

    public function getClass()
    {
        return $this->pspClass;
    }
    public function getPspName()
    {
        return $this->pspName;
    }

    public function generateTxId(bool $clearIfen = true) : string
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