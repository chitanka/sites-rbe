<?php namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

	/**
	 * @Route("/", name="home")
	 */
	public function indexAction(Request $request) {
		$wordRepo = $this->get('app.word.repository');
		$searchTerm = $request->query->get('q');
		$words = $wordRepo->findByName($searchTerm);
		return $this->render('App:Default:index.html.twig', array(
			'words' => $words,
			'searchTerm' => $searchTerm,
		));
	}

	/**
	 * @Route("/letter/{letter}", name="letter")
	 */
	public function letterAction(Request $request, $letter) {
		$wordRepo = $this->get('app.word.repository');
		$words = $wordRepo->findByLetter($letter);
		return $this->render('App:Default:letter.html.twig', array(
			'words' => $words,
			'letter' => $letter,
		));
	}

}
