<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 08.04.2015
 * Time: 10:43
 */

class ShopConfig extends FeedConfig {

    /**
     * @return stdClass
     */
    public function getShopLanguageConfig()
    {
        $oConfig = new stdClass();
        $aLanguages = $this->getLanguagesArray();
        $oConfig->key = "language";
        $oConfig->title = "language";
        foreach ($aLanguages as $language) {
            $oValue = new stdClass();
            $oValue->key = $language['code'];
            $oValue->title = $language['name'];
            $oConfig->values[] = $oValue;
        }

        return $oConfig;
    }


    //function for checking if column products_id exist in tables $table


    public function getLanguagesArray()
    {
        $db = $GLOBALS['db'];
        $query = $db->Execute("SELECT languages_id as id, code, name FROM " . TABLE_LANGUAGES);

        $rez = $this->dataFetch($query);

        return $rez;
    }

    /**
     * @return stdClass
     */
    public function getShopCondition()
    {
        $values = array(
            0 => 'export_all_products',
            1 => 'export_active_products',
            2 => 'export_products_in_stock',
            3 => 'export_active_products_in_stock',
        );

        $stdConfig = new stdClass();
        $stdConfig->key = 'status';
        $stdConfig->title = 'status';
        foreach ($values as $key => $title) {
            $stdValue = new stdClass();
            $stdValue->key = $key;
            $stdValue->title = $title;
            $stdConfig->values[] = $stdValue;
        }

        return $stdConfig;
    }




    /**
     * @return stdClass
     */
    public function getShopCurrencyConfig()
    {
        $oConfig = new stdClass();
        $aCurrencies = $this->getCurrencyArray();
        $oConfig->key = "currency";
        $oConfig->title = "currency";
        foreach ($aCurrencies as $oCurrency) {
            $oValue = new stdClass();
            $oValue->key = $oCurrency['code'];
            $oValue->title = $oCurrency['title'];
            $oConfig->values[] = $oValue;
        }

        return $oConfig;
    }


    public function getCurrencyArray()
    {
        $db = $GLOBALS['db'];
        $query = $db->Execute("SELECT currencies_id as id, code, title FROM " . TABLE_CURRENCIES);

        $rez = $this->dataFetch($query);

        return $rez;
    }



}