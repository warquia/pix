<?php

namespace Warquia\Pix\resources\matera\Model;

class Representative {
    public string $name;
    public string $email;
    public array $documents; // Array of Document
    public TaxIdentifier $taxIdentifier;
    public MobilePhone $mobilePhone;
    public MailAddress $mailAddress;
    public string $mother;
    public string $birthDate;

}