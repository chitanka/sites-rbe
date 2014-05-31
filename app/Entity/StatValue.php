<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* @ORM\Entity(repositoryClass="App\Entity\StatValueRepository")
* @ORM\Table(
*	indexes={
*		@ORM\Index(columns={"name"})})
*/
class StatValue {
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
	private $value;

	public function getId() { return $this->id; }

	public function setName($name) { $this->name = $name; }
	public function getName() { return $this->name; }

	public function setValue($value) { $this->value = $value; }
	public function getValue() { return $this->value; }
}
