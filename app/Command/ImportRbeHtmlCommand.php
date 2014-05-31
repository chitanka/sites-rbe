<?php namespace App\Command;

use App\Service\RbeImporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRbeHtmlCommand extends ContainerAwareCommand {

	private $name = 'app:import-rbe';

	protected function configure() {
		$this
			->setName($this->name)
			->addArgument('html', InputArgument::REQUIRED, 'A HTML file from http://ibl.bas.bg/rbe')
			->setDescription('Import data from http://ibl.bas.bg/rbe');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$htmlInput = $input->getArgument('html');
		if (!file_exists($htmlInput)) {
			throw new \InvalidArgumentException("File '$htmlInput' does not exist.");
		}
		ini_set('memory_limit', '1G');
		$importer = new RbeImporter($this->getContainer()->get('repository.word'));
		$importer->importWordsFromHtml(file_get_contents($htmlInput));
	}

}
