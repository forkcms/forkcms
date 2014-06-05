<?php

namespace Common\Multisite;

/**
 * Represents the current site.
 *
 * @author <per@wijs.be>
 */
class CurrentSite
{
    /**
     * @type int The id of the current site
     */
    private $id;

    /**
     * @type array The list of active languages for this site (active meaning:
     *       selectable in the frontend
     */
    private $activeLanguages;

    /**
     * @type array The list of working languages for this site
     */
    private $workingLanguages;

    /**
     * @type string The domain of the current site
     */
    private $domain;

    /**
     * @type string The possible prefix for the current site
     */
    private $prefix;

    /**
     * @type bool Whether or not this is a domain site, ie. that the real domain
     *       is used and not our prefixing system.
     */
    private $isDomainSite;

    /**
     * @type bool Whether or not this is the main site
     */
    private $isMainSite;

    /**
     * Provisions the current site with all its data
     * This unction is only called once from the multisite service
     *
     * @param array
     * @return self
     */
    public function provision(array $currentSiteArray)
    {
        $this->id = $currentSiteArray['id'];
        $this->activeLanguages = $currentSiteArray['active_languages'];
        $this->workingLanguages = $currentSiteArray['working_languages'];
        $this->domain = $currentSiteArray['domain'];
        $this->prefix = $currentSiteArray['prefix'];
        $this->isDomainSite = $currentSiteArray['is_domain_site'];
        $this->isMainSite = $currentSiteArray['is_main_site'];
    }

    /**
     * @return int ID of the current site.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array List of the active languages
     */
    public function getActiveLanguages()
    {
        return $this->activeLanguages;
    }

    /**
     * @return array List of the working languages
     */
    public function getWorkingLanguages()
    {
        return $this->workingLanguages;
    }

    /**
     * @return string Prefix of the current site
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return boolean Is this site a domain site, ie. does it _not_ use the
     *         prefixing system.
     */
    public function isDomainSite()
    {
        return $this->isDomainSite;
    }

    /**
     * @return boolean is the current site the main site/HQ?
     */
    public function isMainSite()
    {
        return $this->isMainSite;
    }

    /**
     * @return string The default language for this site.
     */
    public function getDefaultLanguage()
    {
        return !empty($this->activeLanguages)
            ? $this->activeLanguages[0]
            : null;
    }

    /**
     * @return string The current domain without possible prefix.
     * @internal Only call this method when we are NOT dealing with a domainsite.
     */
    public function getUnprefixedDomain()
    {
        if (!$this->isMainSite && !$this->isDomainSite) {
            $domainParts = explode('.', SITE_DOMAIN);
            array_shift($domainParts);
            return implode('.', $domainParts);
        } else {
            return SITE_DOMAIN;
        }
    }
}
