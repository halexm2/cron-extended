<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Model;

use Magento\Cron\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class CronManager
{
    /**
     * @var Config
     */
    private Config $cronConfig;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Config $cronConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $cronConfig,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->cronConfig = $cronConfig;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Get Cron Jobs Config List
     *
     * @return array
     */
    public function getCronJobsConfig(): array
    {
        return $this->cronConfig->getJobs();
    }

    /**
     * Get Cron Jobs List
     *
     * @return array
     */
    public function getCronJobsList(): array
    {
        $cronsConfig = $this->getCronJobsConfig();
        $cronList = [];

        foreach ($cronsConfig as $cronGroupName => $cronGroupJobs) {
            foreach ($cronGroupJobs as $cronJobName => $cronJobConfig) {
                $cronList[] = [
                    'job_code' => $cronJobName,
                    'job_group' => $cronGroupName,
                    'job_instance' => $cronJobConfig['instance'] ?? null,
                    'job_method' => $cronJobConfig['method'] ?? null,
                    'schedule' => $this->getCronJobSchedule($cronJobConfig),
                ];
            }
        }

        return $cronList;
    }

    /**
     * Get Config Value
     *
     * @param string $configPath
     *
     * @return string|null
     */
    protected function getConfigValue(string $configPath): string|null
    {
        return $this->scopeConfig->getValue($configPath);
    }

    /**
     * Get Cron Job Schedule
     *
     * @param array $cronJobConfigData
     *
     * @return string|null
     */
    public function getCronJobSchedule(array $cronJobConfigData): ?string
    {
        return $cronJobConfigData['schedule'] ?? (
            isset($cronJobConfigData['config_path'])
                ? $this->getConfigValue($cronJobConfigData['config_path'])
                : null
            );
    }

    /**
     * Get Cron Job Config By Code
     *
     * @param string $cronJobCode
     *
     * @return array|null
     */
    public function getCronJobConfigByCode(string $cronJobCode): ?array
    {
        $cronJobsList = $this->getCronJobsList();
        $cronJob = null;

        foreach ($cronJobsList as $cronJobConfig)
        {
            if ($cronJobConfig['job_code'] === $cronJobCode) {
               $cronJob = $cronJobConfig;

               break;
            }
        }

        return $cronJob;
    }

    /**
     * Run Cron Job
     *
     * @param string $cronJobCode
     *
     * @throws LocalizedException
     */
    public function runJob(string $cronJobCode)
    {
        $cronJobConfig = $this->getCronJobConfigByCode($cronJobCode);

        if (!$cronJobConfig) {
            throw new LocalizedException(__('Cron Job with code %1 not exists', $cronJobCode));
        }

        $jobInstance = $cronJobConfig['job_instance'];
        $jobMethod = $cronJobConfig['job_method'];

        if (!class_exists($jobInstance)) {
            throw new LocalizedException(__('Class %1 not exists', $jobInstance));
        }

        $objectManager = ObjectManager::getInstance();
        $cronInstance = $objectManager->get($jobInstance);

        if (!method_exists($cronInstance, $jobMethod)) {
            throw new LocalizedException(__('Method %1 in class %2 not exists', $jobMethod, $jobInstance));
        }

        $cronInstance->$jobMethod();
    }
}
