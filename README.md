# Pacote integração PIXs

Simples gerenciador de PSPs PIX utilizando PHP

Este pacote proporciona integração inicial com o sistema da MATERA\FLAGSHIP, podendo ser expandido para incluir outros PSPs, conforme a documentação do Banco Central do Brasil.


<hr>

### Instalação

```php
composer require warquia/pix
```

### Configurações Iniciais

Configure os dados de PSP, Api e Certificado 


```php
$config = [
    'environment' => Constants::ENVIRONMENT_TEST,
    'psp_name' => Constants::PSP_NAME_MATERA,
    'client_id' => 'hml',
    'client_secret' => 'fictício-ezz3-1818-ip7c-fictício',
    'secret_key' => 'fictício-9X47-fictício-BE33-fictício',
    'certificate' => __DIR__ . '/hml.pem',
    'certificateKey' => __DIR__ . '/hml.key'
];
```
### Exemplos de Uso

```php
$psp = new \Warquia\Pix\Psp($config);
```
Para aprofundamento ou dúvidas utilize example



## Requisitos
Necessário PHP 7.1 ou superior

#### Licença
GPL-3.0-or-later
