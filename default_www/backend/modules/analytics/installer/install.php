<?php

/**
 * Installer for the analytics module
 *
 * @package		installer
 * @subpackage	analytics
 *
 * @author		Annelies Van Extergem <annelies@netlash.com>
 * @since		2.0
 */
class AnalyticsInstall extends ModuleInstaller
{
	/**
	 * Install the module
	 *
	 * @return	void
	 */
	protected function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/install.sql');

		// add 'analytics' as a module
		$this->addModule('analytics', 'The analytics module.');

		// module rights
		$this->setModuleRights(1, 'analytics');

		// action rights
		$this->setActionRights(1, 'analytics', 'add_landing_page');
		$this->setActionRights(1, 'analytics', 'all_pages');
		$this->setActionRights(1, 'analytics', 'check_status');
		$this->setActionRights(1, 'analytics', 'content');
		$this->setActionRights(1, 'analytics', 'delete_landing_page');
		$this->setActionRights(1, 'analytics', 'detail_page');
		$this->setActionRights(1, 'analytics', 'exit_pages');
		$this->setActionRights(1, 'analytics', 'get_traffic_sources');
		$this->setActionRights(1, 'analytics', 'index');
		$this->setActionRights(1, 'analytics', 'landing_pages');
		$this->setActionRights(1, 'analytics', 'loading');
		$this->setActionRights(1, 'analytics', 'mass_landing_page_action');
		$this->setActionRights(1, 'analytics', 'refresh_traffic_sources');
		$this->setActionRights(1, 'analytics', 'settings');

		// insert locale (nl)
		$this->insertLocale('nl', 'backend', 'core', 'msg', 'NoReferrers', 'Er zijn nog geen statistieken van verwijzende sites.');

