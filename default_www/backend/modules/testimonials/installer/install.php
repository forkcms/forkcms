<?php

/**
 * Installer for the testimonials module
 *
 * @package		installer
 * @subpackage	testimonials
 *
 * @author		Jan Moesen <jan@netlash.com>
 * @since		2.1
 */
class TestimonialsInstall extends ModuleInstaller
{
	/**
	 * Install the module.
	 *
	 * @return  void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/install.sql');

		// add 'testimonials' as a module
		$this->addModule('testimonials', 'The testimonials module.');

		// module rights
		$this->setModuleRights(1, 'testimonials');

		// action rights
		$this->setActionRights(1, 'testimonials', 'add');
		$this->setActionRights(1, 'testimonials', 'delete');
		$this->setActionRights(1, 'testimonials', 'edit');
		$this->setActionRights(1, 'testimonials', 'index');

		// add extra's
		$this->insertExtra('testimonials', 'block', 'AllTestimonials', 'all_testimonials', null, 'N');
		$this->insertExtra('testimonials', 'widget', 'RandomTestimonial', 'random_testimonial', null, 'N');

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'AllTestimonials', 'alle getuigenissen');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'RandomTestimonial', 'willekeurige getuigenis');
		$this->insertLocale('nl', 'backend', 'core', 'lbl', 'Testimonials', 'getuigenissen');
		$this->insertLocale('nl', 'backend', 'testimonials', 'err', 'TestimonialIsRequired', 'Gelieve een getuigenis in te geven.');
		$this->insertLocale('nl', 'backend', 'testimonials', 'lbl', 'Add', 'getuigenis toevoegen');
		$this->insertLocale('nl', 'backend', 'testimonials', 'lbl', 'Testimonial', 'getuigenis');
		$this->insertLocale('nl', 'backend', 'testimonials', 'lbl', 'Visible', 'zichtbaar');
		$this->insertLocale('nl', 'backend', 'testimonials', 'msg', 'Added', 'De getuigenis van "%1$s" werd toegevoegd.');
		$this->insertLocale('nl', 'backend', 'testimonials', 'msg', 'ConfirmDelete', 'Ben je zeker dat je de getuigenis van "%1$s" wil verwijderen?');
		$this->insertLocale('nl', 'backend', 'testimonials', 'msg', 'Deleted', 'De getuigenis van "%1$s" werd verwijderd.');
		$this->insertLocale('nl', 'backend', 'testimonials', 'msg', 'Edit', 'bewerk de getuigenis van "%1$s"');
		$this->insertLocale('nl', 'backend', 'testimonials', 'msg', 'Edited', 'De getuigenis van "%1$s" werd opgeslagen.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'TestimonialsNoItems', 'Er zijn geen getuigenissen om te tonen.');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'RandomTestimonial', 'Een willekeurige getuigenis:');
		$this->insertLocale('nl', 'frontend', 'core', 'msg', 'ReadMoreTestimonials', 'Lees meer getuigenissen…');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'AllTestimonials', 'all testimonials');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'RandomTestimonial', 'random testimonial');
		$this->insertLocale('en', 'backend', 'core', 'lbl', 'Testimonials', 'testimonials');
		$this->insertLocale('en', 'backend', 'testimonials', 'err', 'TestimonialIsRequired', 'Please provide a testimonial.');
		$this->insertLocale('en', 'backend', 'testimonials', 'lbl', 'Add', 'add testimonial');
		$this->insertLocale('en', 'backend', 'testimonials', 'lbl', 'Testimonial', 'testimonial');
		$this->insertLocale('nl', 'backend', 'testimonials', 'lbl', 'Visible', 'visible');
		$this->insertLocale('en', 'backend', 'testimonials', 'msg', 'Added', 'The testimonial by "%1$s" has been added.');
		$this->insertLocale('en', 'backend', 'testimonials', 'msg', 'ConfirmDelete', 'Are you sure you want to delete the testimonial by "%1$s"?');
		$this->insertLocale('en', 'backend', 'testimonials', 'msg', 'Deleted', 'The testimonial by "%1$s" has been deleted.');
		$this->insertLocale('en', 'backend', 'testimonials', 'msg', 'Edit', 'edit the testimonial by "%1$s"');
		$this->insertLocale('en', 'backend', 'testimonials', 'msg', 'Edited', 'The testimonial by "%1$s" has been edited.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'TestimonialsNoItems', 'There are no testimonials to show.');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'RandomTestimonial', 'A random testimonial:');
		$this->insertLocale('en', 'frontend', 'core', 'msg', 'ReadMoreTestimonials', 'Read more testimonials…');
	}
}

?>