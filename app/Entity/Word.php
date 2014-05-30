<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Entity\WordRepository")
 * @ORM\Table(
 *     indexes={
 *         @ORM\Index(columns={"name"}),
 *         @ORM\Index(columns={"first_letter"})
 *     }
 * )
 */
class Word {
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100)
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100)
	 */
	private $nameWithStress;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=1)
	 */
	private $firstLetter;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $definition;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $sourceDefinition;

	/**
	 * @var string
	 * @ORM\Column(type="string", length=100, nullable=true)
	 */
	private $workroomPage;

	public function getId() { return $this->id; }

	public function getName() { return $this->name; }
	public function setName($name) {
		$this->name = $name;
		$this->firstLetter = mb_substr($this->name, 0, 1);
	}

	public function getNameWithStress() { return $this->nameWithStress; }
	public function setNameWithStress($nameWithStress) {
		$this->nameWithStress = $nameWithStress;
		$this->setName(strtr($this->nameWithStress, array(
			'`' => '', // grave accent
			'´' => '', // acute accent
		)));
	}

	public function getFirstLetter() { return $this->firstLetter; }

	public function getDefinition() { return $this->definition; }
	public function setDefinition($definition) { $this->definition = $definition; }

	public function getSourceDefinition() { return $this->sourceDefinition; }
	public function setSourceDefinition($sourceDefinition) { $this->sourceDefinition = $sourceDefinition; }

	public function getWorkroomPage() { return $this->workroomPage; }
	public function setWorkroomPage($workroomPage) { $this->workroomPage = $workroomPage; }

}