		$this->insertLocale('nl', 'backend', 'analytics', 'err', 'AnalyseNoSessionToken', 'Er is nog geen Google analytics account gekoppeld. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'analytics', 'err', 'AnalyseNoTableId', 'Er is nog geen analytics website profiel gekoppeld. <a href="%1$s">Configureer</a>');
		$this->insertLocale('nl', 'backend', 'analytics', 'err', 'NoSessionToken', 'Er is nog geen Google account gekoppeld.');
		$this->insertLocale('nl', 'backend', 'analytics', 'err', 'NoTableId', 'Er is nog geen website profiel gekoppeld.');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'AddLandingPage', 'landingspagina toevoegen');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'AllStatistics', 'alle statistieken');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'AverageTimeOnPage', 'gemiddelde tijd op pagina');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'AverageTimeOnSite', 'gemiddelde tijd op site');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'BounceRate', 'weigeringspercentage');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Bounces', 'weigeringen');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'ChangePeriod', 'periode aanpassen');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'ChooseThisAccount', 'kies deze account');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'DirectTraffic', 'direct verkeer');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Entrances', 'instappunten');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'ExitRate', 'uitstappercentage');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Exits', 'uitstappunten');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'GetLiveData', 'haal live gegevens op');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'GoogleAnalyticsLink', 'koppeling met Google Analytics');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'LinkedAccount', 'gekoppelde account');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'LinkedProfile', 'gekoppeld profiel');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'LinkThisProfile', 'koppel dit profiel');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'NewVisitsPercentage', 'nieuw bezoekpercentage');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'PagesPerVisit', 'pagina\'s per bezoek');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Pageviews', 'paginaweergaves');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'PageviewsByTrafficSources', 'paginaweergaves per verkeersbron');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'PercentageOfSiteTotal', 'percentage van sitetotaal');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'PeriodStatistics', 'periode statistieken');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Referral', 'verwijzende site');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'SearchEngines', 'zoekmachines');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'SiteAverage', 'sitegemidddelde');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TimeOnSite', 'bezoekduur');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopContent', 'belangrijkste inhoud');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopExitPages', 'belangrijkste uitstappagina\'s');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopKeywords', 'belangrijkste zoekwoorden');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopLandingPages', 'belangrijkste landingspagina\'s');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopPages', 'belangrijkste pagina\'s');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'TopReferrers', 'belangrijkste verwijzende sites');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'UniquePageviews', 'unieke paginaweergaves');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Views', 'weergaves');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'ViewStatistics', 'bekijk de statistieken');
		$this->insertLocale('nl', 'backend', 'analytics', 'lbl', 'Visits', 'bezoeken');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'AuthenticateAtGoogle', 'Koppel uw Google account');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'ChooseWebsiteProfile', 'Kies een Analytics website profiel...');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkAccount', 'Weet u zeker dat u de koppeling met de account "%1$s" wil verwijderen?<br />Alle opgeslagen statistieken worden dan verwijderd uit het CMS.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkGoogleAccount', 'Weet u zeker dat u de koppeling met uw Google account wilt verwijderen?');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'GetDataError', 'Er liep iets mis bij het ophalen van de gegevens via Google Analytics. Onze excuses voor het ongemak. Probeer het later nog eens.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'LinkGoogleAccount', 'Koppel uw Google account aan Fork CMS.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'LinkWebsiteProfile', 'Koppel een Google Analytics website profiel aan Fork CMS.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'LoadingData', 'Fork haalt momenteel de gegevens binnen via Google Analytics.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoAccounts', 'Er hangen geen website profielen aan deze Google account. Meld je af bij Google en probeer het met een andere account.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoContent', 'Er zijn nog geen statistieken van inhoud.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoData', 'Google heeft nog geen Analytics gegevens van uw website. Dit kan enkele dagen duren. Kijk zeker ook na of al uw instellingen bij Google goedstaan.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoExitPages', 'Er zijn nog geen statistieken van uitstappagina\'s');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoKeywords', 'Er zijn nog geen statistieken van zoekwoorden.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoLandingPages', 'Er zijn nog geen statistieken van landingpagina\'s.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'NoPages', 'Er zijn nog geen statistieken van pagina\'s.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'PagesHaveBeenViewedTimes', 'Pagina\'s op deze site zijn in totaal %1$s keer bekeken.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'RefreshedTrafficSources', 'De verkeersbronnen werden vernieuwd.');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'RemoveAccountLink', 'Verwijder de koppeling met uw Google account');
		$this->insertLocale('nl', 'backend', 'analytics', 'msg', 'RemoveProfileLink', 'Verwijder de koppeling met uw Analytics website profiel');

		// insert locale (en)
		$this->insertLocale('en', 'backend', 'core', 'msg', 'NoReferrers', 'There are no referrers yet.');

		$this->insertLocale('en', 'backend', 'analytics', 'err', 'AnalyseNoSessionToken', 'There is no link with a Google analytics account yet. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'analytics', 'err', 'AnalyseNoTableId', 'There is no link with an analytics website profile yet. <a href="%1$s">Configure</a>');
		$this->insertLocale('en', 'backend', 'analytics', 'err', 'NoSessionToken', 'There is no link with a Google analytics account yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'err', 'NoTableId', 'There is no link with an analytics website profile yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'AddLandingPage', 'add landing page');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'AllStatistics', 'all statistics');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'AverageTimeOnPage', 'average time on page');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'AverageTimeOnSite', 'average time on site');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'BounceRate', 'bounce rate');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Bounces', 'bounces');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'ChangePeriod', 'change period');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'ChooseThisAccount', 'choose this account');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'DirectTraffic', 'direct traffic');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Entrances', 'entrances');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'ExitRate', 'exit rate');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Exits', 'exits');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'GetLiveData', 'collect live data');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'GoogleAnalyticsLink', 'link to Google Analytics');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'LinkedAccount', 'linked account');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'LinkedProfile', 'linked profile');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'LinkThisProfile', 'link this profile');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'NewVisitsPercentage', 'new visits percentage');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'PagesPerVisit', 'pages per visit');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Pageviews', 'pageviews');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'PageviewsByTrafficSources', 'pageviews per traffic source');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'PercentageOfSiteTotal', 'percentage of site total');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'PeriodStatistics', 'period statistics');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Referral', 'referring site');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'SearchEngines', 'search engines');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'SiteAverage', 'site average');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TimeOnSite', 'time on site');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopContent', 'top content');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopExitPages', 'top exit pages');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopKeywords', 'top keywords');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopLandingPages', 'top landing pages');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopPages', 'top pages');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'TopReferrers', 'top referrers');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'UniquePageviews', 'unique pageviews');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Views', 'views');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'ViewStatistics', 'view statistics');
		$this->insertLocale('en', 'backend', 'analytics', 'lbl', 'Visits', 'visits');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'AuthenticateAtGoogle', 'Link your Google account');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'ChooseWebsiteProfile', 'Choose an Analytics website profile...');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkAccount', 'Are you sure you want to remove the link with the account "%1$s"?<br />All saves statistics will be deleted from the CMS.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'ConfirmDeleteLinkGoogleAccount', 'Are you sure you want to remove the link with your Google account?');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'GetDataError', 'Something went wrong while collecting the data from Google Analytics. Our appologies for the inconvenience. Please try again later.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'LinkGoogleAccount', 'Link your Google account to Fork CMS.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'LinkWebsiteProfile', 'Link your Google Analytics website profile to Fork CMS.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'LoadingData', 'Fork is collecting the data from Google Analytics.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoAccounts', 'There are no website profiles linked to this Google account. Log off at Google and try with a different account.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoContent', 'There is no content yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoData', 'Google has no Analytics data yet for your website. This could take a few days. Also check you Google Analytics account to make sure all settings are correct.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoExitPages', 'There are no exit pages yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoKeywords', 'There are no keywords yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoLandingPages', 'There are no landing pages yet.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'NoPages', 'There are ni statistics for any pages.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'PagesHaveBeenViewedTimes', 'Pages on this site have been viewed %1$s times.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'RefreshedTrafficSources', 'The traffic sources have been refreshed.');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'RemoveAccountLink', 'Remove the link with your Google account');
		$this->insertLocale('en', 'backend', 'analytics', 'msg', 'RemoveProfileLink', 'Remove the link with your Analytics website profile');
	}
}

?>