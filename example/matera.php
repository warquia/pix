<?php

require '../vendor/autoload.php';

use Warquia\Pix\Constants;
use Warquia\Pix\resources\matera\ConstantsMatera;
use Warquia\Pix\resources\matera\Matera;
use Warquia\Pix\resources\matera\Model\LegalPerson;

$config = [
    'environment' => Constants::ENVIRONMENT_TEST,
    'psp_name' => Constants::PSP_NAME_MATERA,
    'client_id' => 'hml',
    'client_secret' => 'fictício-ezz3-1818-ip7c-fictício',
    'secret_key' => 'fictício-9X47-fictício-BE33-fictício',
    'certificate' => __DIR__ . '/hml.pem',
    'certificateKey' => __DIR__ . '/hml.key',
];

$psp = new \Warquia\Pix\Psp($config);
if ($psp->getClass() instanceof Matera) {
    $pj = (new LegalPerson());
    $pj->externalIdentifier = \Warquia\Pix\Psp::generateTxId();
    $pj->clientType = ConstantsMatera::CLIENT_TYPE["CORPORATE"];
    $pj->accountType = ConstantsMatera::ACCOUNT_TYPE["UNLIMITED_ORDINARY"];
    $pj->billingAddress = (new \Warquia\Pix\resources\matera\Model\BillingAddress());
    $pj->billingAddress->logradouro = 'Rua Sacramento';
    $pj->billingAddress->numero = '15';
    $pj->billingAddress->complemento = 'Casa';
    $pj->billingAddress->bairro = 'Centro';
    $pj->billingAddress->cidade = 'São Paulo';
    $pj->billingAddress->estado = 'SP';
    $pj->billingAddress->cep = '13720-000';
    $pj->billingAddress->pais = 'BRA';

    $pj->additionalDetailsCorporate = (new \Warquia\Pix\resources\matera\Model\AdditionalDetailsCorporate());
    $pj->additionalDetailsCorporate->companyName = 'Nome da Empresa';
    $pj->additionalDetailsCorporate->businessLine = 47;
    $pj->additionalDetailsCorporate->establishmentForm = '1';
    $pj->additionalDetailsCorporate->establishmentDate = '1990-05-29';

    $qtdRepres = 1;
    for ($i = 1; $i <= $qtdRepres; $i++) {

        //Foto da Pessoa
        $documentPicture = (new \Warquia\Pix\resources\matera\Model\Document());
        $documentPicture->content = Matera::fileToBase64('foto.png');
        $documentPicture->type = ConstantsMatera::DOCUMENT_TYPE_PICTURE;

        //Identidade Frente
        $documentFront = (new \Warquia\Pix\resources\matera\Model\Document());
        $documentFront->content = Matera::fileToBase64('fotorgfrente.png');
        $documentFront->type = ConstantsMatera::DOCUMENT_TYPE_IDENTITY_FRONT;

        //Identidade Atras
        $documentBack = (new \Warquia\Pix\resources\matera\Model\Document());
        $documentBack->content = Matera::fileToBase64('fotorgverso.png');
        $documentBack->type = ConstantsMatera::DOCUMENT_TYPE_IDENTITY_BACK;

        $representative = (new \Warquia\Pix\resources\matera\Model\Representative());
        $representative->name = 'Representante 1';
        $representative->email = 'representante.pj@mp.com.br';
        $representative->documents = [$documentPicture, $documentFront, $documentBack];

        //Dados da Identidade
        $representative->taxIdentifier = (new \Warquia\Pix\resources\matera\Model\TaxIdentifier());
        $representative->taxIdentifier->taxId = '13585366864';
        $representative->taxIdentifier->country = 'BRA';

        //Telefone
        $representative->mobilePhone = (new \Warquia\Pix\resources\matera\Model\MobilePhone());
        $representative->mobilePhone->country = 'BRA';
        $representative->mobilePhone->phoneNumber = '12922223893';

        //Endereço
        $representative->mailAddress = (new \Warquia\Pix\resources\matera\Model\MailAddress());
        $representative->mailAddress->logradouro = 'Rua Fernando de Albuquerque';
        $representative->mailAddress->numero = '88';
        $representative->mailAddress->bairro = 'Consolação';
        $representative->mailAddress->cidade = 'São Paulo';
        $representative->mailAddress->estado = 'SP';
        $representative->mailAddress->cep = '01309030';
        $representative->mailAddress->pais = 'BRA';
        $representative->mother = 'Mãe do Representante';
        $representative->birthDate = '1990-05-28';

        $pj->additionalDetailsCorporate->representatives[] = $representative;
    }

    $pj->client = (new \Warquia\Pix\resources\matera\Model\Client());
    $pj->client->name = 'Pessoa Jurídica';
    $pj->client->email = 'pessoajuridica@mp.com.br';
    $pj->client->taxIdentifier = (new \Warquia\Pix\resources\matera\Model\TaxIdentifier());
    $pj->client->taxIdentifier->taxId = "18181808000104";
    $pj->client->taxIdentifier->country = "BRA";
    $pj->client->mobilePhone = (new \Warquia\Pix\resources\matera\Model\MobilePhone());
    $pj->client->mobilePhone->country = "BRA";
    $pj->client->mobilePhone->phoneNumber = "12922223893";

    ##CRIANDO UMA CONTA
    $account = $psp->getClass()->createAccount($pj);

    if ($account->code == 200) {
        $account_id = json_decode(json_encode($account->contents))->data->account->accountId;

        //##CRIANDO CHAVE PIX
        $chaveIdentifier = (new \Warquia\Pix\resources\matera\Model\ExternalIdentifier());
        $chaveIdentifier->accountId = $account_id;
        $chaveIdentifier->externalIdentifier = \Warquia\Pix\Psp::generateTxId(false);
        $chaveIdentifier->type = ConstantsMatera::ALIAS_TYPE_EVP;
        $account_alias = $psp->getClass()->aliasRegistration($chaveIdentifier);

        ##Consultando chaves PIXs
        $search_account_alias = $psp->getClass()->queryAliasAssociated($account_id);
    }
}