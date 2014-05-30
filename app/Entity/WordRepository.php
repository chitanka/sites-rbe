<?php namespace App\Entity;

/**
 *
 */
class WordRepository extends EntityRepository {

	public function findByName($name) {
		return $this->findBy(array('name' => $name));
	}

	public function findByLetter($letter) {
		return $this->createQueryBuilder('w')
			->where('w.firstLetter = ?1')->setParameter(1, $letter)
			->orderBy('w.name')
			->getQuery()->getArrayResult();
	}
}
