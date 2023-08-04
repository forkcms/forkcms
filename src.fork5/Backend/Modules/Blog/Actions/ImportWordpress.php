<?php

namespace Backend\Modules\Blog\Actions;

use SimpleXMLElement;
use Symfony\Component\Filesystem\Filesystem;
use Backend\Core\Engine\Base\ActionEdit as BackendBaseActionEdit;
use Backend\Core\Engine\Exception;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Language\Language as BL;
use Backend\Core\Engine\Form;
use Backend\Modules\Blog\Engine\Model;

/**
 * This import-action will let you import a wordpress blog
 */
class ImportWordpress extends BackendBaseActionEdit
{
    /**
     * @var array
     */
    private $authors = [];

    /**
     * @var array
     */
    private $attachments = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function execute(): void
    {
        parent::execute();
        set_time_limit(0);
        $this->filesystem = new Filesystem();
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }

    private function loadForm(): void
    {
        $this->form = new Form('import');
        $this->form->addFile('wordpress');
        $this->form->addText('filter', SITE_URL);
    }

    private function validateForm(): void
    {
        // Is the form submitted?
        if (!$this->form->isSubmitted()) {
            return;
        }

        // Cleanup the submitted fields, ignore fields that were added by hackers
        $this->form->cleanupFields();

        // XML provided?
        if ($this->form->getField('wordpress')->isFilled()) {
            $this->form->getField('wordpress')->isAllowedExtension(['xml'], BL::err('XMLFilesOnly'));
        } else {
            // No file
            $this->form->getField('wordpress')->addError(BL::err('FieldIsRequired'));
        }

        if (!$this->form->isCorrect()) {
            return;
        }

        // Move the file
        $this->form->getField('wordpress')->moveFile(FRONTEND_FILES_PATH . '/wordpress.xml');

        // Process the XML
        $this->processXML();

        // Remove the file
        $this->filesystem->remove(FRONTEND_FILES_PATH . '/wordpress.xml');

        // Everything is saved, so redirect to the overview
        $this->redirect(BackendModel::createUrlForAction('index') . '&report=imported');
    }

    private function processXML(): void
    {
        $reader = new \XMLReader();
        $reader->open(FRONTEND_FILES_PATH . '/wordpress.xml');

        // Loop through the document
        while ($reader->read()) {
            // Start tag for item?
            if ($reader->name !== 'item' && $reader->name !== 'wp:author') {
                continue;
            }

            // End tag?
            if ($reader->nodeType === \XMLReader::END_ELEMENT) {
                continue;
            }

            // Get the raw XML
            $xmlString = $reader->readOuterXml();

            // Read the XML as an SimpleXML-object
            /* @var SimpleXMLElement $xml */
            $xml = @simplexml_load_string($xmlString);

            // Skip element if it isn't a valid SimpleXML-object
            if ($xml === false) {
                continue;
            }

            // Is it really an item?
            if (mb_substr($xmlString, 0, 5) === '<item') {
                // What type of content are we dealing with?
                switch ($xml->children('wp', true)->post_type) {
                    case 'post':
                        // Process as post
                        $this->importPost($xml);
                        break;

                    case 'attachment':
                        // Process as attachment
                        $this->importAttachment($xml);
                        break;

                    default:
                        // Don't do anything
                        break;
                }
            } elseif (mb_substr($xmlString, 0, 10) === '<wp:author') {
                // Process the authors
                $this->authors[(string) $xml->children('wp', true)->author_login] = [
                    'id' => (string) $xml->children('wp', true)->author_id,
                    'login' => (string) $xml->children('wp', true)->author_login,
                    'email' => (string) $xml->children('wp', true)->author_email,
                    'display_name' => (string) $xml->children('wp', true)->author_display_name,
                    'first_name' => (string) $xml->children('wp', true)->author_first_name,
                    'last_name' => (string) $xml->children('wp', true)->author_last_name,
                ];
            }

            // End
            if (!$reader->read()) {
                break;
            }
        }

        // close
        $reader->close();
    }

