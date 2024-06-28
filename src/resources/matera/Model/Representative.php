<?php

namespace Warquia\Pix\resources\matera\Model;

/**
 *
 */
class Representative {
    /**
     * @var string
     */
    public string $name;
    /**
     * @var string
     */
    public string $email;
    /**
     * @var array
     */
    public array $documents; // Array of Document
    /**
     * @var TaxIdentifier
     */
    public TaxIdentifier $taxIdentifier;
    /**
     * @var MobilePhone
     */
    public MobilePhone $mobilePhone;
    /**
     * @var MailAddress
     */
    public MailAddress $mailAddress;
    /**
     * @var string
     */
    public string $mother;
    /**
     * @var string
     */
    public string $birthDate;

}