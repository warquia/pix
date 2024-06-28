<?php

namespace Warquia\Pix\resources\matera\Model;

/**
 *
 */
class Client {
    /**
     * @var string
     */
    public string $name;
    /**
     * @var string
     */
    public string $socialName;
    /**
     * @var string
     */
    public string $email;
    /**
     * @var TaxIdentifier
     */
    public TaxIdentifier $taxIdentifier; // Objeto TaxIdentifier
    /**
     * @var MobilePhone
     */
    public MobilePhone $mobilePhone; // Objeto MobilePhone
    /**
     * @var MailAddress
     */
    public MailAddress $mailAddress;
}