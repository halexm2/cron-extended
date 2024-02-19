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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

/**
 * Class CronListCommand
 */
class CronListCommand extends Command
{
    /**
     * @var CronManager
     */
    private CronManager $cronManager;

    /**
     * @var array
     */
    private const TABLE_HEADERS = [
        'Job Name',
        'Job Group',
        'Job Schedule',
    ];

    /**
     * @var string
     */
    private const CRON_GROUP_FILTER = 'group_filter';

    /**
     * @var string
     */
    private const CRON_JOB_FILTER = 'job_filter';

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
        $this->setName('cron:extended:list')
            ->setDescription('Shows all available cron jobs');

        $this->addOption(
            self::CRON_GROUP_FILTER,
            null,
            InputOption::VALUE_OPTIONAL,
            'Filter by group'
        );

        $this->addOption(
            self::CRON_JOB_FILTER,
            null,
            InputOption::VALUE_OPTIONAL,
            'Filter by cron job'
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
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cronList = $this->cronManager->getCronJobsConfig();
        $cronGroupFilter = $input->getOption(self::CRON_GROUP_FILTER);
        $cronJobFilter = $input->getOption(self::CRON_JOB_FILTER);

        foreach ($cronList as $cronGroupName => $cronGroupJobs) {
            if ($cronGroupFilter && $cronGroupName !== $cronGroupFilter) {
                continue;
            }

            if ($cronJobFilter && !array_key_exists($cronJobFilter, $cronGroupJobs)) {
                continue;
            }

            $output->writeln(__('<info>Cron Group %1</info>', $cronGroupName));

            $table = new Table($output);
            $table->setHeaders(self::TABLE_HEADERS);

            foreach ($cronGroupJobs as $cronJobName => $cronJobConfig) {
                if ($cronJobFilter && $cronJobName !== $cronJobFilter) {
                    continue;
                }

                $table->addRow([
                    $cronJobName,
                    $cronGroupName,
                    $this->cronManager->getCronJobSchedule($cronJobConfig),
                ]);
            }

            $table->render();
            $output->writeln('');
        }

        return Cli::RETURN_SUCCESS;
    }
}
