<?php

namespace App\Twig;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TransformerExtension extends AbstractExtension
{
    /**
     * @var array<string, TransformerAbstract>
     */
    private array $transformers = [];

    /**
     * @param iterable<TransformerAbstract> $transformers
     */
    public function __construct(
        iterable $transformers,
    ) {
        foreach ($transformers as $transformer) {
            $this->transformers[$transformer::class] = $transformer;
        }
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('transform_item', [$this, 'transformItem']),
            new TwigFunction('transform_collection', [$this, 'transformCollection']),
        ];
    }

    public function transformItem(mixed $item, string $transformerClass): array
    {
        return $this->createManager()
            ->createData(new Item($item, $this->transformers[$transformerClass]))
            ->toArray();
    }

    public function transformCollection(iterable $collection, string $transformerClass): array
    {
        return $this->createManager()
            ->createData(new Collection($collection, $this->transformers[$transformerClass]))
            ->toArray()['data'];
    }

    private function createManager(): Manager
    {
        return (new Manager())->setSerializer(new ArraySerializer());
    }
}
