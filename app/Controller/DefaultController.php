<?php namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

	/**
	 * @Route("/", name="home")
	 */
	public function indexAction(Request $request) {
		$searchTerm = $request->query->get('q');
		return $this->render('App:Default:index.html.twig', array(
			'words' => $this->em()->getWordRepository()->findByName($searchTerm),
			'searchTerm' => $searchTerm,
		));
	}

	/**
	 * @Route("/letter/{letter}", name="letter")
	 */
	public function letterAction($letter) {
		return $this->render('App:Default:letter.html.twig', array(
			'words' => $this->em()->getWordRepository()->findByLetter($letter),
			'letter' => $letter,
		));
	}

}
