<?php namespace App\Service;

use App\Entity\WordRepository;
use App\Entity\Word;

class RbeImporter {

	private $repo;
	private $cleaner;

	public function __construct(WordRepository $repo) {
		$this->repo = $repo;
		$this->cleaner = new RbeCleaner();
	}

	public function importWordsFromHtml($htmlContent) {
		mb_internal_encoding('UTF-8');

		$objectCount = 0;
		foreach ($this->extractWordDefinitions($htmlContent) as $sourceDefinition) {
			$this->repo->add($this->createWord($sourceDefinition));
			if (++$objectCount > 500) {
				$this->repo->saveAll();
				$objectCount = 0;
			}
		}
		$this->repo->saveAll();
	}

	private function createWord($sourceDefinition) {
		$definition = $this->cleaner->prepareInput($sourceDefinition);
		$word = new Word;
		$word->setDefinition($definition);
		$word->setNameWithStress($this->extractTerm($definition));
		$word->setSourceDefinition($sourceDefinition);
		return $word;
	}

	private function extractTerm($input) {
		if (preg_match('#^<p><b>(.+)</b>#U', $input, $matches)) {
			$term = $matches[1];
			$term = preg_replace('#<sup>.*</sup>#', '', $term);
			$term = preg_replace('#\d+#', '', $term);
			$term = preg_replace('#<i>.+</i>#U', '', $term);
			$term = preg_replace('#[а-яI]+#u', '', $term);
			$term = strtr($term, array(
				'A' => 'А', // latin A to cyrillic one
				'E' => 'Е', // latin to cyrillic
				'B' => 'В', // latin to cyrillic
				'C' => 'С', // latin to cyrillic
				'O' => 'О', // latin to cyrillic
				'.' => '',
				',' => '',
				'­' => '',
				'‑' => '',
				'-' => '',
				'< ' => '<',
				' >' => '>',
				'|' => '',
			));
			$term = trim($term);
			$term = preg_replace('/ (С[ЕИ]|<С[ЕИ]>|\(С[ЕИ]\))$/u', '', $term);
			$term = trim($term, ' ()');
			return $term;
		}
		return null;
	}

	private function extractWordDefinitions($htmlContent) {
		$words = array();
		$wordIndex = -1;
		foreach (explode("\n", $htmlContent) as $line) {
			if ($line == '<br>') {
				$wordIndex++;
				$words[$wordIndex] = '';
				continue;
			}
			if ($wordIndex != -1) {
				if (strpos($line, '</div>') !== false) {
					break; // end of word block
				}
				$words[$wordIndex] .= $line . "\n";
			}
		}
		return array_filter(array_map('trim', $words));
	}

}
