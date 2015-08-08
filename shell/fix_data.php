<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Shell
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once 'abstract.php';

/**
 * Magento Log Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_Fix_Data extends Mage_Shell_Abstract
{

    const xtrader_link = 'http://www.dropshipping.co.uk/catalog/xml_id_xl_multi_info.xml';

    const new_line = "\n";

    // const xtrader_msg = 'Xtrader'

    private $response_xml_data = false;
    /**
     * Run script
     *
     */
    public function run(){

      // echo var_dump($this->_args['images']) . "\n"; exit;
      echo self::xtrader_link . self::new_line;
      if($this->getXtrader()){
        echo "fix data is running\n";
        $this->syncXtraderToMage();
      }
    }

    /**
     * Sets thumbnail and small image to value of image if not set
     * 
     */
    public function setUnsetImages(){
      
      $products = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToFilter('entity_id',array('gt' => '1652')) //memory limit may kick in
        ->addAttributeToSelect(array('image', 'small_image', 'thumbnail'));
        foreach ($products as $product) {
          if ($product->hasImage()){
            if (!$product->hasSmallImage()) $product->setSmallImage($product->getImage())->save();
            if (!$product->hasThumbnail()) $product->setThumbnail($product->getImage())->save();
            if (!$product->hasBaseImage()) $product->setBaseImage($product->getImage())->save();
          }elseif($product->hasSmallImage()){
            $product->setImage($product->getSmallImage())->save();
          }elseif($product->hasMulti()){
            $product->setImage($product->getMulti())->save();
          }elseif($product->hasMulti2()){
            $product->setImage($product->getMulti2())->save();
          }elseif($product->hasBigMulti1()){
            $product->setImage($product->getBigMulti1())->save();
          }elseif($product->hasBigMulti2()){
            $product->setImage($product->getBigMulti2())->save();
          }elseif($product->hasXimage()){
            $product->setImage($product->getXimage())->save();
          }else{
            $product = $this->setImagesForOneProduct($product->getId());
            echo $product->getImage();
            echo "Sku: " .$product->getSku() . " no images found\n";
          }

          $product->save();
          echo $product->getId()."\n";
        }
        echo 'Finished!';
    }

    public function setImagesForOneProduct($id){
      $product = Mage::getModel('catalog/product')->load($id);

          if ($product->hasImage()){
            if (!$product->hasSmallImage()) $product->setSmallImage($product->getImage())->save();
            if (!$product->hasThumbnail()) $product->setThumbnail($product->getImage())->save();
            if (!$product->hasBaseImage()) $product->setBaseImage($product->getImage())->save();
          }elseif($product->hasSmallImage()){
            $product->setImage($product->getSmallImage())->save();
          }elseif($product->hasMulti()){
            $product->setImage($product->getMulti())->save();
          }elseif($product->hasMulti2()){
            $product->setImage($product->getMulti2())->save();
          }elseif($product->hasBigMulti1()){
            $product->setImage($product->getBigMulti1())->save();
          }elseif($product->hasBigMulti2()){
            $product->setImage($product->getBigMulti2())->save();
          }elseif($product->hasXimage()){
            $product->setImage($product->getXimage())->save();
          }else{
            echo "Sku: " .$product->getSku() . " no images found\n";
          }
          echo "\n";
          return $product;
    }

    public function setStock(){
      $products = Mage::getModel('catalog/product')->getCollection();
      echo $products->count()."\n";
      foreach ($products as $product) {
        $stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product);
        if (!$stock_item->getId()) {
            $stock_item->setData('product_id', $product_id);
            $stock_item->setData('stock_id', 1); 
        }

        $stock_item->setData('is_in_stock', 1); // is 0 or 1
        $stock_item->setData('manage_stock', 0); // should be 1 to make something out of stock
        $stock_item->setData('qty', 100);
        try {
            $stock_item->save();
        } catch (Exception $e) {
            echo "{$e}\n";
        }
        echo $product->getId()."\n";
      }
    }
    /**
     * @param $product = mage product loaded by MODEL attribute from xml
     * @param $productInfo = 1 product node from xml
     * perform task based on args
     */
    private function _do($product,$productInfo){

      if($this->_args['images']){
        
        $images = array(
          'thumbnail'=>(string)$productInfo->THUMB[0],
          'small_image'=>(string)$productInfo->IMAGE[0],
          'image'=>(string)$productInfo->XIMAGE[0]
        );
        $this->addImages($product,$images);
      }else{
        echo "No action specified\n";
        die();
      }
    }

    /**
     * parse xml and find matching product in magento (by MODEL to sku)
     * call _do() function to perform tasks
     */
    private function syncXtraderToMage(){ 

      echo "Syncing Xtrader data\n";
      foreach ($this->response_xml_data->children() as $category) {
        foreach ($category as $key => $products) {
          foreach ($products as $_key => $product) {
            if($mageProduct = Mage::getModel('catalog/product')->loadByAttribute('sku', $product->MODEL[0])){
              // $this->_do($mageProduct,$product);
              echo $mageProduct->getSku()."\n";
            }else{
              $this->addMissingProduct($product);
              echo '+';
            }
          }
        }
      }
    }

    public function addMissingProduct($productInfo){

      $product = Mage::getModel('catalog/product');
       
      try{
      $product
          ->setWebsiteIds(array(1)) 
          ->setAttributeSetId( (Mage::getModel('catalog/product')->getDefaultAttributeSetId()+1) ) 
          ->setTypeId('simple') 
          ->setCreatedAt(strtotime('now')) 
          ->setSku($productInfo->MODEL[0]) //SKU
          ->setName($productInfo->NAME[0]) //product name
          ->setWeight(1)
          ->setStatus(1)
          ->setTaxClassId(4) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
          ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
          ->setManufacturer() //manufacturer id
          ->setColor($productInfo->COLOR[0])
          ->setPrice($productInfo->RRP[0]) 
          ->setCostPrice($productInfo->PRICE[0]) //price in form 11.22
          ->setMsrpEnabled(1) //enable MAP
          ->setMsrpDisplayActualPriceType(1) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
          ->setMetaTitle($productInfo->NAME[0])
          ->setMetaKeyword($productInfo->NAME[0])
          ->setMetaDescription($productInfo->NAME[0])
          ->setDescription($productInfo->DESCRIPTION[0])
          ->setShortDescription($productInfo->NAME[0])
          ->setStockData(array(
                             'use_config_manage_stock' => 0, //'Use config settings' checkbox
                             'manage_stock'=>1, //manage stock
                             'min_sale_qty'=>1, //Minimum Qty Allowed in Shopping Cart
                             'max_sale_qty'=>2, //Maximum Qty Allowed in Shopping Cart
                             'is_in_stock' => 1, //Stock Availability
                             'qty' => 999 //qty
                         )
          );
       
          // ->setCategoryIds(array(3, 10)); //assign product to categories
      $product->save();
      echo $product->getSku() . "\n";
      }catch(Exception $e){
        Mage::log($e->getMessage());
        echo $e . "\n";
      }
    }

    /**
     * read xtrader xml url and store data
     *
     */
    private function getXtrader(){
      
      try {
        $this->response_xml_data = new SimpleXMLElement(file_get_contents(self::xtrader_link));
      }catch (Exception $e){
        throw new Exception("Error Processing Request " . $e, 1);
      }
      return true;
    }

    /**
     * Add image to gallery if exists
     *
     */
    private function addImages($product, $mediaArray){

      $importDir = Mage::getBaseDir('media') . DS . 'import/';
      foreach($mediaArray as $imageType => $fileName) {
        $filePath = $importDir.$fileName;
        echo $filePath . "\n";
        if(file_exists($filePath)){
          try {
            $product->addImageToMediaGallery($filePath, $imageType, false);
            $product->save();
          }catch(Exception $e){
            echo $e->getMessage() . "\n";
          }
        }else{
          echo "Product does not have an image or the path is incorrect. Path was: {$filePath}\n";
        }
      }
    }

    /**
     * Retrieve Usage Help Message§
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f fix_data.php -- [options]

  help              This help

USAGE;
    }
}

$shell = new Mage_Shell_Fix_Data();
// $shell->run();
// $shell->setUnsetImages();
// $shell->setImagesForOneProduct();
$shell->setStock();
