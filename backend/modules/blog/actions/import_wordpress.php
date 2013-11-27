<?php
/**
 * This import-action will let you import a wordpress blog
 *
 * @author Toon Daelman <toon@sumocoders.be>
 */
class BackendBlogImportWordpress extends BackendBaseActionEdit
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();
        set_time_limit(0);
        $this->loadForm();
        $this->validateForm();
        $this->parse();
        $this->display();
    }


    /**
     * Load the form
     */
    private function loadForm()
    {
        $this->frm = new BackendForm('import');
        $this->frm->addFile('wordpress');
    }


    /**
     * Validate the form
     */
    private function validateForm()
    {
        // Is the form submitted?
        if($this->frm->isSubmitted()) {
            // Cleanup the submitted fields, ignore fields that were added by hackers
            $this->frm->cleanupFields();

            // XML provided?
            if($this->frm->getField('wordpress')->isFilled()) {
                $this->frm->getField('wordpress')->isAllowedExtension(array('xml'), BL::err('XMLFilesOnly'));
            }

            // No file
            else {
                $this->frm->getField('wordpress')->addError(BL::err('FieldIsRequired'));
            }

            // No errors?
            if($this->frm->isCorrect()) {
                // Move the file
                $this->frm->getField('wordpress')->moveFile(FRONTEND_FILES_PATH . '/wordpress.xml');

                // Process the XML
                $this->processXML();

                // Recalculate the comments
                BackendBlogModel::reCalculateCommentCount($this->newIds);

                // Remove the file
                SpoonFile::delete(FRONTEND_FILES_PATH . '/wordpress.xml');

                // Everything is saved, so redirect to the overview
                $this->redirect(BackendModel::createURLForAction('index') . '&report=imported');
            }
        }
    }


    /**
     * Process the xml
     */
    private function processXML()
    {
        // Init object
        $reader = new XMLReader();

        // Open the file
        $reader->open(FRONTEND_FILES_PATH . '/wordpress.xml');

        // Loop through the document
        while($reader->read())
        {
            // Start tag for item?
            if($reader->name != 'item') continue;

            // End tag?
            if($reader->nodeType == XMLReader::END_ELEMENT) continue;

            // Get the raw XML
            $xmlString = $reader->readOuterXml();

            // Is it really an item?
            if(substr($xmlString, 0, 5) == '<item')
            {
                // Read the XML as an SimpleXML-object
                /* @var SimpleXMLElement $xml */
                $xml = @simplexml_load_string($reader->readOuterXml());

                // Skip element if it isn't a valid SimpleXML-object
                if($xml === false) continue;

                // What type of content are we dealing with?
                switch ($xml->children('wp', true)->post_type) {
                    case 'post':
                        // Process as post
                        $this->processPost($xml);
                        break;

                    case 'page':
                        // Process as page
                        // $this->processPage($xml);
                        break;

                    case 'attachment':
                        // Process as attachment
                        // $this->processAttachment($xml);
                        break;

                    default:
                        // Don't do anything
                        break;
                }
            }

            // End
            if(!$reader->read()) break;
        }
        die;

        // close
        $reader->close();
    }


    /**
     * Import a blog post
     *
     * @param SimpleXMLElement $xml
     * @return bool
     */
    private function processPost($xml)
    {
        // Are we really working with a post?
        if ($xml->children('wp', true)->post_type != 'post') {
            return false;
        }

        // This is a deleted post, don't import
        if ($xml->children('wp', true)->status == 'trash') {
            return false;
        }

        // Mapping for wordpress status => fork status
        $statusses = array(
            'draft'   => 'draft',
            'future'  => 'draft',
            'pending'  => 'draft',
            'private'  => 'private',
            'publish' => 'active',
            'trash'   => '',
        );
        $commentStatusses = array(
            'open'    => 'Y',
            'closed'  => 'N',
        );

        // Prepare item
        $item = array();
        $item['user_id'] = 0; // @TODO $xml->children('dc', true)->creator
        $item['title'] = (string) $xml->title;
        $item['text'] = (string) $xml->children('content', true)->encoded;
        $item['created_on'] = (string) $xml->children('wp', true)->post_date;
        $item['publish_on'] = (string) $xml->children('wp', true)->post_date;
        $item['edited_on'] = (string) $xml->children('wp', true)->post_date;
        $item['status'] = $statusses[(string) $xml->children('wp', true)->status];
        $item['allow_comments'] = $commentStatusses[(string) $xml->children('wp', true)->comment_status];

        // Some status corrections
        if ($item['status'] == 'draft') {
            $item['hidden'] = 'Y';
        }
        elseif ($item['status'] == 'private') {
            $item['status'] = 'publish';
            $item['hidden'] = 'Y';
        }

        // Prepare meta
        $meta = array();
        $meta['url'] = (string) $xml->children('wp', true)->post_name;

        // Prepare tags
        $tags = array();

        // Walk through wp categories
        foreach ($xml->category as $category) {
            /* @var SimpleXMLElement $category */
            switch ($category->attributes()->domain) {
                case 'category':
                    $item['category_id'] = (string) $category; // @TODO
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
        $comments = array();

        // Walk through wp comments
        foreach ($xml->children('wp', true)->comment as $comment) {
            /* @var SimpleXMLElement $comment */
            $comments[] = array(
                'author' => (string) $comment->children('wp', true)->comment_author,
                'email'  => (string) $comment->children('wp', true)->comment_author_email,
                'text'   => (string) $comment->children('wp', true)->comment_content,
                'created_on' => (string) $comment->children('wp', true)->comment_date,
                'status' => ((string) $comment->children('wp', true)->comment_approved == '1') ? 'published' : 'draft',
            );
        }

        var_dump($item, $meta, $tags, $comments);
        echo '<hr>';

        // Make the call
        // BackendBlogModel::insertCompletePost($item, $meta, $tags, $comments);
    }
}
