<?php

namespace Warquia\Pix\resources\matera;

class ConstantsMatera
{
    const CONNECTION_URL = [
        "AUTHENTICATION" => [
            "PRODUCTION" => "https://mtls-mp.prd.flagship.maas.link",
            "HOMOLOGATION" => "https://mtls-mp.hml.flagship.maas.link"
        ]
    ];

    const URI_TOKEN = "auth/realms/Matera/protocol/openid-connect/token";
    const URI_ACCOUNTS = "v1/accounts";

    const CLIENT_TYPE = [
        "PERSON" => "PERSON",
        "CORPORATE" => "CORPORATE",
        "FOREIGNER" => "FOREIGNER"
    ];
    const ACCOUNT_TYPE = [
        "ORDINARY" => "ORDINARY",
        "OVERDRAFT_PROTECTED" => "OVERDRAFT_PROTECTED",
        "COMMON" => "COMMON",
        "UNLIMITED_ORDINARY" => "UNLIMITED_ORDINARY"
    ];

    const DOCUMENT_TYPE_PICTURE = "PICTURE";
    const DOCUMENT_TYPE_IDENTITY_FRONT = "IDENTITY_FRONT";
    const DOCUMENT_TYPE_IDENTITY_BACK = "IDENTITY_BACK";
    const DOCUMENT_TYPE_CNH = "CNH";
    const DOCUMENT_TYPE_UNKNOWN = "UNKNOWN";
    
    const ALIAS_TYPE_EVP = 'EVP';
}
