<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * In this file we store all generic functions that we will be using in the search module
 *
 * @author Matthias Mullie <matthias@mullie.eu>
 */
class FrontendSearchModel
{
	/**
	 * Build the search term
	 *
	 * @param string $terms The string to build.
	 * @return string
	 */
	public static function buildTerm($terms)
	{
		// loop all items
		foreach($terms as $i => $term)
		{
			// trim terms
			$term = trim($term);

			// last word may be incomplete (still typing)
			$split = explode(' ', $term);
			$last = (string) array_pop($split);
			$terms[$i] = ($split ? '+' . implode(' +', $split) . ' ' : '') . '(>+' . $last . ' <+' . $last . '*)';

			// current string encountered
			$terms[$i] = '>' . $terms[$i];

			if(strpos($terms[$i], ' ') !== false)
			{
				// part of words encountered
				$terms[$i] .= ' <(' . implode(' ', $split) . ' ' . trim($last) . '*)';
			}
		}

		return $terms;
	}

	/**
	 * Execute actual search
	 *
	 * This function can be called with either a string as parameter (simple search) or an array (advanced search)
	 * Simple search: all search index fields will be searched for the given term
	 * Advanced search: only the given fields (keys in the array) will be matched to the corresponding values (correspinding values in the array)
	 *
	 * @param mixed $term The searchterm (simple search) or the fields to search for (advanced search - please note that the field names may not be consistent throughout several modules).
	 * @param int[optional] $limit The number of articles to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function execSearch($term, $limit = 20, $offset = 0)
	{
		$limit = (int) $limit;
		$offset = (int) $offset;

		// advanced search
		if(is_array($term))
		{
			// init vars
			$where = array();
			$order = array();
			$join = array();
			$params1 = array();
			$params2 = array();

			// loop all searches
			foreach($term as $field => $value)
			{
				// get all terms to search for (including synonyms)
				$terms = self::getSynonyms((string) $value);

				// build search terms
				$terms = self::buildTerm($terms);

				$queryNr = count($where);

				// add query
				$where[$queryNr] = '(' . substr(str_repeat('MATCH (i' . $queryNr . '.value) AGAINST (? IN BOOLEAN MODE) OR ', count($terms)), 0, -4) . ') AND i' . $queryNr . '.field = ? AND i' . $queryNr . '.language = ? AND i' . $queryNr . '.active = ? AND m' . $queryNr . '.searchable = ?';
				$order[$queryNr] = '(' . substr(str_repeat('MATCH (i' . $queryNr . '.value) AGAINST (? IN BOOLEAN MODE) + ', count($terms)), 0, -3) . ') * m' . $queryNr . '.weight';
				$join[$queryNr] = 'search_index AS i' . $queryNr . ($join ? ' ON i' . $queryNr . '.module = i0.module AND i' . $queryNr . '.other_id = i0.other_id' : '') . ' INNER JOIN search_modules AS m' . $queryNr . ' ON m' . $queryNr . '.module = i' . $queryNr . '.module';

				// add params
				$params1 = array_merge($params1, $terms);
				$params2 = array_merge($params2, $terms, array((string) $field, FRONTEND_LANGUAGE, 'Y', 'Y'));
			}

			// prepare query and params
			$query =
				'SELECT i0.module, i0.other_id, ' . implode(' + ', $order) . ' AS score
				 FROM ' . implode(' INNER JOIN ', $join) . '
				 WHERE ' . implode(' AND ', $where) . '
				 ORDER BY score DESC
				 LIMIT ?, ?';

			$params = array_merge($params1, $params2, array($offset, $limit));
		}

		// simple search
		else
		{
			// get all terms to search for (including synonyms)
			$terms = self::getSynonyms((string) $term);

			// build search terms
			$terms = self::buildTerm($terms);

			// prepare query and params
			$query =
				'SELECT i.module, i.other_id, SUM(' . substr(str_repeat('MATCH (i.value) AGAINST (? IN BOOLEAN MODE) + ', count($terms)), 0, -3) . ') * m.weight AS score
				 FROM search_index AS i
				 INNER JOIN search_modules AS m ON i.module = m.module
				 WHERE (' . substr(str_repeat('MATCH (i.value) AGAINST (? IN BOOLEAN MODE) OR ', count($terms)), 0, -4) . ') AND i.language = ? AND i.active = ? AND m.searchable = ?
				 GROUP BY module, other_id
				 ORDER BY score DESC
				 LIMIT ?, ?';

			$params = array_merge($terms, $terms, array(FRONTEND_LANGUAGE, 'Y', 'Y', $offset, $limit));
		}

		return (array) FrontendModel::getDB()->getRecords($query, $params);
	}

	/**
	 * Get preview searches that start with ...
	 *
	 * @param string $term The first letters of the term we're looking for.
	 * @param string[optional] $language The language to search in.
	 * @param int[optional] $limit Limit resultset.
	 * @return array
	 */
	public static function getStartsWith($term, $language = '', $limit = 10)
	{
		// language given
		if($language)
		{
			return (array) FrontendModel::getDB()->getRecords(
				'SELECT s1.term, s1.num_results
				 FROM search_statistics AS s1
				 INNER JOIN
				 (
			 		SELECT term, MAX(id) AS id, language
				 	FROM search_statistics
				 	WHERE term LIKE ? AND num_results IS NOT NULL AND language = ?
				 	GROUP BY term
				 ) AS s2 ON s1.term = s2.term AND s1.id = s2.id AND s1.language = s2.language AND s1.num_results > 0
				 ORDER BY s1.num_results ASC
				 LIMIT ?',
				array((string) $term . '%', $language, $limit)
			);
		}

		// no language given
		else
		{
			return (array) FrontendModel::getDB()->getRecords(
				'SELECT s1.term, s1.num_results
				 FROM search_statistics AS s1
				 INNER JOIN
				 (
				 	SELECT term, MAX(id) AS id, language
				 	FROM search_statistics
				 	WHERE term LIKE ? AND num_results IS NOT NULL
				 	GROUP BY term
				 ) AS s2 ON s1.term = s2.term AND s1.id = s2.id AND s1.language = s2.language AND s1.num_results > 0
				 ORDER BY s1.num_results ASC
				 LIMIT ?',
				array((string) $term . '%', $limit)
			);
		}
	}

