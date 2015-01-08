<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Webapi\Model\Rest\Config as RestConfig;

/**
 * Class InvoiceCommentsListTest
 */
class InvoiceCommentsListTest extends WebapiAbstract
{
    const SERVICE_NAME = 'salesInvoiceManagementV1';

    const SERVICE_VERSION = 'V1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/invoice.php
     */
    public function testInvoiceCommentsList()
    {
        $comment = 'Test comment';
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Sales\Model\Resource\Order\Invoice\Collection $invoiceCollection */
        $invoiceCollection = $objectManager->get('Magento\Sales\Model\Resource\Order\Invoice\Collection');
        $invoice = $invoiceCollection->getFirstItem();
        $invoiceComment = $objectManager->get('Magento\Sales\Model\Order\Invoice\Comment');
        $invoiceComment->setComment($comment);
        $invoiceComment->setParentId($invoice->getId());
        $invoiceComment->save();

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/invoice/' . $invoice->getId() . '/comments',
                'httpMethod' => RestConfig::HTTP_METHOD_GET,
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'getCommentsList',
            ],
        ];
        $requestData = ['id' => $invoice->getId()];
        // TODO Test fails, due to the inability of the framework API to handle data collection
        $result = $this->_webApiCall($serviceInfo, $requestData);
        foreach ($result['items'] as $item) {
            /** @var \Magento\Sales\Model\Order\Invoice\Comment $invoiceHistoryStatus */
            $invoiceHistoryStatus = $objectManager->get('Magento\Sales\Model\Order\Invoice\Comment')
                ->load($item['entity_id']);
            $this->assertEquals($invoiceHistoryStatus->getComment(), $item['comment']);
        }
    }
}