    private function importPost(SimpleXMLElement $xml): bool
    {
        // Are we really working with a post?
        if ($xml->children('wp', true)->post_type != 'post') {
            return false;
        }

        // This is a deleted post, don't import
        if ($xml->children('wp', true)->status === 'trash') {
            return false;
        }

        // Mapping for wordpress status => fork status
        $statusses = [
            'draft' => 'draft',
            'pending' => 'draft',
            'private' => 'private',
            'publish' => 'active',
            'future' => 'publish',
        ];
        $commentStatusses = [
            'open' => true,
            'closed' => false,
        ];

        // Prepare item
        $item = [];
        $item['user_id'] = $this->handleUser((string) $xml->children('dc', true)->creator);
        $item['title'] = (string) $xml->title;
        $item['text'] = $this->handleUrls(
            (string) $xml->children('content', true)->encoded,
            $this->form->getField('filter')->getValue()
        );
        $item['created_on'] = (string) $xml->children('wp', true)->post_date;
        $item['publish_on'] = (string) $xml->children('wp', true)->post_date;
        $item['edited_on'] = (string) $xml->children('wp', true)->post_date;
        $item['status'] = $statusses[(string) $xml->children('wp', true)->status];
        $item['allow_comments'] = $commentStatusses[(string) $xml->children('wp', true)->comment_status];

        // Some status corrections
        if ($item['status'] === 'draft') {
            $item['hidden'] = true;
        } elseif ($item['status'] === 'private') {
            $item['status'] = 'publish';
            $item['hidden'] = true;
        }

        // Prepare meta
        $meta = [];
        $meta['url'] = (string) $xml->children('wp', true)->post_name;

        // Prepare tags
        $tags = [];

        // Walk through wp categories
        foreach ($xml->category as $category) {
            /* @var SimpleXMLElement $category */
            switch ($category->attributes()->domain) {
                case 'category':
                    $item['category_id'] = $this->handleCategory((string) $category);
                    break;

                case 'post_tag':
                    $tags[] = (string) $category;
                    break;

                default:
                    // Do nothing
                    break;
            }
        }

        // Prepare comments
        $comments = [];

        // Walk through wp comments
        foreach ($xml->children('wp', true)->comment as $comment) {
            /* @var SimpleXMLElement $comment */
            $comments[] = [
                'author' => (string) $comment->children('wp', true)->comment_author,
                'email' => (string) $comment->children('wp', true)->comment_author_email,
                'text' => filter_var((string) $comment->children('wp', true)->comment_content, FILTER_SANITIZE_STRING),
                'created_on' => (string) $comment->children('wp', true)->comment_date,
                'status' => ((string) $comment->children('wp', true)->comment_approved == '1') ? 'published' : 'moderation',
            ];
        }

        // Make the call
        Model::insertCompletePost($item, $meta, $tags, $comments);

        return true;
    }

    private function importAttachment(SimpleXMLElement $xml): bool
    {
        // Are we really working with a post?
        if ($xml->children('wp', true)->post_type !== 'attachment') {
            return false;
        }

        // Set paths
        $imagesPath = FRONTEND_FILES_PATH . '/Core/CKFinder/images/blog';
        $imagesUrl = FRONTEND_FILES_URL . '/Core/CKFinder/images/blog';

        // Create directory if needed
        if (!file_exists($imagesPath) || !is_dir($imagesPath)) {
            $this->filesystem->mkdir($imagesPath);
        }

        $file = (string) $xml->children('wp', true)->attachment_url;
        $guid = (string) $xml->guid;
        $fileId = (string) $xml->children('wp', true)->post_id;

        // Set filename
        $destinationFile = $fileId . '_' . basename($file);

        // Download the file
        try {
            $this->filesystem->dumpFile(
                $imagesPath . '/' . $destinationFile,
                file_get_contents($file)
            );
        } catch (Exception $e) {
            // Ignore
        }

        // Keep a log of downloaded files
        $this->attachments[mb_strtolower($file)] = $imagesUrl . '/' . $destinationFile;
        $this->attachments[mb_strtolower($guid)] = $imagesUrl . '/' . $destinationFile;

        return true;
    }

