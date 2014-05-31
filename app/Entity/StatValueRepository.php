<?php namespace App\Entity;

/**
 *
 */
class StatValueRepository extends EntityRepository {

	const NAME_LETTER_COUNTS = 'letter_counts';

	public function updateLetterCounts() {
		$sql = 'SELECT first_letter AS letter, COUNT(*) AS count
			FROM word
			GROUP BY first_letter
			ORDER BY first_letter';
		$letterCounts = array();
		foreach ($this->executeAndFetch($sql) as $row) {
			$letterCounts[$row['letter']] = $row['count'];
		}
		$this->update(self::NAME_LETTER_COUNTS, $letterCounts);
	}

	public function update($name, $value) {
		$statValue = $this->findByName($name);
		if ($statValue) {
			$statValue->setValue($value);
		} else {
			$statValue = new StatValue($name, $value);
		}
		$this->save($statValue);
	}

	public function getLetterCounts() {
		return $this->findByName(self::NAME_LETTER_COUNTS)->getValue();
	}

	/** @return StatValue */
	public function findByName($name) {
		return $this->findOneBy(array('name' => $name));
	}
}
