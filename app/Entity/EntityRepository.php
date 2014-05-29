<?php namespace App\Entity;

use Doctrine\ORM\EntityRepository as DoctrineEntityRepository;

abstract class EntityRepository extends DoctrineEntityRepository {

	protected $queryableFields = array();

	/**
	 * Save an entity into the database.
	 * @param Entity $object
	 * @see \Doctrine\ORM\EntityManager::persist()
	 * @see \Doctrine\ORM\EntityManager::flush()
	 */
	public function save($object) {
		$em = $this->getEntityManager();
		$em->persist($object);
		$em->flush();
	}

	/**
	 * Executes an SQL INSERT/UPDATE/DELETE query with the given parameters
	 * and returns the number of affected rows.
	 *
	 * This method supports PDO binding types as well as DBAL mapping types.
	 *
	 * @param string $query  The SQL query.
	 * @param array  $params The query parameters.
	 * @param array  $types  The parameter types.
	 *
	 * @return integer The number of affected rows.
	 *
	 * @throws \Doctrine\DBAL\DBALException
	 * @see \Doctrine\DBAL\Connection::executeUpdate
	 */
	public function execute($query, array $params = array(), array $types = array()) {
		return $this->getEntityManager()->getConnection()->executeUpdate($query, $params, $types);
	}

}
