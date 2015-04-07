<?php

class FeedConnector implements FeedPlugin
{

    protected $productsWithAttributes;
    protected $order;
    protected $config;

    /**
     * constructor caller is forwarded
     *
     * @param Feed $container
     */
    public function __construct(Feed $container)
    {
        $this->config = new FeedConfig();
    }

    /**
     * Returns APIUsername
     * @return string
     */
    public function getApiUsername()
    {
        return $this->config->getConfig('FEED_USER');
    }

    /**
     * Return APIPassword
     * @return string
     */
    public function getApiPassword()
    {
        return $this->config->getConfig('FEED_PASS');
    }

    /**
     * Returns APISecret code
     * @return string
     */
    public function getApiSecret()
    {
        return $this->config->getConfig('FEED_SECRET');
    }

    /**
     * Returns identifyer (oxid, magento, opencart)
     * @return string
     */
    public function getShopName()
    {
        return 'zencart';//$this->config->getConfig('STORE_NAME');
    }

    /**
     * Generates and returns the array of datafeed
     *
     * @param stdClass $queryParameters
     * @param array $fieldMap
     * @return stdClass
     */
    public function getFeed(stdClass $queryParameters, array $fieldMap)
    {
        set_time_limit(0);
        $this->config->iniParameters();
        $limit = 10;
        $offset = 0;
        $tempContents = array();

        //save sessions cart contents
        if ($_SESSION['cart']->contents) {
            $tempContents = $_SESSION['cart']->contents;
            $_SESSION['cart']->reset();
        }

        header('Content-Encoding: UTF-8');
        header("Content-type: text/csv; charset=UTF-8");
        header('Content-Disposition: attachment; filename=feed.csv');
        mb_internal_encoding("UTF-8");


        $csv_file = fopen("php://output", 'w+');
        if (!$csv_file) {
            echo 'File Error';
            exit();
        }
        fputcsv($csv_file, array_keys($fieldMap), ';', '"');
        $shopConfig = $this->getShopConfig();
        do {
            $products = $this->config->getProducts($limit, $offset, $queryParameters);
            $attributes = $this->config->getProductsAttr();
            $count = 0;

            foreach ($products as $product) {
                $this->config->uploadCSVfileWithCombinations($csv_file, $product, $attributes, $fieldMap, $queryParameters, null);
                flush();
                ++$count;
            }

            $offset += $limit;
        } while ($count == $limit);

        fclose($csv_file);
        if ($tempContents) {
            $_SESSION['cart']->contents = $tempContents;
        }

    }

    /**
     * Returns possible shop configuration option for different channels
     * @return stdClass
     */
    public function getShopConfig()
    {
        $shopConfig = new stdClass();
        $shopConfig->language = $this->config->getShopLanguageConfig();
        $shopConfig->currency = $this->config->getShopCurrencyConfig();
        $shopConfig->status = $this->config->getShopCondition();

        return $shopConfig;
    }

    /**
     * Returns the URL where to get generated DataFeed
     *
     * @param stdClass $queryParameters
     * @param array $fieldMap
     * @return string
     */
    public function getFeedUrl(stdClass $queryParameters, array $fieldMap = null)
    {
        // TODO: Implement getFeedUrl() method.
    }

    /**
     * Generates and returns the delta changes array
     *
     * @param stdClass $queryParameters
     * @param array $fieldMap
     * @param int $deltaTimestamp
     * @return stdClass
     */
    public function getDelta(stdClass $queryParameters, array $fieldMap, int $deltaTimestamp)
    {
        // TODO: Implement getDelta() method.
    }

    /**
     * Generates and returns the orders
     *
     * @param int $deltaTimestamp
     * @return stdClass
     */
    public function getOrders(int $deltaTimestamp)
    {

    }

    /**
     * Returns the url from where to get the article
     *
     * @param int $deltaTimestamp
     * @return string
     */
    public function getOrdersUrl(int $deltaTimestamp)
    {
        // TODO: Implement getOrdersUrl() method.
    }

    /**
     * Returns the bridge URL throw the Feed is communicating with shop.
     *
     * @return string
     */
    public function getBridgeUrl()
    {
        return 'http://' . $_SERVER['SERVER_NAME'] . '/feed.php';
    }

    /**
     * Returns the bridge URL parameters the Feed is communicating with shop.
     *
     * @return string
     */
    public function getUrlParameters()
    {
        // TODO: Implement getUrlParameters() method.
    }

    /**
     * Returns posible shop fields configuration throw the Feed gets csv fields
     * @return stdClass
     */
    public function getShopFields()
    {
        return array_merge(FeedConfig::$gReturn, $this->config->getQueryFields());
    }

    /**
     * Returns product info
     *
     * @param stdClass $queryParameters
     * @param array $fieldMap
     * @param string $id
     * @return mixed
     */
    public function getProductInfo(stdClass $queryParameters, array $fieldMap, $id)
    {

        $array = explode('_', $id);
        $productId = (int)$array[0];
        $product = $this->config->getProducts(null, null, $queryParameters, $productId);

        $attributes = $this->config->getProductsAttr();

        $shopConfig = $this->getShopConfig();
        $product = $product[0];
        $result = $this->config->uploadCSVfileWithCombinations(null, $product, $attributes, $fieldMap, $queryParameters, $id);
        print_r($result);
    }

    /**
     * @param stdClass $queryParameters
     * @param $id
     * @return mixed
     */
    public function getOrderProducts(stdClass $queryParameters, $id)
    {
        $products = null;


        if ($queryParameters->currency) {
            $currency = $this->config->getCurrencyId($queryParameters->currency);
            $products = $this->config->getOrdersProducts($currency, $id);
        }

        $result = array();
        $i = 0;
        foreach ($products as $item) {

            $result[$i]['ModelOwn'] = $item['attributes']['ModelOwn'];
            $result[$i]['Quantity'] = $item['product']['qty'];
            $result[$i]['BasePrice'] = $item['product']['price'];
            $result[$i]['Currency'] = $item['product']['code'];
            $i++;
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function getFeatures()
    {
        $return = array(
            'getShopName',
            'getConfig',
            'getFeed',
            'getFields',
            'getBridgeUrl',
//            'getFeedUrl',
//            'getDelta',
//            'getOrders',
//            'getOrdersUrl',
            'getProduct',
            'getOrderProducts',
        );
        return $return;
    }
}