	/**
	 * Get synonyms
	 *
	 * @param string $term The term to get synonyms for.
	 * @return array
	 */
	public static function getSynonyms($term)
	{
		// query db for synonyms
		$synonyms = FrontendModel::getDB()->getVar(
			'SELECT synonym
			 FROM search_synonyms
			 WHERE term = ?',
			array((string) $term)
		);

		// found any? merge with original term
		if($synonyms) return array_unique(array_merge(array($term), explode(',', $synonyms)));

		// only original term
		return array($term);
	}

	/**
	 * Get total results
	 *
	 * Note: please be aware that this is an approximate amount. It IS possible that this is not the exact amount of search results,
	 * since search results may vary in time (entries may not yet/no longer be shown) and we will not rebuild the entire search index
	 * on every search (would be a great performance killer and huge scalibility loss)
	 *
	 * This function can be called with either a string as parameter (simple search) or an array (advanced search)
	 * Simple search: all search index fields will be searched for the given term
	 * Advanced search: only the given fields (keys in the array) will be matched to the corresponding values (correspinding values in the array)
	 *
	 * @param mixed $term The searchterm (simple search) or the fields to search for (advanced search - please note that the field names may not be consistent throughout several modules).
	 * @return int
	 */
	public static function getTotal($term)
	{
		// advanced search
		if(is_array($term))
		{
			// init vars
			$where = array();
			$join = array();
			$params = array();

			// loop all searches
			foreach($term as $field => $value)
			{
				// get all terms to search for (including synonyms)
				$terms = self::getSynonyms((string) $value);

				// build search terms
				$terms = self::buildTerm($terms);

				$queryNr = count($where);

				// add query
				$where[$queryNr] = '(' . substr(str_repeat('MATCH (i' . $queryNr . '.value) AGAINST (? IN BOOLEAN MODE) OR ', count($terms)), 0, -4) . ') AND i' . $queryNr . '.field = ? AND i' . $queryNr . '.language = ? AND i' . $queryNr . '.active = ? AND m' . $queryNr . '.searchable = ?';
				$join[$queryNr] = 'search_index AS i' . $queryNr . ($join ? ' ON i' . $queryNr . '.module = i0.module AND i' . $queryNr . '.other_id = i0.other_id' : '') . ' INNER JOIN search_modules AS m' . $queryNr . ' ON m' . $queryNr . '.module = i' . $queryNr . '.module';

				// add params
				$params = array_merge($params, $terms, array((string) $field, FRONTEND_LANGUAGE, 'Y', 'Y'));
			}

			// prepare query and params
			$query =
				'SELECT COUNT(module)
				 FROM
				 (
				 	SELECT i0.module, i0.other_id
				 	FROM ' . implode(' INNER JOIN ', $join) . '
				 	WHERE ' . implode(' AND ', $where) . '
				 ) AS results';
		}

		// simple search
		else
		{
			// get all terms to search for (including synonyms)
			$terms = self::getSynonyms((string) $term);

			// build search terms
			$terms = self::buildTerm($terms);

			// prepare query and params
			$query =
				'SELECT COUNT(module)
				 FROM
				 (
				 	SELECT i.module
				 	FROM search_index AS i
				 	INNER JOIN search_modules AS m ON i.module = m.module
				 	WHERE (' . substr(str_repeat('MATCH (i.value) AGAINST (? IN BOOLEAN MODE) OR ', count($terms)), 0, -4) . ') AND i.language = ? AND i.active = ? AND m.searchable = ?
				 	GROUP BY i.module, i.other_id
				 ) AS results';

			$params = array_merge($terms, array(FRONTEND_LANGUAGE, 'Y', 'Y'));
		}

		// get the search results
		return (int) FrontendModel::getDB()->getVar($query, $params);
	}

