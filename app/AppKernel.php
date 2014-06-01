<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

	protected $rootDir = __DIR__;

	/** {@inheritdoc} */
	public function registerBundles() {
		switch ($this->getEnvironment()) {
			case 'prod':
				return $this->getBundlesForProduction();
			default:
				return $this->getBundlesForDevelopment();
		}
	}

	/** {@inheritdoc} */
	public function registerContainerConfiguration(LoaderInterface $loader)	{
		$loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
	}

	/** {@inheritdoc} */
	public function getCacheDir() {
		return __DIR__.'/../var/cache/'.$this->environment;
	}

	/** {@inheritdoc} */
	public function getLogDir() {
		return __DIR__.'/../var/log';
	}

	private function getBundlesForProduction() {
		return array(
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Symfony\Bundle\AsseticBundle\AsseticBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new App\App(),
		);
	}

	private function getBundlesForDevelopment() {
		return array_merge($this->getBundlesForProduction(), array(
			new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle(),
		));
	}
}
