<?php namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Cache(maxage="86400", smaxage="86400")
 */
class DefaultController extends Controller {

	/**
	 * @Route("/", name="home")
	 */
	public function indexAction(Request $request) {
		$searchTerm = $request->query->get('q');
		$words = $this->em()->getWordRepository()->findByName($searchTerm);
		$response = $this->render('App:Default:index.html.twig', array(
			'words' => $words,
			'searchTerm' => $searchTerm,
			'letterCounts' => $this->em()->getStatValueRepository()->getLetterCounts(),
		));
		if (empty($words)) {
			$response->setStatusCode(Response::HTTP_NOT_FOUND);
		}
		return $response;
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
