<?php

namespace App\Request;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class JourneyRequest
{
    #[Assert\NotBlank]
    #[SerializedName('base_cost')]
    public ?string $baseCost = null;

    #[Assert\NotBlank]
    #[SerializedName('year_of_birth')]
    public ?\DateTime $yearOfBirth = null;

    #[SerializedName('travel_start_date')]
    public ?\DateTime $travelStartDate = null;

    #[SerializedName('payment_date')]
    public ?\DateTime $paymentDate = null;

    public function __construct()
    {
        $this->paymentDate = new \DateTime;
    }
}