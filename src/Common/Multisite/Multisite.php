<?php

namespace Common\Multisite;

/**
 * Responsible for handling the multisite aspect.
 * Provides some information on the available site and loads the current site as
 * a Common\Multisite\CurrentSite object.
 *
 * @author Wouter Sioen <wouter@wijs.be>
 * @author Per <per@wijs.be>
 */
class Multisite
{
    /** @type SpoonDatabase */
    private $db;

    /**
     * @param SpoonDatabase $db The database to execute queries on.
     */
    public function __construct(\SpoonDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * @throws SpoonException If the current site ID cannot be determined.
     */
    public function loadCurrentSite()
    {
        $isDomainSite = false;
        $isMainSite = false;
        $id = $this->getSiteIdForCurrentDomain();

        // We have found our site id by a domain, so we're on a domain site
        if ($id) {
            $isDomainSite = true;
            $isMainSite = $this->db->getVar(
                'SELECT is_main_site FROM sites WHERE id = ?',
                array($id)
            ) === 'Y';
        } else {
            $id = $this->getSiteIdForCurrentPrefix();
        }

        // if no site was found, fall back to the main site
        if (!$id) {
            $id = $this->getMainSiteId();
            $isMainSite = true;
        }
        if (!$id) {
            throw new SpoonException('Unable to determine site id.');
        }

        // provision the current site
        return $this->getCurrentSiteRecord($id, $isDomainSite, $isMainSite);
    }

    /**
     * Fetches the siteId based on the domain we're surfing on
     *
     * @return int
     */
    protected function getSiteIdForCurrentDomain()
    {
        $domain = SITE_DOMAIN;
        return $this->db->getVar(
            'SELECT id FROM sites WHERE domain = ?',
            array($domain)
        );
    }

    /**
     * Fetches the siteId based on the prefix from the current domain
     *
     * @return int
     */
    protected function getSiteIdForCurrentPrefix()
    {
        $domain = SITE_DOMAIN;
        $domainParts = explode('.', $domain);
        $prefix = $domainParts[0];
        return $this->db->getVar(
            'SELECT id FROM sites WHERE prefix = ?',
            array($prefix)
        );
    }

    /**
     * Fetches the siteId for the main site
     *
     * @return int
     */
    public function getMainSiteId()
    {
        return (int) $this->db->getVar(
            'SELECT id FROM sites WHERE is_main_site = ?',
            array('Y')
        );
    }

    /**
     * Fetches the domain for the main site
     *
     * @return string
     */
    public function getMainSiteDomain()
    {
        return $this->db->getVar(
            'SELECT domain FROM sites WHERE is_main_site = ?',
            array('Y')
        );
    }

    /**
     * @param int  $id           The ID of the current site.
     * @param bool $isDomainSite Whether or not this is a domain site
     * @param bool $isMainSite   Whether or not this site is the main site / HQ.
     */
    protected function getCurrentSiteRecord($id, $isDomainSite, $isMainSite)
    {
        $record = $this->db->getRecord(
            'SELECT * FROM sites WHERE id = ?',
            array((int)$id)
        );
        $record['is_domain_site'] = (bool)$isDomainSite;
        $record['is_main_site'] = (bool)$isMainSite;
        $record['active_languages'] = (array)$this->db->getColumn(
            'SELECT language FROM sites_languages
             WHERE site_id = ? AND is_active = ? AND is_viewable = ?',
            array((int)$id, 'Y', 'Y')
        );
        $record['working_languages'] = $this->getWorkingLanguages($id);

        return $record;
    }

    /**
     * @return List of working languages
     * @internal This is like BL::getWorkingLanguages(), but restricted to the
     *           available languages of the current site.
     * @note We can't send back translated languages yet, since
     * this function can be called before locale is initialized.
     */
    protected function getWorkingLanguages($siteId)
    {
        $languages = $this->getLanguageList($siteId);
        $workingLanguages = array();
        foreach ($languages as $language) {
            $workingLanguages[$language] = $language;
        }
        asort($workingLanguages);

        return $workingLanguages;
    }

    /**
     * @param int $siteId ID of the site to get the languages for.
     * @return array List of languages for the given site ID.
     */
    public function getLanguageList($siteId, $includeNonActive = false)
    {
        $active = ($includeNonActive) ? '%' : 'Y';

        return (array) $this->db->getColumn(
            'SELECT language FROM sites_languages
             WHERE site_id = ? AND is_active LIKE ?',
            array((int) $siteId, $active)
        );
    }

    /**
     * @return array list of domains that have languages
     */
    public function getSites()
    {
        return (array) $this->db->getPairs(
            'SELECT s.id, s.domain
             FROM sites s
             INNER JOIN sites_languages l ON l.site_id = s.id'
        );
    }

    /**
     * @return array
     */
    public function getAllSites()
    {
        return (array) $this->db->getRecords(
            'SELECT *
             FROM sites'
        );
    }
}
