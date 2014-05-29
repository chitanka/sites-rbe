<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity(repositoryClass="App\Entity\WordRepository")
* @ORM\Table(
*	indexes={
*		@ORM\Index(columns={"name"})})
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
	 * @ORM\Column(type="string", length=100, unique=true)
	 */
	private $name;

	/**
	 * @var string
	 * @ORM\Column(type="text")
	 */
	private $definition;

	public function getId() { return $this->id; }

	public function setName($name) { $this->name = $name; }
	public function getName() { return $this->name; }

	public function setDefinition($definition) { $this->definition = $definition; }
	public function getDefinition() { return $this->definition; }
}
