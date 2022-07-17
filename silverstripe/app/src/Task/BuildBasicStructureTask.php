<?php

namespace App\Task;

use App\Model\Page\ArticleHolder;
use App\Model\Page\ProjectHolder;
use SilverStripe\Dev\Debug;
use SilverStripe\GraphQL\Schema\Logger;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\Taxonomy\TaxonomyType;

class BuildBasicStructureTask extends \SilverStripe\Dev\BuildTask
{
    private static $segment = 'BuildBasicStructureTask';
    protected $title = 'Build Basic Structure';
    protected $description = 'Builds the basic structure of the site.';
    protected Logger $logger;

    public function __construct()
    {
        parent::__construct();

        $this->logger = new Logger();
    }

    /**
     * @inheritDoc
     */
    public function run($request)
    {
        try {
            $this->createCategoryTaxonomies();
            $this->createTagTaxonomies();
            $this->createArticleHolder();
            $this->createProjectHolder();
        } catch (\Exception $e) {
            Debug::message($e->getMessage());
        }
    }

    /**
     * @throws \SilverStripe\ORM\ValidationException
     */
    protected function createCategoryTaxonomies()
    {
        if (TaxonomyType::get()->filter('Name', 'Category')->count() === 0) {
            $type = TaxonomyType::create();
            $type->Name = 'Category';
            $type->write();
            $this->logger->info('Created Taxonomy Type: Category');
        }

        if (TaxonomyTerm::get()->filter('Name', 'Programming')->count() === 0) {
            $type = TaxonomyType::get()->filter('Name', 'Category')->first();

            $term = TaxonomyTerm::create();
            $term->Name = 'Programming';
            $term->TypeID = $type->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Category): Programming');

        }

        if (TaxonomyTerm::get()->filter('Name', 'Web Development')->count() === 0) {
            $type = TaxonomyType::get()->filter('Name', 'Category')->first();
            $parent = TaxonomyTerm::get()->filter('Name', 'Programming')->first();

            $term = TaxonomyTerm::create();
            $term->Name = 'Web Development';
            $term->TypeID = $type->ID;
            $term->ParentID = $parent->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Category): Web Development');

        }
    }

    protected function createTagTaxonomies()
    {
        $type = TaxonomyType::get()->filter('Name', 'Tag')->first();
        if (!$type) {
            $type = TaxonomyType::create();
            $type->Name = 'Tag';
            $type->write();
            $this->logger->info('Created Taxonomy Type: Tag');
        }

        if (TaxonomyTerm::get()->filter('Name', 'PHP')->count() === 0) {
            $term = TaxonomyTerm::create();
            $term->Name = 'PHP';
            $term->TypeID = $type->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Tag): PHP');
        }

        // add javascript tag
        if (TaxonomyTerm::get()->filter('Name', 'JavaScript')->count() === 0) {
            $term = TaxonomyTerm::create();
            $term->Name = 'Javascript';
            $term->TypeID = $type->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Tag): Javascript');
        }

        // add css tag
        if (TaxonomyTerm::get()->filter('Name', 'CSS')->count() === 0) {
            $term = TaxonomyTerm::create();
            $term->Name = 'CSS';
            $term->TypeID = $type->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Tag): CSS');
        }

        // add typescript tag
        if (TaxonomyTerm::get()->filter('Name', 'TypeScript')->count() === 0) {
            $term = TaxonomyTerm::create();
            $term->Name = 'TypeScript';
            $term->TypeID = $type->ID;
            $term->write();
            $this->logger->info('Created Taxonomy Term (Tag): TypeScript');
        }
    }

    protected function createArticleHolder()
    {
        if (ArticleHolder::get()->count() === 0) {
            $articleHolder = ArticleHolder::create();
            $articleHolder->Title = 'Articles';
            $articleHolder->write();
            $this->logger->info('Created ArticleHolder');
        }
    }

    protected function createProjectHolder()
    {
        if (ProjectHolder::get()->count() === 0) {
            $projectHolder = ProjectHolder::create();
            $projectHolder->Title = 'Projects';
            $projectHolder->write();
            $this->logger->info('Created ProjectHolder');
        }
    }
}
