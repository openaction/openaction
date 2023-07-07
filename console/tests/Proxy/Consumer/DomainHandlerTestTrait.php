<?php

namespace App\Tests\Proxy\Consumer;

use App\Bridge\Cloudflare\MockCloudflare;
use App\Bridge\Postmark\MockPostmark;
use App\Bridge\Sendgrid\MockSendgrid;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\Yaml\Yaml;

trait DomainHandlerTestTrait
{
    private WorkflowInterface $workflow;
    private EntityManagerInterface|MockObject $manager;
    private MessageBusInterface|MockObject $bus;
    private MockCloudflare $cloudflare;
    private MockSendgrid $sendgrid;
    private MockPostmark $postmark;
    private LoggerInterface|MockObject $logger;

    abstract protected function createMock(string $originalClassName): MockObject;

    public function setUp(): void
    {
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->bus = $this->createMock(MessageBusInterface::class);
        $this->cloudflare = new MockCloudflare();
        $this->sendgrid = new MockSendgrid();
        $this->postmark = new MockPostmark();
        $this->logger = $this->createMock(LoggerInterface::class);

        // Create actual workflow from YAML config
        $config = Yaml::parse(file_get_contents(__DIR__.'/../../../config/packages/workflow.yaml'));
        $config = $config['framework']['workflows']['domain_configuration'];

        $this->workflow = $this->createWorkflow($config);
    }

    private function createWorkflow(array $config): WorkflowInterface
    {
        $builder = new DefinitionBuilder();
        $builder->addPlaces($config['places']);

        foreach ($config['transitions'] as $name => $transition) {
            $builder->addTransition(new Transition($name, $transition['from'], $transition['to']));
        }

        $marking = new MethodMarkingStore(false, $config['marking_store']['property']);

        return new Workflow($builder->build(), $marking, null, 'domain_configuration');
    }
}
