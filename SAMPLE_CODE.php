<?php
/**
 * Copyright (c) 2015-present, Facebook, Inc. All rights reserved.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

require __DIR__ . '/vendor/autoload.php';

use FacebookAds\Object\Business;
use FacebookAds\Object\ProductCatalog;
use FacebookAds\Object\ProductFeed;
use FacebookAds\Object\ProductSet;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\AdSet;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdPreview;
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;

$access_token = 'EAAO6rKmQVsEBAGq4P4x73dQLmaWNP84axNNO0OjYasVwi7OX8pLWoQgdhc1lRl1ZCZCoHyiaDBMz11geCwJObXXNNjAXe27ZB8gyvQCrzdzxnUD1kxIDCU3zqNwNbnn4dfC635IfJZCCN72SS14P9n9XlxGdDo29UcyX1RAB1SwZBxVzmbkvM';
$app_secret = '5c6292aab948aa3d469e9d8ee9f1b956';
$ad_account_id = '951842825287997';
$business_id = '459043744286616';
$page_id = '247603552097304';
$pixel_id = '637832650482560';
$app_id = '1049675672082113';

$api = Api::init($app_id, $app_secret, $access_token);
$api->setLogger(new CurlLogger());

$fields = array(
);
$params = array(
  'name' => 'Test Catalog',
);
$product_catalog = (new Business($business_id))->createOwnedProductCatalog(
  $fields,
  $params
);
$product_catalog_id = $product_catalog->id;
echo 'product_catalog_id: ' . $product_catalog_id . "\n\n";

$fields = array(
);
$params = array(
  'name' => 'Test Feed',
  'schedule' => array('interval' => 'DAILY','url' => 'https://developers.facebook.com/resources/dpa_product_catalog_sample_feed.csv','hour' => '22'),
);
echo json_encode((new ProductCatalog($product_catalog_id))->createProductFeed(
  $fields,
  $params
)->getResponse()->getContent(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'All Product',
);
$product_set = (new ProductCatalog($product_catalog_id))->createProductSet(
  $fields,
  $params
);
$product_set_id = $product_set->id;
echo 'product_set_id: ' . $product_set_id . "\n\n";

$fields = array(
);
$params = array(
  'external_event_sources' => array($pixel_id),
);
echo json_encode((new ProductCatalog($product_catalog_id))->createExternalEventSource(
  $fields,
  $params
)->getResponse()->getContent(), JSON_PRETTY_PRINT);

$fields = array(
);
$params = array(
  'name' => 'My Campaign',
  'objective' => 'PRODUCT_CATALOG_SALES',
  'promoted_object' => array('product_catalog_id' => $product_catalog_id),
  'status' => 'PAUSED',
);
$campaign = (new AdAccount($ad_account_id))->createCampaign(
  $fields,
  $params
);
$campaign_id = $campaign->id;
echo 'campaign_id: ' . $campaign_id . "\n\n";

$fields = array(
);
$params = array(
  'name' => 'My AdSet',
  'optimization_goal' => 'OFFSITE_CONVERSIONS',
  'billing_event' => 'IMPRESSIONS',
  'bid_amount' => '20',
  'promoted_object' => array('product_set_id' =>  $product_set_id),
  'daily_budget' => '1000',
  'campaign_id' => $campaign_id,
  'targeting' => array('geo_locations' => array('countries' => array('US'))),
  'status' => 'PAUSED',
);
$ad_set = (new AdAccount($ad_account_id))->createAdSet(
  $fields,
  $params
);
$ad_set_id = $ad_set->id;
echo 'ad_set_id: ' . $ad_set_id . "\n\n";

$fields = array(
);
$params = array(
  'name' => 'My Creative',
  'object_story_spec' => array('page_id' =>  $page_id, 'template_data' =>  array('call_to_action' =>  array('type' =>  'SHOP_NOW'), 'link' =>  'www.example.com', 'name' =>  'array(array(product.name)) - array(array(product.price))', 'description' =>  'array(array(product.description))', 'message' =>  'array(array(product.name | titleize))')),
  'applink_treatment' => 'web_only',
  'product_set_id' => $product_set_id,
  'url_tags' => 'utm_source=facebook',
);
$creative = (new AdAccount($ad_account_id))->createAdCreative(
  $fields,
  $params
);
$creative_id = $creative->id;
echo 'creative_id: ' . $creative_id . "\n\n";

$fields = array(
);
$params = array(
  'name' => 'My Ad',
  'adset_id' => $ad_set_id,
  'creative' => array('creative_id' => $creative_id),
  'tracking_specs' => array( array('action_type' =>  array('offsite_conversion'), 'fb_pixel' =>  array($pixel_id)) ),
  'status' => 'PAUSED',
);
$ad = (new AdAccount($ad_account_id))->createAd(
  $fields,
  $params
);
$ad_id = $ad->id;
echo 'ad_id: ' . $ad_id . "\n\n";

$fields = array(
);
$params = array(
  'ad_format' => 'DESKTOP_FEED_STANDARD',
);
echo json_encode((new Ad($ad_id))->getPreviews(
  $fields,
  $params
)->getResponse()->getContent(), JSON_PRETTY_PRINT);