	/**
	 * Save a search
	 *
	 * @param array $item The data to store.
	 */
	public static function save($item)
	{
		FrontendModel::getDB(true)->insert('search_statistics', $item);
	}

	/**
	 * Search
	 * The actual search will be performed by the execSearch() method.
	 * It will then pass on the results to the specific modules, which are then responsible for returning the required data and filtering out unwanted results (entries that should not be shown)
	 * The activation/deactivation of search indices will automatically be handled by this function to keep the search index up to date, based on the module's returned results
	 *
	 * This function can be called with either a string as parameter (simple search) or an array (advanced search)
	 * Simple search: all search index fields will be searched for the given term
	 * Advanced search: only the given fields (keys in the array) will be matched to the corresponding values (correspinding values in the array)
	 *
	 * @param mixed $term The searchterm (simple search) or the fields to search for (advanced search - please note that the field names may not be consistent throughout several modules).
	 * @param int[optional] $limit The number of articles to get.
	 * @param int[optional] $offset The offset.
	 * @return array
	 */
	public static function search($term, $limit = 20, $offset = 0)
	{
		// revalidate searches
		if(FrontendModel::getModuleSetting('search', 'validate_search', true) == true) self::validateSearch(); // @note: on heavy sites with a lot of inactive search indices better use a cronjob (which will automatically set this module setting to N)

		// execute the actual search
		$searchResults = self::execSearch($term, $limit, $offset);

		// get the total amount of results (we'll get back to this later ;) )
		$total = count($searchResults);

		// none found? return empty :(
		if(!$searchResults) return array();

		// prepare to send to modules
		$moduleResults = array();

		// loop the resultset
		foreach($searchResults as $searchResult) $moduleResults[$searchResult['module']][] = $searchResult['other_id'];

		// pass the results to the modules
		foreach($moduleResults as $module => $otherIds)
		{
			// check if this module actually is prepared to handle searches (well it should, because else there shouldn't be any search indices)
			if(is_callable(array('Frontend' . SpoonFilter::toCamelCase($module) . 'Model', 'search')))
			{
				// get the required info from our module
				$moduleResults[$module] = call_user_func(array('Frontend' . SpoonFilter::toCamelCase($module) . 'Model', 'search'), $otherIds);
			}

			// does not exist, let's get this module out of here
			else unset($moduleResults[$module]);
		}

		// now place the prepared data back in our original resultset, which has our results in correct order
		foreach($searchResults as $i => $result)
		{
			// loop parsed results for this specific module to find the one we want here
			foreach($moduleResults[$result['module']] as $otherId => $moduleResult)
			{
				// that's the one..
				if($otherId == $result['other_id'])
				{
					$searchResults[$i] = array_merge(array('module' => $result['module']), $moduleResult);
					continue 2;
				}
			}

			// if we made it here, we obviously did not get this result parsed by the module, so remove it!
			unset($searchResults[$i]);
			self::statusIndex($result['module'], (array) $result['other_id'], false);
		}

		// results got removed by the module? oh noes :o have another run, because now we've deactivated those responsible for the holes :)
		if(count($searchResults) < $total && $total == $limit) $searchResults = self::search($term, $limit, $offset);

		// return results
		return $searchResults;
	}

