<?php
/**
 * @category    Halex
 * @package     Halex\CronExtended
 * @author      Aleksejs Prjahins <aleksejs.prjahins@gmail.com>
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 */

declare(strict_types=1);

namespace Halex\CronExtended\Controller\Adminhtml\Jobs;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Index
 */
class Index extends Action implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * ACL
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Halex_CronExtended::jobs';

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $rawFactory
     */
    public function __construct(
        Context $context,
        PageFactory $rawFactory
    ) {
        $this->pageFactory = $rawFactory;

        parent::__construct($context);
    }

    /**
     * Add the main Admin Grid page
     *
     * @return Page
     */
    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();

        $resultPage->setActiveMenu('Halex_CronExtended::jobs')
            ->getConfig()
            ->getTitle()
            ->prepend(__('Cron Jobs'));

        return $resultPage;
    }
}
