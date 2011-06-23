<?php

/**
 * This import-action will let you import a blog from blogger.com
 *
 * @package		backend
 * @subpackage	blog
 *
 * @author		Tijs Verkoyen <tijs@sumocoders.be>
 * @since		2.0
 */
class BackendBlogImportBlogger extends BackendBaseActionEdit
{
	/**
	 * Link between BloggerID and the Fork CMS ID
	 *
	 * @var	array
	 */
	private $newIds = array();


	/**
	 * Download a file
	 *
	 * @return	mixed
	 * @param	string $oldURL		The URL of the file to download.
	 * @param	int $id				The new ID of the blogpost.
	 */
	public static function download($oldURL, $id)
	{
		// redefine
		$oldURL = (string) $oldURL;
		$id = (int) $id;

		// get file info
		$fileInfo = SpoonFile::getInfo($oldURL);

		// no extension means no file
		if($fileInfo['extension'] == '') return false;

		// some extensions we can ignore by default
		$extensionsToIgnore = array('htm', 'html', 'shtml', 'php', 'asp', 'aspx', 'com', 'be', 'org', 'eu');
		if(in_array($fileInfo['extension'], $extensionsToIgnore)) return false;

		// init vars
		$headersToIgnore = array('text/html', 'application/xhtml+xml');
		$headersImages = array('image/bmp', 'image/gif', 'image/jpeg', 'image/png', 'image/tiff');

		// set options
		$options[CURLOPT_URL] = $oldURL;
		$options[CURLOPT_HEADER] = false;
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_SSL_VERIFYPEER] = false;
		$options[CURLOPT_SSL_VERIFYHOST] = false;
		$options[CURLOPT_TIMEOUT] = 5;
		$options[CURLOPT_RETURNTRANSFER] = true;

		// make the first call
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// make the call
		curl_exec($curl);

		// get info
		$info = curl_getinfo($curl);

		// get error
		$error = curl_error($curl);

		// validate
		if($error != '') return false;

		// close
		curl_close($curl);

		// validate headers
		if(!in_array($info['http_code'], array(200))) return false;

		// validate and stop if needed
		if(!isset($info['content_type'])) return false;

		// loop headers to ignore
		foreach($headersToIgnore as $header)
		{
			// should we ignore the file?
			if(substr_count($info['content_type'], $header) > 0) return false;
		}

		// initial path
		$filesPath = FRONTEND_FILES_PATH . '/userfiles/files/blog';
		$imagesPath = FRONTEND_FILES_PATH . '/userfiles/images/blog';

		// create dir if needed
		if(!SpoonDirectory::exists($filesPath)) SpoonDirectory::create($filesPath);
		if(!SpoonDirectory::exists($imagesPath)) SpoonDirectory::create($imagesPath);

		// get file info
		$fileInfo = SpoonFile::getInfo($oldURL);

		// init var
		$destinationFile = $id . '_' . $fileInfo['basename'];
		$isImage = false;

		// is it an image?
		foreach($headersImages as $header)
		{
			// is this an image?
			if(substr_count($info['content_type'], $header) > 0)
			{
				// reset & stop
				$isImage = true;
				break;
			}
		}

		// set destination path
		$destinationPath = $filesPath . '/' . $destinationFile;

		// images should be in the correct folder
		if($isImage) $destinationPath = $imagesPath . '/' . $destinationFile;

		try
		{
			// download the file
			SpoonFile::download($oldURL, $destinationPath);
		}

		// catch exceptions
		catch(Exception $e)
		{
			return false;
		}


