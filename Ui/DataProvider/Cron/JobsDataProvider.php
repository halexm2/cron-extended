<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Ui\DataProvider\Cron;

use Halex\CronExtended\Model\CronManager;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\SortOrder;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\Http;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;
use Magento\Ui\DataProvider\AddFilterToCollectionInterface;

/**
 * Class JobsDataProvider
 */
class JobsDataProvider extends AbstractDataProvider
{
    /**
     * @var AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;

    /**
     * @var Http
     */
    protected Http $request;

    /**
     * @var CronManager
     */
    private CronManager $cronManager;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Http $request
     * @param CronManager $cronManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Http $request,
        CronManager $cronManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );

        $this->request = $request;
        $this->cronManager = $cronManager;
    }

    /**
     * Apply Sort Order
     *
     * @param array $items
     */
    private function applySortOrder(array &$items)
    {
        if (!$this->request->getParams()) {
           return;
        }

        $requestSorting = $this->request->getParams()['sorting'] ?? [];

        if (!count($requestSorting)) {
            return;
        }

        usort($items, function ($item1, $item2) use($requestSorting) {
            return strtoupper($requestSorting['direction']) === SortOrder::SORT_ASC
                ? strcasecmp($item1[$requestSorting['field']], $item2[$requestSorting['field']])
                : strcasecmp($item2[$requestSorting['field']], $item1[$requestSorting['field']]);
        });
    }

    /**
     * Apply Pagination
     *
     * @param array $items
     *
     * @return void
     */
    private function applyPagination(array &$items)
    {
        if (!$this->request->getParams()) {
            return;
        }

        $requestPaging = $this->request->getParam('paging') ?: [];

        $pageSize = intval($requestPaging['pageSize'] ?? 1);
        $pageCurrent = intval($requestPaging['current'] ?? 1);
        $pageOffset = ($pageCurrent - 1) * $pageSize;

        $items = array_slice($items, $pageOffset, $pageOffset + $pageSize);
    }

    /**
     * Apply Filters
     *
     * @param array $items
     */
    private function applyFilters(array &$items)
    {
        $requestFilters = $this->request->getParams()['filters'] ?? [];

        if (!count($requestFilters)) {
            return;
        }

        $filteredData = $items;

        foreach ($requestFilters as $fieldName => $fieldValue) {
            if ($fieldName === 'placeholder') {
               continue;
            }

            $filteredData = array_filter($filteredData, function ($cronData) use($fieldName, $fieldValue) {
                return str_contains($cronData[$fieldName], $fieldValue);
            });
        }

        $items = $filteredData;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $items = $this->cronManager->getCronJobsList();

        $this->applySortOrder($items);
        $this->applyFilters($items);
        $this->applyPagination($items);

        return [
            'totalRecords' => count($items),
            'items' => $items,
        ];
    }

    /**
     * @inheirtDoc
     */
    public function setLimit($offset, $size)
    {
        /** No Collection, method is overridden to avoid exception */
    }

    /**
     * @inheirtDoc
     */
    public function addOrder($field, $direction)
    {
        /** No Collection, method is overridden to avoid exception */
    }

    /**
     * @inheirtDoc
     */
    public function addFilter(Filter $filter)
    {
        /** No Collection, method is overridden to avoid exception */
    }
}
