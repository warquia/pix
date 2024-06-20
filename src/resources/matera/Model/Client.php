<?php

namespace Warquia\Pix\resources\matera\Model;

class Client {
    public string $name;
    public string $socialName;
    public string $email;
    public TaxIdentifier $taxIdentifier; // Objeto TaxIdentifier
    public MobilePhone $mobilePhone; // Objeto MobilePhone
    public MailAddress $mailAddress;
}