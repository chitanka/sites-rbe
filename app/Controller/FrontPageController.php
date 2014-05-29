<?php namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FrontPageController extends Controller {

	/**
	 * @Route("/", name="home")
	 */
	public function indexAction() {
		return $this->render('App:FrontPage:index.html.twig');
	}

}
