# Readme

```
<?php

namespace Frontend\Modules\Realizations\Provider;

use Backend\Modules\Realizations\Domain\Realization\Realization;
use Backend\Modules\Sitemap\Provider\SitemapProviderInterface;

class RealizationSitemapProvider implements SitemapProviderInterface
{
    /** @var string */
    private $entityClass;

    /** @var RealizationRepository */
    private $realizationRepository;

    public function __construct(RealizationRepository $realizationRepository)
    {
        $this->realizationRepository = $realizationRepository;
        $this->entityClass = Realization::class;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function getRows(): SitemapRowCollection
    {
        $collection = new SitemapRowCollection();

        // We can also use $activeLanguages

        // Add all articles from Blog to sitemap
        foreach ($this->realizationRepository->findAllActive() as $realization) {
            $collection->add(new SitemapRow(
                $realization->getLocale(),
                $realization->getMeta()->getUrl(),
                ChangeFrequently::never()
            ));
        }

        return $collection;
    }
}
```
