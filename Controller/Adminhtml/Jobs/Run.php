<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Controller\Adminhtml\Jobs;

use Halex\CronExtended\Model\CronManager;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Run
 */
class Run extends Action implements HttpGetActionInterface
{
    /**
     * @var CronManager
     */
    private CronManager $cronManager;

    /**
     * ACL
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Halex_CronExtended::jobs_run';

    /**
     * Constructor
     *
     * @param Context $context
     * @param CronManager $cronManager
     */
    public function __construct(
        Context $context,
        CronManager $cronManager
    ) {
        parent::__construct($context);

        $this->cronManager = $cronManager;
    }

    /**
     * Run Cron Job
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $jobCode = $this->getRequest()->getParam('job_code');

        if (!$jobCode) {
            $this->messageManager->addErrorMessage(
                __('Cron job code must be specified!')
            );

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->cronManager->runJob($jobCode);
        } catch (LocalizedException $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/');
        }

        $this->messageManager->addSuccessMessage(__('Cron job %1 was successfully executed.', $jobCode));

        return $resultRedirect->setPath('*/*/');
    }
}
