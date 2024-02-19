<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class AbstractAction
 */
abstract class AbstractAction extends Column
{
    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * Url path
     */
    public const URL_PATH_JOB_RUN = 'cron_extended/jobs/run';

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );

        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get Run Job Action
     *
     * @param $item
     *
     * @return array[]
     */
    protected function getRunJobAction($item): array
    {
        return [
            'run_job' => [
                'href' => $this->urlBuilder->getUrl(
                    static::URL_PATH_JOB_RUN,
                    [
                        'job_code' => $item['job_code'],
                    ]
                ),
                'label' => __('Run'),
                'post' => true,
            ],
        ];
    }
}
