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
 * Class JobActions
 */
class JobActions extends AbstractAction
{
    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['job_code'])) {
                    $item[$this->getData('name')] = $this->getRunJobAction($item);
                }
            }
        }

        return $dataSource;
    }
}
