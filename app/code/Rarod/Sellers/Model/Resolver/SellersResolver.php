<?php
namespace Rarod\Sellers\Model\Resolver;

use Rarod\Sellers\Helper\Data as SellersHelper;
use Magento\CatalogInventory\Api\StockStateInterface as StockStateInterface;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\Resolver\ValueFactory;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

/**
 * Sellers information resolver, used for GraphQL request processing.
 */

class SellersResolver implements ResolverInterface
{
    /**
     * @var ValueFactory
     */
    private $valueFactory;

    /**
     * @var ServiceOutputProcessor
     */
    private $serviceOutputProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    private $sellersHelper;
    /**
     *
     * @param ValueFactory $valueFactory
     * @param ServiceOutputProcessor $serviceOutputProcessor
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        ValueFactory $valueFactory,
        ServiceOutputProcessor $serviceOutputProcessor,
        ExtensibleDataObjectConverter $dataObjectConverter,
        StockStateInterface $stockState,
        StoreManager $storeManager,
        SellersHelper $sellersHelper
    ) {
        $this->valueFactory = $valueFactory;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->sellersHelper = $sellersHelper;
        $this->stockState = $stockState;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)  {

        if (!isset($args['id'])) {
            throw new GraphQlAuthorizationException(
                __(
                    'id for customer should be specified'
                )
            );
        }
        try {

            $sellerData = $this->sellersHelper->getSellerById($args['id']);
            $sellerWebsite = $this->storeManager->getWebsite($sellerData['website_id'])->getName();

            return [
                'name' => $sellerData['name'],
                'website' => $sellerWebsite,
                'phone' => $sellerData['phone']
            ];

        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        } catch (LocalizedException $exception) {
            throw new GraphQlNoSuchEntityException(__($exception->getMessage()));
        }
    }

}