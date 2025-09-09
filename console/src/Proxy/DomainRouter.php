<?php

namespace App\Proxy;

use App\Entity\Project;

class DomainRouter
{
    public function generateShareUrl(Project $project, string $type, string $id, string $slug): string
    {
        return $this->generateUrl($project, '/share/'.$type.'/'.$id.'/'.$slug);
    }

    public function generateRedirectUrl(Project $project, string $type = 'home', ?string $ref = null): string
    {
        return $this->generateUrl($project, '/_redirect/'.$type.($ref ? '/'.$ref : ''));
    }

    public function generateUrl(Project $project, string $endpoint): string
    {
        return 'https://'.$project->getFullDomain().$endpoint;
    }
}
