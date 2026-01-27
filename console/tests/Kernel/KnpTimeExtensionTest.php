<?php

namespace App\Tests\Kernel;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Twig\Environment;

class KnpTimeExtensionTest extends KernelTestCase
{
    public function testTimeFiltersRemainRegistered(): void
    {
        self::bootKernel();

        $twig = static::getContainer()->get(Environment::class);

        $this->assertNotNull($twig->getFilter('ago'));
        $this->assertNotNull($twig->getFilter('time_diff'));
        $this->assertNotNull($twig->getFunction('time_diff'));

        $template = $twig->createTemplate('{{ date|ago }}');
        $output = $template->render(['date' => new \DateTimeImmutable('-5 minutes')]);

        $this->assertNotSame('', trim($output));
    }
}
