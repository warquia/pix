<?php

namespace Warquia\Pix\resources\matera\Model;

class LegalPerson
{
    public string $externalIdentifier;
    public string $clientType;
    public string $accountType;
    public BillingAddress $billingAddress;
    public AdditionalDetailsCorporate $additionalDetailsCorporate;
    public Client $client;
}

