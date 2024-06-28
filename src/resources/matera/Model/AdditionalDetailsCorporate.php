<?php

namespace Warquia\Pix\resources\matera\Model;

/**
 *
 */
class AdditionalDetailsCorporate {
    /**
     * @var string
     */
    public string $companyName;
    /**
     * @var int
     */
    public int $businessLine;
    /**
     * @var string
     */
    public string $establishmentForm;
    /**
     * @var string
     */
    public string $establishmentDate;
    /**
     * @var int
     */
    public int $financialStatistic;
    /**
     * @var string
     */
    public string $stateRegistration;
    /**
     * @var int
     */
    public int $monthlyIncome;
    /**
     * @var array
     */
    public array $representatives; // Array of Representative
}