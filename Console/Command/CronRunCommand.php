<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Console\Command;

use Halex\CronExtended\Model\CronManager;
use Magento\Framework\Console\Cli;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class CronRunCommand
 */
class CronRunCommand extends Command
{
    /**
     * @var CronManager
     */
    private CronManager $cronManager;

    /**
     * @var string
     */
    private const CRON_JOB_NAME = 'job';

    /**
     * Constructor
     *
     * @param CronManager $cronManager
     * @param string|null $name
     */
    public function __construct(
        CronManager $cronManager,
        string $name = null
    ) {
        parent::__construct($name);

        $this->cronManager = $cronManager;
    }

    /**
     * Configure CLI Command
     */
    protected function configure()
    {
        $this->setName('cron:extended:run')
            ->setDescription('Runs specific cron job manually');

        $this->addOption(
            self::CRON_JOB_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Cron Job Name'
        );

        parent::configure();
    }

    /**
     * Execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cronJobName = $input->getOption(self::CRON_JOB_NAME);

        if (!$cronJobName) {
            $output->writeln(
                '<error>Cron job name must be specified via --job="[job_name]" param.</error>'
            );

            return Cli::RETURN_FAILURE;
        }

        $currentCron = $this->cronManager->getCronJobConfigByCode($cronJobName);

        $output->writeln(__(
            'Running <info>%1</info>::<comment>%2()</comment>',
            $currentCron['job_instance'],
            $currentCron['job_method']
        ));

        $this->cronManager->runJob($cronJobName);

        return Cli::RETURN_SUCCESS;
    }
}
