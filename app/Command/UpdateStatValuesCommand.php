<?php namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStatValuesCommand extends ContainerAwareCommand {

	private $name = 'app:update-stat-values';

	protected function configure() {
		$this
			->setName($this->name)
			->setDescription('Update statistic values');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$this->getContainer()->get('repository.stat_value')->updateLetterCounts();
	}

}
