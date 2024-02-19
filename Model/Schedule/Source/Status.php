<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Model\Schedule\Source;

use Magento\Cron\Model\Schedule;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'Success',
                'value' => Schedule::STATUS_SUCCESS,
            ],
            [
                'label' => 'Error',
                'value' => Schedule::STATUS_ERROR,
            ],
            [
                'label' => 'Missed',
                'value' => Schedule::STATUS_MISSED,
            ],
            [
                'label' => 'Running',
                'value' => Schedule::STATUS_RUNNING,
            ],
            [
                'label' => 'Pending',
                'value' => Schedule::STATUS_PENDING,
            ],
        ];
    }
}
