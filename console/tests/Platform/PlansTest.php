<?php

namespace App\Tests\Platform;

use App\Platform\Features;
use App\Platform\Plans;
use App\Tests\UnitTestCase;

class PlansTest extends UnitTestCase
{
    public function provideFeatureAccessibleFor()
    {
        yield [Features::MODULE_WEBSITE, Plans::ESSENTIAL, true];
        yield [Features::TOOL_WEBSITE_POSTS, Plans::ESSENTIAL, false];
        yield [Features::TOOL_WEBSITE_EVENTS, Plans::ESSENTIAL, false];
        yield [Features::TOOL_WEBSITE_PETITIONS, Plans::ESSENTIAL, false];
        yield [Features::MODULE_MEMBERS_AREA, Plans::ESSENTIAL, false];

        yield [Features::MODULE_WEBSITE, Plans::STANDARD, true];
        yield [Features::TOOL_WEBSITE_POSTS, Plans::STANDARD, true];
        yield [Features::TOOL_WEBSITE_EVENTS, Plans::STANDARD, false];
        yield [Features::TOOL_WEBSITE_PETITIONS, Plans::STANDARD, false];
        yield [Features::MODULE_MEMBERS_AREA, Plans::STANDARD, false];

        yield [Features::MODULE_WEBSITE, Plans::PREMIUM, true];
        yield [Features::TOOL_WEBSITE_POSTS, Plans::PREMIUM, true];
        yield [Features::TOOL_WEBSITE_EVENTS, Plans::PREMIUM, true];
        yield [Features::TOOL_WEBSITE_PETITIONS, Plans::PREMIUM, true];
        yield [Features::MODULE_MEMBERS_AREA, Plans::PREMIUM, false];

        yield [Features::MODULE_WEBSITE, Plans::ORGANIZATION, true];
        yield [Features::TOOL_WEBSITE_POSTS, Plans::ORGANIZATION, true];
        yield [Features::TOOL_WEBSITE_EVENTS, Plans::ORGANIZATION, true];
        yield [Features::TOOL_WEBSITE_PETITIONS, Plans::ORGANIZATION, true];
        yield [Features::MODULE_MEMBERS_AREA, Plans::ORGANIZATION, true];
    }

    /**
     * @dataProvider provideFeatureAccessibleFor
     */
    public function testFeatureAccessibleFor(string $feature, string $plan, bool $expectedResult)
    {
        $this->assertSame($expectedResult, Plans::isFeatureAccessibleFor($feature, $plan));
    }

    /**
     * @dataProvider provideFeatureAccessibleFor
     */
    public function testFeatureAccessibleForOrganization(string $feature, string $plan, bool $expectedResult)
    {
        $orga = $this->createOrganization(1, 'Orga', $plan);
        $this->assertSame($expectedResult, Plans::isFeatureAccessibleFor($feature, $orga));
    }
}
