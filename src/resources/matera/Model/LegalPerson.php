<?php

namespace Warquia\Pix\resources\matera\Model;

/**
 *
 */
class LegalPerson
{
    /**
     * @var string
     */
    public string $externalIdentifier;
    /**
     * @var string
     */
    public string $clientType;
    /**
     * @var string
     */
    public string $accountType;
    /**
     * @var BillingAddress
     */
    public BillingAddress $billingAddress;
    /**
     * @var AdditionalDetailsCorporate
     */
    public AdditionalDetailsCorporate $additionalDetailsCorporate;
    /**
     * @var Client
     */
    public Client $client;
}

