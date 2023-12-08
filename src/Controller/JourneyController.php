<?php

namespace App\Controller;

use App\Request\JourneyRequest;
use App\Service\Journey\DiscountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JourneyController extends AbstractController
{
    public function __construct(
        private readonly DiscountService     $ageService,
        private readonly ValidatorInterface  $validator,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    #[Route(path: '/index', name: '.index', methods: 'GET')]
    public function index(
        Request $request
    ): JsonResponse
    {
        /** @var JourneyRequest $journeyRequest */
        $journeyRequest = $this->serializer->denormalize($request->query->all(), JourneyRequest::class);

        $violations = $this->validator->validate($journeyRequest);

        if (\count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, 424, [], true);
        }

        return $this->json($this->ageService->getDiscount($journeyRequest));
    }
}