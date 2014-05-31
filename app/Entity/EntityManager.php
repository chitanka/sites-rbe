<?php namespace App\Entity;

use Doctrine\ORM\EntityManager as DoctrineEntityManager;

class EntityManager {

	private $em;

	public function __construct(DoctrineEntityManager $em) {
		$this->em = $em;
	}

	/**
	 * @param string $entityName
	 * @return EntityRepository
	 * @see DoctrineEntityManager::getRepository()
	 */
	public function getRepository($entityName) {
		if (strpos($entityName, ':') === false && strpos($entityName, '\\') === false) {
			$entityName = "App:$entityName";
		}
		return $this->em->getRepository($entityName);
	}

	/** @return WordRepository */
	public function getWordRepository() { return $this->getRepository('Word'); }
	/** @return StatValueRepository */
	public function getStatValueRepository() { return $this->getRepository('StatValue'); }

	/**
	 * A proxy to Doctrine EntityManager methods
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->em, $name), $arguments);
	}

}
