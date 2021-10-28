<?php

namespace JRComercio\CompanyInformation\Controller\Company;

use GuzzleHttp\Client;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends Action
{
    private $scopeConfig;
    private $resultJsonFactory;

    public function __construct(JsonFactory $resultJsonFactory, ScopeConfigInterface $scopeConfig, Context $context)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
    }

    function cleanCnpj($valor){
        $valor = trim($valor);
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", "", $valor);
        $valor = str_replace("-", "", $valor);
        $valor = str_replace("/", "", $valor);
        return $valor;
    }

    public function execute()
    {
        $json = $this->resultJsonFactory->create();
        $token = $this->scopeConfig->getValue('token/general/token_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $cnpj = $this->getRequest()->getParam('cnpj');
        $cnpj = $this->cleanCnpj($cnpj);
        $client = new Client();

        $url = 'https://www.sintegraws.com.br/api/v1/execute-api.php?token=' . $token . '&cnpj=' . $cnpj . '&plugin=ST';
        $response = $client->get($url);

        return $json->setJsonData($response->getBody());
    }
}
