<?php

namespace Backend\Core\Engine;

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use Doctrine\ORM\EntityManager;

/**
 * This class is used for datagrids based on doctrine
 *
 * @author Wouter Sioen <wouter@woutersioen.be>
 */
class DataGridSourceDoctrine extends \SpoonDatagridSource
{
	/**
	 * EntityManager instance
	 *
	 * @var	EntityManager
	 */
	private $em;

	/**
	 * @var string
	 */
	private $repository;

	/**
	 * @var array
	 */
	private $parameters = array();

	/**
	 * @var array
	 */
	private $columns = array();

	/**
	 * @var string
	 */
	private $order;

	/**
	 * @var string;
	 */
	private $sort;

	/**
	 * Class construtor.
	 *
	 * @param EntityManager $em         The entity manager.
	 * @param string        $repository The entity repository
	 * @param array         $parameters The parameters to fetch data with
	 * @param array         $columns    The columns to fetch
     * @param string        $order      The column to order on
     * @param string        $sort       Order ascending (asc) or descending (desc)
	 */
	public function __construct(EntityManager $em, $repository, $parameters = array(), $columns = array(), $order = null, $sort = null)
	{
		$this->em = $em;
		$this->repository = $repository;
		$this->parameters = $parameters;
		$this->columns = $columns;
		$this->order = $order;
		$this->sort = $sort;

		$this->setNumResults();
	}


	/**
	 * Get the list of columns.
	 *
	 * @return	array
	 */
	public function getColumns()
	{
		// has results
		if($this->numResults != 0)
		{
			return $this->columns;
		}
	}


	/**
	 * Fetch the data as an array.
	 *
	 * @return	array
	 * @param	int[optional] $offset		The offset to start from.
	 * @param	int[optional] $limit		The maximum number of items to retrieve.
	 * @param	string[optional] $order		The column to order on.
	 * @param	string[optional] $sort		The sorting method.
	 */
	public function getData($offset = null, $limit = null, $order = null, $sort = null)
	{
		$qb = $this->getQueryBuilder();
		$qb->setFirstResult($offset);
		$qb->setMaxResults($limit);

		if ($order !== null) {
			$qb->orderBy('i.' . $order, $sort);
		} elseif($this->order !== null) {
			$qb->orderBy('i.' . $this->order, $this->sort);
		}

		$rows = $qb->getQuery()->getArrayResult();

		// extract the right columns from the array results
		$results = array();
		foreach ($rows as $row) {
			$results[] = $this->extractColumns($row);
		}

		// fetch data
		return $results;
	}


	/**
	 * Set the number of results.
	 */
	private function setNumResults()
	{
		$qb = $this->getQueryBuilder(true);
		$this->numResults = (int) $qb->getQuery()->getSingleScalarResult();
	}

	private function getQueryBuilder($count = false)
	{
		$qb = $this->em
			->getRepository($this->repository)
			->createQueryBuilder('i')
			->select($count ? 'COUNT(i)' : 'i')
		;

		foreach ($this->parameters as $name => $value) {
			$qb->andWhere('i.' . $name . ' = :' . $name);
		}

		$qb->setParameters($this->parameters);

		return $qb;
	}

	private function extractColumns($fullObject)
	{
		$result = array();

		foreach ($this->columns as $name => $alias) {

			// if our row is numeric, we don't have an alias
			if (is_numeric($name)) {
				$name = $alias;
			}

			$result[$alias] = $fullObject[$name];

			if ($result[$alias] instanceOf \DateTime) {
				$result[$alias] = $result[$alias]->getTimeStamp();
			}
		}

		return $result;
	}
}