		// return the new URL
		return str_replace(FRONTEND_FILES_PATH, FRONTEND_FILES_URL, $destinationPath);
	}


	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// set infinite time
		set_time_limit(0);

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}


	/**
	 * Load the form
	 *
	 * @return	void
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('import');

		// add a filefield
		$this->frm->addFile('blogger');
	}


	/**
	 * Process the XML and treat it as a comment
	 *
	 * @return	bool
	 * @param	SimpleXMLElement $xml	The XML to process.
	 */
	private function processXMLAsComment(SimpleXMLElement $xml)
	{
		// validate
		if(!isset($xml->link)) return false;

		// init var
		$postID = false;

		// try to determine the blogger postID
		foreach($xml->link as $link)
		{
			if($link['rel'] == 'self')
			{
				// split into chunks
				$chunks = (array) explode('/', $link['href']);

				// validate
				if(!isset($chunks[5])) return false;

				// set
				$postID = $chunks[5];
			}
		}

		// validate
		if($postID === false) return false;

		// check if we can find the new postID
		if(!isset($this->newIds[$postID])) return false;

		// build array
		$item['post_id'] = $this->newIds[$postID];
		$item['language'] = BL::getWorkingLanguage();
		$item['created_on'] = BackendModel::getUTCDate(null, strtotime((string) $xml->published));
		$item['author'] = (string) $xml->author->name;
		$item['email'] = (string) $xml->author->email;
		$item['website'] = ((string) $xml->author->uri) ? (string) $xml->author->uri : null;
		$item['text'] = (string) $xml->content;
		$item['type'] = 'comment';
		$item['status'] = 'published';

		// cleanup
		$search = array('<br />', '<BR/>', '<br>', "\n\n", '<i>', '</i>', '<b>', '</b>');
		$replace = array("\n", "\n", "\n", "\n", '', '', '', '');
		$item['text'] = str_replace($search, $replace, SpoonFilter::htmlentitiesDecode($item['text']));

		// replace links
		$item['text'] = preg_replace('|<a(.*)href="(.*)"(.*)>(.*)</a>|Ui', '$4 ($2)', $item['text']);

		// insert comment
		BackendModel::getDB(true)->insert('blog_comments', $item);

		// return
		return true;
	}


	/**
	 * Process the XML and treat it as a blogpost
	 *
	 * @return	bool
	 * @param	SimpleXMLElement $xml	The XML to process.
	 */
	private function processXMLAsPost(SimpleXMLElement $xml)
	{
		// init var
		$postID = substr((string) $xml->id, mb_strpos((string) $xml->id, 'post-') + 5);

		// validate
		if($postID == '') return false;
		if((string) $xml->title == '') return false;

		// build item
		$item['id'] = (int) BackendBlogModel::getMaximumId() + 1;
		$item['user_id'] = BackendAuthentication::getUser()->getUserId();
		$item['hidden'] = 'N';
		$item['allow_comments'] = 'Y';
		$item['num_comments'] = 0;
		$item['status'] = 'active';
		$item['language'] = BL::getWorkingLanguage();
		$item['publish_on'] = BackendModel::getUTCDate(null, strtotime((string) $xml->published));
		$item['created_on'] = BackendModel::getUTCDate(null, strtotime((string) $xml->published));
		$item['edited_on'] = BackendModel::getUTCDate(null, strtotime((string) $xml->updated));
		$item['category_id'] = 1;
		$item['title'] = (string) $xml->title;
		$item['text'] = (string) $xml->content;

		// set drafts hidden
		if(strtotime((string) $xml->published) > time())
		{
			$item['hidden'] = 'Y';
			$item['status'] = 'draft';
		}

		// build meta
		$meta = array();
		$meta['keywords'] = $item['title'];
		$meta['keywords_overwrite'] = 'N';
		$meta['description'] = $item['title'];
		$meta['description_overwrite'] = 'N';
		$meta['title'] = $item['title'];
		$meta['title_overwrite'] = 'N';
		$meta['url'] = BackendBlogModel::getURL(SpoonFilter::urlise($item['title']));
		$meta['url_overwrite'] = 'N';

		// replace fucked up links
		$item['text'] = preg_replace('|<a(.*)onblur="(.*)"(.*)>|Ui', '<a$1$3>', $item['text']);

		// fix images
		$item['text'] = preg_replace('|<img(.*)border="(.*)"(.*)>|Ui', '<img$1$3>', $item['text']);

		// remove inline styles
		$item['text'] = preg_replace('|<(.*)style="(.*)"(.*)>|Ui', '<$1$3>', $item['text']);

		// whitespace
		$item['text'] = preg_replace('|\s{2,}|', ' ', $item['text']);

		// cleanup
		$search = array('<br /><br />', '<div><br /></div>',
						'<div>', '</div>', '<i>', '</i>', '<b>', '</b>',
						'<p><object', '</object></p>',
						'<p><p>', '</p></p>',
						'...');
		$replace = array('</p><p>', '</p><p>',
							'', '', '<em>', '</em>', '<strong>', '</strong>',
							'<object', '</object>',
							'<p>', '</p>',
							'…');

		// cleanup
		$item['text'] = '<p>' . str_replace($search, $replace, SpoonFilter::htmlentitiesDecode($item['text'])) . '</p>';

		// get images
		$matches = array();
		preg_match_all('/<img.*src="(.*)".*\/>/Ui', $item['text'], $matches);

		// any images?
		if(isset($matches[1]) && !empty($matches[1]))
		{
			// init var
			$imagesPath = FRONTEND_FILES_PATH . '/userfiles/images/blog';
			$imagesURL = FRONTEND_FILES_URL . '/userfiles/images/blog';

			// create dir if needed
			if(!SpoonDirectory::exists($imagesPath)) SpoonDirectory::create($imagesPath);

			// loop matches
			foreach($matches[1] as $key => $file)
			{
				// get file info
				$fileInfo = SpoonFile::getInfo($file);

				// init var
				$destinationFile = $item['id'] . '_' . $fileInfo['basename'];

				try
				{
					// download
					SpoonFile::download($file, $imagesPath . '/' . $destinationFile);

					// replace the old URL with the new one
					$item['text'] = str_replace($file, $imagesURL . '/' . $destinationFile, $item['text']);
				}

				// catch exceptions
				catch(Exception $e)
				{
					// ignore
				}
			}
		}

		// get links
		$matches = array();
		preg_match_all('/<a.*href="(.*)".*\/>/Ui', $item['text'], $matches);

		// any images?
		if(isset($matches[1]) && !empty($matches[1]))
		{
			// loop matches
			foreach($matches[1] as $key => $file)
			{
				// get new link
				$replaceWith = self::download($file, $item['id']);

				// should we replace?
				if($replaceWith !== false)
				{
					// replace the old URL with the new one
					$item['text'] = str_replace($file, $replaceWith, $item['text']);
				}
			}
		}

		// insert meta
		$item['meta_id'] = BackendModel::getDB(true)->insert('meta', $meta);

		// insert
		BackendBlogModel::insert($item);

		// store the post
		$this->newIds[$postID] = $item['id'];

		// get tags
		$tags = array();

		// loop categories
		foreach($xml->category as $category)
		{
			// is this a tag? if so add it
			if((string) $category['scheme'] == 'http://www.blogger.com/atom/ns#') $tags[] = (string) $category['term'];
		}

		// any tags?
		if(!empty($tags)) BackendTagsModel::saveTags($item['id'], implode(',', $tags), 'blog');

		// return
		return true;
	}


	/**
	 * Validate the form
	 *
	 * @return	void
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// XML provided?
			if($this->frm->getField('blogger')->isFilled()) $this->frm->getField('blogger')->isAllowedExtension(array('xml'), BL::err('XMLFilesOnly'));

			// no file
			else $this->frm->getField('blogger')->addError(BL::err('FieldIsRequired'));

			// no errors?
			if($this->frm->isCorrect())
			{
				// move the file
				$this->frm->getField('blogger')->moveFile(FRONTEND_FILES_PATH . '/blogger.xml');

				// init object
				$reader = new XMLReader();

				// open the file
				$reader->open(FRONTEND_FILES_PATH . '/blogger.xml');

				// start reading
				$reader->read();

				// loop through the document
				while(true)
				{
					// start tag for entry?
					if($reader->name == 'entry')
					{
						// end tag?
						if($reader->nodeType == XMLReader::END_ELEMENT) $reader->next();

						// get the raw XML
						$xmlString = $reader->readOuterXml();

						// is it really an entry?
						if(substr($xmlString, 0, 6) == '<entry')
						{
							// read the XML as an SimpleXML-object
							$xml = @simplexml_load_string($reader->readOuterXml());

							// validate
							if($xml !== false)
							{
								// loop the categories
								foreach($xml->category as $category)
								{
									// post
									if($category['term'] == 'http://schemas.google.com/blogger/2008/kind#post')
									{
										// process the post
										$this->processXMLAsPost($xml);

										// stop looping
										break;
									}

									// comment
									if($category['term'] == 'http://schemas.google.com/blogger/2008/kind#comment')
									{
										// process the post
										$this->processXMLAsComment($xml);

										// stop looping
										break;
									}
								}
							}
						}
					}

					// end
					if(!$reader->read()) break;
				}

				// close
				$reader->close();

				// recalculate the comments
				BackendBlogModel::reCalculateCommentCount($this->newIds);

				// remove the file
				SpoonFile::delete(FRONTEND_FILES_PATH . '/blogger.xml');

				// everything is saved, so redirect to the overview
				$this->redirect(BackendModel::createURLForAction('index') . '&report=imported');
			}
		}
	}
}

?>