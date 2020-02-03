<?php

namespace Frontend\Modules\Faq\Actions;

use Backend\Modules\Faq\DataFixtures\LoadFaqCategories;
use Backend\Modules\Faq\DataFixtures\LoadFaqQuestions;
use Frontend\Core\Tests\FrontendWebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;

class CategoryTest extends FrontendWebTestCase
{
    public function testCategoryHasPage(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
                LoadFaqQuestions::class,
            ]
        );

        $this->assertPageLoadedCorrectly(
            $client,
            '/en/faq/category/' . LoadFaqCategories::FAQ_CATEGORY_SLUG,
            [
                '<title>' . LoadFaqCategories::FAQ_CATEGORY_TITLE,
            ]
        );
    }

    public function testNonExistingCategoryPostGives404(Client $client): void
    {
        $this->assertHttpStatusCode404($client, '/en/faq/category/non-existing');
    }

    public function testCategoryPageContainsQuestion(Client $client): void
    {
        $this->loadFixtures(
            $client,
            [
                LoadFaqCategories::class,
                LoadFaqQuestions::class,
            ]
        );

        $this->assertPageLoadedCorrectly(
            $client,
            '/en/faq/category/' . LoadFaqCategories::FAQ_CATEGORY_SLUG,
            [
                LoadFaqQuestions::FAQ_QUESTION_TITLE,
            ]
        );

        $link = $client->getCrawler()->selectLink(LoadFaqQuestions::FAQ_QUESTION_TITLE)->link();
        $this->assertPageLoadedCorrectly(
            $client,
            $link->getUri(),
            [
                '<title>' . LoadFaqQuestions::FAQ_QUESTION_TITLE,
            ]
        );
        $this->assertCurrentUrlEndsWith($client, '/en/faq/detail/' . LoadFaqQuestions::FAQ_QUESTION_SLUG);
    }
}
