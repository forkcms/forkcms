<?php

namespace Frontend\Modules\Blog\Actions;

use DateTimeImmutable;
use Frontend\Core\Engine\Base\Block as FrontendBaseBlock;
use Frontend\Core\Engine\Navigation;
use Frontend\Core\Language\Language as FL;
use Frontend\Core\Engine\Navigation as FrontendNavigation;
use Frontend\Modules\Blog\Engine\Model as FrontendBlogModel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Archive extends FrontendBaseBlock
{
    /** @var array */
    private $articles;

    /**  @var DateTimeImmutable */
    private $startDate;

    /**  @var DateTimeImmutable */
    private $endDate;

    /** @var bool */
    private $hasMonth;

    /** @var string */
    private $format;

    public function execute(): void
    {
        parent::execute();
        $this->loadTemplate();
        $this->getData();

        $this->parse();
    }

    private function buildUrl(): string
    {
        return FrontendNavigation::getUrlForBlock($this->getModule(), $this->getAction())
               . '/' . $this->startDate->format($this->format);
    }

    private function buildPaginationConfig(): array
    {
        $requestedPage = $this->url->getParameter('page', 'int', 1);
        $numberOfItems = FrontendBlogModel::getAllForDateRangeCount(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp()
        );

        $limit = $this->get('fork.settings')->get($this->getModule(), 'overview_num_items', 10);
        $numberOfPages = (int) ceil($numberOfItems / $limit);

        // Check if the page exists
        if ($requestedPage > $numberOfPages || $requestedPage < 1) {
            throw new NotFoundHttpException();
        }

        return [
            'url' => $this->buildUrl(),
            'limit' => $limit,
            'offset' => ($requestedPage * $limit) - $limit,
            'requested_page' => $requestedPage,
            'num_items' => $numberOfItems,
            'num_pages' => $numberOfPages,
        ];
    }

    /**
     * Complete the slug to prevent wrong months when the current day is higher than possible in the requested month
     *
     * @param string $slug
     *
     * @return string
     */
    private function getStartDateSlug(string $slug):string
    {
        if (!$this->hasMonth) {
            $slug .= '/01';
        }

        return $slug . '/01 00:00:00';
    }

    private function getSlug(): string
    {
        $yearIndex = $this->url->getParameter(0) === FL::act('Archive') ? 1 : 0;
        $monthIndex = $yearIndex + 1;
        $this->hasMonth = !empty($this->url->getParameter($monthIndex));

        if ($this->hasMonth) {
            $this->format = 'Y/m';

            return $this->url->getParameter($yearIndex) . '/' . $this->url->getParameter($monthIndex);
        }

        $this->format = 'Y';

        return $this->url->getParameter($yearIndex);
    }

    private function setDateRange(): void
    {
        $slug = $this->getSlug();
        $this->startDate = DateTimeImmutable::createFromFormat('Y/m/d H:i:s', $this->getStartDateSlug($slug));

        if (!$this->startDate instanceof DateTimeImmutable) {
            throw new NotFoundHttpException();
        }

        if ($slug !== $this->startDate->format($this->format)) {
            // redirect /2010/6 to /2010/06 to avoid duplicate content
            $redirectUrl = Navigation::getUrlForBlock($this->getModule(), $this->getAction())
                           . '/' . $this->startDate->format($this->format);
            if ($this->getRequest()->getQueryString() !== null) {
                $redirectUrl .= '?' . $this->getRequest()->getQueryString();
            }

            $this->redirect($redirectUrl, Response::HTTP_MOVED_PERMANENTLY);
        }

        $this->endDate = $this->startDate
            ->setDate($this->startDate->format('Y'), 12, 31)
            ->setTime(23, 59, 59, 999999);
    }

    private function getData(): void
    {
        $this->setDateRange();
        $this->pagination = $this->buildPaginationConfig();

        $this->articles = FrontendBlogModel::getAllForDateRange(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            $this->pagination['limit'],
            $this->pagination['offset']
        );
    }

    private function parse(): void
    {
        $this->addLinkToRssFeed();
        $this->addPageToBreadcrumb();
        $this->setPageTitle();
        $this->parsePagination();

        $this->template->assign(
            'archive',
            [
                'start_date' => $this->startDate->getTimestamp(),
                'end_date' => $this->endDate->getTimestamp(),
                'year' => $this->startDate->format('Y'),
                'month' => $this->hasMonth ? $this->startDate->format('m') : null,
            ]
        );
        $this->template->assign('items', $this->articles);
        $this->template->assign(
            'allowComments',
            $this->get('fork.settings')->get($this->getModule(), 'allow_comments')
        );
    }

    private function setPageTitle(): void
    {
        $this->header->setPageTitle(\SpoonFilter::ucfirst(FL::lbl('Archive')));
        $this->header->setPageTitle($this->startDate->format('Y'));
        if ($this->hasMonth) {
            $this->header->setPageTitle(
                \SpoonDate::getDate('F', $this->startDate->getTimestamp(), LANGUAGE, true)
            );
        }
    }

    private function addPageToBreadcrumb(): void
    {
        $this->breadcrumb->addElement(\SpoonFilter::ucfirst(FL::lbl('Archive')));
        $this->breadcrumb->addElement($this->startDate->format('Y'));
        if ($this->hasMonth) {
            $this->breadcrumb->addElement(
                \SpoonDate::getDate('F', $this->startDate->getTimestamp(), LANGUAGE, true)
            );
        }
    }

    private function addLinkToRssFeed(): void
    {
        $this->header->addRssLink(
            $this->get('fork.settings')->get($this->getModule(), 'rss_title_' . LANGUAGE, SITE_DEFAULT_TITLE),
            FrontendNavigation::getUrlForBlock($this->getModule(), 'Rss')
        );
    }
}
