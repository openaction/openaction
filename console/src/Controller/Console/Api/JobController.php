<?php

namespace App\Controller\Console\Api;

use App\Controller\AbstractController;
use App\Entity\Platform\Job;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/console/api/jobs')]
class JobController extends AbstractController
{
    #[Route('/{id}', name: 'console_api_job_status', methods: ['GET'])]
    public function status(Job $job)
    {
        return new JsonResponse([
            'finished' => $job->isFinished(),
            'step' => $job->getStep(),
            'progress' => $job->getProgress(),
            'payload' => $job->getPayload(),
        ]);
    }
}
