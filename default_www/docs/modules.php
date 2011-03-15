<?php require_once '_head.php' ?>
<body>
	<div id="container">

		<?php require_once '_header.php' ?>
		<?php require_once '_toc.php' ?>

		<h2 id="themes">Modules</h2>

		<div class="cols id1">
			<div class="col col-6 content">

				<ol>
					<li><a href="#intro">Introduction</a></li>
					<li><a href="#core">Core</a></li>
					<li><a href="#contentBlocks">Content blocks</a></li>
					<li><a href="#blog">Blog</a></li>
					<li><a href="#tags">Tags</a></li>
					<li><a href="#search">Search</a></li>
					<li><a href="#analytics">Analytics</a></li>
				</ol>

				<h3 id="intro">Introduction</h3>

				<p>In this section you'll find documentation by module. For the pages module, see <a href="pages.php">pages</a>.</p>

				<h3 id="core">Core</h3>

				<p>The core will handle most of the hassle for you, so you only have to worry about what is really important.</p>

				<p><span class="markedTodo">@todo write documentation on the core module</span></p>

				<h3 id="translations">Translations</h3>

				<p>Fork CMS support websites with multiple languages by default. The Translations module allows you to translate your website in a simple way.</p>

				<h4 id="tutorial">Tutorial</h3>

				<p>Let&#8217;s say you&#8217;re maintaining a single language website. Here&#8217;s an excerpt of the template code of the contact form:</p>

				<pre class="brush: xml;">
					&lt;p&gt;
						&lt;label for="author"&gt;Name&lt;/label&gt;
						{$txtAuthor} {$txtAuthorError}
					&lt;/p&gt;
				</pre>

				<p>Now you&#8217;re tasked with making a Dutch version of the website. <em>Name</em> in the current template is hardcoded. Change it to <code>{$lblName}</code>:</p>

				<pre class="brush: xml;">
					&lt;p&gt;
						&lt;label for="author"&gt;{$lblName|ucfirst}&lt;/label&gt;
						{$txtAuthor} {$txtAuthorError}
					&lt;/p&gt;
				</pre>

				<p>Now, when you add a translation for <em>Name</em> to the locale module using the right settings, you can specify a translation in English (Name) and Dutch (Naam).</p>

				<p>What are the right settings? Read on!</p>

				<h4 id="translation_types">Translation types</h3>

				<p>There are 4 different translation types: action, error, label and message.</p>

				<h4 id="lbl_label">lbl (label):</h4>

				<p>A label is a <strong>literal translation</strong> of one word or a word group (Not a full sentence, use messsages for sentences).</p>

				<p>Name: should be a descriptive camelcased string in English.
				Value: should contain one word or a word group</p>

				<p>HTML is not allowed within messages.</p>

				<p>Don&#8217;t use capitals: formatting (e.g. uppercase, ucfirst) should be done using modifiers in the template. You should, of course, use capitals when the translation is an abbreviation such as RSS.</p>

				<p>Correct examples:</p>

				<ul>
					<li>Name: naam</li>
					<li>Archive: archief</li>
					<li>RSS: RSS</li>
					<li>Email: e-mail</li>
					<li>Language: taal</li>
				</ul>

				<p>Incorrect examples:</p>

				<ul>
					<li>MinutesAgo: %1$s minutes ago (this should be a message)</li>
					<li>JanuaryShort: jan (not a literal translation, should be a message)</li>
				</ul>

				<p>Usage:</p>

				<ul>
					<li>in templates: {$lblTitle|ucfirst}</li>
					<li>in frontend PHP: FL::lbl(&#8216;Title&#8217;);</li>
				</ul>

				<h4 id="msg_message">msg (message):</h4>

				<p>Messages should be used for all <strong>full sentences</strong>. If the sentence is an error, use the error translation type (err) instead.</p>

				<p>Name: should be a descriptive camelcased string in English.
				Value: should contain a full formatted sentence with punctuation</p>

				<p>HTML is allowed within messages.</p>

				<p>Note: unlike labels, formatting should be done inside the message, not by modifiers. A full sentence starts with a capital letter; a message does too.</p>

				<p>Correct examples:</p>

				<ul>
					<li>NewsletterSuccess: Bedankt voor je inschrijving.</li>
					<li>JanuaryShort: jan
						<ul>
							<li>This is a short message, why not a label? Because it&#8217;s not a literal translation.</li>
						</ul>
					</li>
					<li>WebdesignNetlash: <a href="http://www.netlash.com">Netlash webdesign &amp; grafisch ontwerp</a></li>
				</ul>

				<p>Incorrect examples:</p>

				<ul>
					<li>Email: e-mail
						<ul>
							<li>What&#8217;s wrong? This should be a label, not a message</li>
						</ul>
					</li>
					<li>InvalidEmail: Ongeldig e-mailadres
						<ul>
							<li>What&#8217;s wrong? This should be an error, not a message</li>
						</ul>
					</li>
				</ul>

				<p>Usage:</p>

				<ul>
					<li>in templates: {$msgNewsletterSuccess}</li>
					<li>in frontend PHP: FL::msg(&#8216;NewsletterSuccess&#8217;);</li>
				</ul>

				<h4 id="act_action">act (action):</h4>

				<p>Name: should be a descriptive camelcased string in English. Most likely a single word (e.g. Archive)
				Value: should contain the literal translation. (e.g. archief/archive)</p>

				<p>An action should only be used to build clean URLs. It cannot contain any special characters or spaces and describes. It describes the current module action (e.g. &#8220;add&#8221;, &#8220;edit&#8221;). This is an example Fork URL:</p>

				<ul>
					<li>English: http://mysite.be/en/blog/archive/</li>
					<li>Dutch: http://mysite.be/nl/blog/archief/</li>
				</ul>

				<p>Let&#8217;s deconstruct the URL:</p>

				<ul>
					<li><strong>http://mysite.be</strong>: the domain</li>
					<li><strong>/nl</strong>: the current languag; depends on what language the page is requested in</li>
					<li><strong>/blog</strong>: the current page name; this is the name given to the page in the Pages module</li>
					<li><strong>/index</strong>: the current module action; this depends on the &#8220;act&#8221; translation</li>
				</ul>

				<p>Usage:</p>

				<ul>
					<li>In templates: {$actCategory}</li>
					<li>In frontend PHP: FL::act(&#8216;Category&#8217;);</li>
				</ul>

				<p>Please note: it&#8217;s not possible for a page&#8217;s URL to be the same as one of the existing actions translations. E.g. you can&#8217;t set a page&#8217;s URL to &#8216;detail&#8217; when the translation for the action &#8216;detail&#8217; already exists in the translations module.</p>

				<h4 id="err_error">err (error):</h4>

				<p>Name: should be a descriptive camelcased string in English.
				Value: should contain a full sentence.</p>

				<p>What’s the difference between the Error translation type (err) and the message translation type (msg)? We use this system to group together errors, since they&#8217;re a special type of message. Errors are often very similar (e.g. &#8220;Please enter a name. Please enter your e-mail.&#8221;) so they are easier to translate when grouped together.</p>

				<p>HTML is allowed within error values.</p>

				<p>Correct examples:</p>

				<ul>
					<li>InvalidEmail: This e-mail address is invalid.</li>
					<li>RequiredField: This field is required.</li>
				</ul>

				<p>Incorrect errors:</p>

				<ul>
					<li>RequiredField: This field is required
						<ul>
							<li>What&#8217;s wrong? Errors are full sentcences. Punctuation marks (e.g. periods and colons) are part of the translation.</li>
						</ul>
					</li>
					<li>Email: e-mail
						<ul>
							<li>What&#8217;s wrong? This should be a label, not an error</li>
						</ul>
					</li>
					<li>NewsletterSuccess: Thanks for subscribing to our newsletter.
						<ul>
							<li>What&#8217;s wrong? This is not an error, should be a message.</li>
						</ul>
					</li>
				</ul>

				<p>Usage:</p>

				<ul>
					<li>in templates: {$errInvalidEmail}</li>
					<li>in frontend PHP: FL::err(&#8216;InvalidEmail&#8217;);</li>
				</ul>

				<h4 id="backend_translations">Backend translations</h4>

				<p>Translating the backend works the same as translating the frontend.</p>

				<p>A few optimizations were made to make your life a little bit easier.</p>

				<h4 id="module_specific_translations">Module specific translations</h4>

				<p>When adding a backend translation for a custom module, choose the module from the dropdown.</p>

				<p>A set of commonly translations is available from the Core module (e.g. &#8220;add&#8221;). When your custom module requires a new translation, add it as a module translation.</p>

				<p>Module specific message examples:</p>

				<ul>
					<li>Users - InvalidUsername: A username may not contain special characters. Only alphanumeric characters are allowed.</li>
					<li>Blog - NoAkismetKey: No akismet key was provided. Please enter one in the field below.</li>
				</ul>

				<p>Labels: </p>

				<ul>
					<li>Pages - Add: add page</li>
					<li>Users - Add: add user</li>
				</ul>

				<p>Notice that in this last example &#8220;Add&#8221; translates as &#8220;Add page&#8221; when in the pages module and &#8220;add user&#8221; in the users module. When adding a translation for a custom module that already exists in the core module, your translation will override it.</p>

				<p>Note: Backend URLs are automatically generated so you&#8217;ll never have to add &#8220;act&#8221; translations for the backend. The backend application only has 3 translation types: lbl, msg and err.</p>

				<h4 id="replace_codes">Replace codes</h4>

				<p>In some cases you might have a message/error that contains some variable piece.</p>

				<pre class="brush: xml;">
					&nbsp;
					$msgBlogNumComments =&gt; 'This blogpost has %1$s comments'
					&nbsp;
				</pre>

				<p>The %1$s will be replaced by the number of comments if you use the following template code and assume $numComments exists.</p>

				<pre class="brush: xml;">
					&nbsp;
					{$msgBlogNumComments|sprintf:{$numComments}}
					&nbsp;
				</pre>

				<p>If you have more than 1 variable in your string:</p>

				<pre class="brush: xml;">
					&nbsp;
					$msgMyAnimals =&gt; 'I have %1$s horses, %2$s dogs and %3$s cats';
					&nbsp;
				</pre>

				<p>Template code:</p>

				<pre class="brush: xml;">
					&nbsp;
					{$msgMyAnimals|sprintf:{$numHorses}:{$numDogs}:{$numCats}}
					&nbsp;
				</pre>

				<h3 id="contentBlocks">Content blocks</h3>

				<p><span class="markedTodo">@todo write documentation on the content blocks module</span></p>

				<h3 id="blog">Blog</h3>

				<p><span class="markedTodo">@todo write documentation on the blog module</span></p>

				<h3 id="tags">Tags</h3>

				<p><span class="markedTodo">@todo write documentation on the tags module</span></p>

				<h3 id="search">Search</h3>

				<p><span class="markedTodo">@todo write documentation on the search module</span></p>

				<h3 id="analytics">Analytics</h3>

				<p><span class="markedTodo">@todo write documentation on the analytics module</span></p>

			</div>
		</div>

		<div class="hr"><hr /></div>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

	</div>
</body>
</html>