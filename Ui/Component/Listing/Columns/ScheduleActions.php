<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Ui\Component\Listing\Columns;

/**
 * Class ScheduleActions
 */
class ScheduleActions extends AbstractAction
{
    /**
     * Get Message Action
     *
     * @param $item
     *
     * @return array
     */
    private function getMessageAction($item): array
    {
        if (!$item['messages']) {
            return [];
        }

        return [
            'more_info' => [
                'href' => '#',
                'label' => __('Show Messages'),
                'confirm' => [
                    'title' => __('Cron Execution Messages'),
                    'message' => sprintf('<pre>%s</pre>', $item['messages']),
                ]
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['job_code'])) {
                    $item[$this->getData('name')] = [
                        ...$this->getRunJobAction($item),
                        ...$this->getMessageAction($item),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