	/**
	 * Deactivate an index (no longer has to be searched)
	 *
	 * @param string $module The module we're deleting an item from.
	 * @param array $otherIds An array of other_id's for this module.
	 * @param bool[optional] $active Set the index to active?
	 */
	public static function statusIndex($module, array $otherIds, $active = true)
	{
		// redefine
		$active = ($active && $active !== 'N') ? 'Y' : 'N';

		// deactivate!
		if($otherIds) FrontendModel::getDB(true)->update('search_index', array('active' => $active), 'module = ? AND other_id IN (' . implode(',', $otherIds) . ')', array((string) $module));
	}

	/**
	 * Validate searches: check everything that has been marked as 'inactive', if should still be inactive
	 */
	public static function validateSearch()
	{
		// we'll iterate through the inactive search indices in little batches
		$offset = 0;
		$limit = 50;

		while(1)
		{
			// get the inactive indices
			$searchResults = (array) FrontendModel::getDB()->getRecords(
				'SELECT module, other_id
				 FROM search_index
				 WHERE language = ? AND active = ?
				 GROUP BY module, other_id
				 LIMIT ?, ?',
				array(FRONTEND_LANGUAGE, 'N', $offset, $limit)
			);

			// none found? good news!
			if(!$searchResults) return;

			// prepare to send to modules
			$moduleResults = array();

			// loop the resultset
			foreach($searchResults as $searchResult) $moduleResults[$searchResult['module']][] = $searchResult['other_id'];

			// pass the results to the modules
			foreach($moduleResults as $module => $otherIds)
			{
				// check if this module actually is prepared to handle searches (well it should, because else there shouldn't be any search indices)
				if(is_callable(array('Frontend' . SpoonFilter::ucfirst($module) . 'Model', 'search')))
				{
					$moduleResults[$module] = call_user_func(array('Frontend' . SpoonFilter::ucfirst($module) . 'Model', 'search'), $otherIds);

					// update the ones that are allowed to be searched through
					self::statusIndex($module, array_keys($moduleResults[$module]), true);
				}
			}

			// didn't even get the amount of result we asked for? no need to ask again!
			if(count($searchResults) < $offset) return;

			$offset += $limit;
		}
	}
}