    /**
     * Handle the user of a post
     *
     * We'll try and match the original user with a fork user.
     * If we find no matches, we'll assign to the main fork user.
     *
     * @param string $username The original user name
     *
     * @return int
     */
    private function handleUser(string $username = ''): int
    {
        // Does someone with this username exist?
        /* @var \SpoonDatabase $database */
        $database = BackendModel::getContainer()->get('database');
        $id = (int) $database->getVar(
            'SELECT id FROM users WHERE email=? AND active=? AND deleted=?',
            [mb_strtolower($this->authors[(string) $username]['email']), true, false]
        );

        // We found an id!
        if ($id > 0) {
            return $id;
        }

        // Assign to main user
        return 1;
    }

    /**
     * Handle the urls inside a post
     *
     * We'll try and download images, and replace their urls
     * We'll also check for links to schrijf.be and try to replace them
     *
     * @param string $text The post text
     * @param string $filter The text that needs to be in a url before we start replacing it.
     *
     * @return string
     */
    private function handleUrls(string $text, string $filter = ''): string
    {
        // Check for images and download them, replace urls
        preg_match_all('/<img.*src="(.*)".*\/>/Ui', $text, $matchesImages);

        if (isset($matchesImages[1]) && !empty($matchesImages[1])) {
            // Walk through image links
            foreach ($matchesImages[1] as $key => $file) {
                // Should we bother looking at this file?
                if (!empty($filter) && !mb_stristr($file, $filter)) {
                    continue;
                }

                $noSize = preg_replace('/\-\d+x\d+/i', '', $file);

                if (isset($this->attachments[mb_strtolower($file)])) {
                    $text = str_replace($file, $this->attachments[mb_strtolower($file)], $text);
                } elseif (isset($this->attachments[mb_strtolower($noSize)])) {
                    $text = str_replace($file, $this->attachments[mb_strtolower($noSize)], $text);
                }
            }
        }

        // Check for links to schrijf.be and try to replace them
        preg_match_all('/<a.*href="(.*)".*\/>/Ui', $text, $matchesLinks);

        if (isset($matchesLinks[1]) && !empty($matchesLinks[1])) {
            // Walk through links
            foreach ($matchesLinks[1] as $key => $link) {
                // Should we bother looking at this file?
                if (!empty($filter) && !mb_stristr($link, $filter)) {
                    continue;
                }

                $noSize = preg_replace('/\-\d+x\d+/i', '', $link);

                if (isset($this->attachments[mb_strtolower($link)])) {
                    $text = str_replace($link, $this->attachments[mb_strtolower($link)], $text);
                } elseif (isset($this->attachments[mb_strtolower($noSize)])) {
                    $text = str_replace($link, $this->attachments[mb_strtolower($noSize)], $text);
                }
            }
        }

        return $text;
    }

    /**
     * Handle the category of a post
     *
     * We'll check if the category exists in the fork blog module, and create it if it doesn't.
     *
     * @param string $category The post category
     *
     * @return int
     */
    private function handleCategory(string $category = ''): int
    {
        // Does a category with this name exist?
        /* @var \SpoonDatabase $database */
        $database = BackendModel::getContainer()->get('database');
        $id = (int) $database->getVar(
            'SELECT id FROM blog_categories WHERE title=? AND language=?',
            [$category, BL::getWorkingLanguage()]
        );

        // We found an id!
        if ($id > 0) {
            return $id;
        }

        // Return default if we got an empty string
        if (trim($category) === '') {
            return 2;
        }

        // We should create a new category
        $cat = [];
        $cat['language'] = BL::getWorkingLanguage();
        $cat['title'] = $category;
        $meta = [];
        $meta['keywords'] = $category;
        $meta['description'] = $category;
        $meta['title'] = $category;
        $meta['url'] = $category;

        return Model::insertCategory($cat, $meta);
    }
}
