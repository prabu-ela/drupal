<?php

namespace Drupal\custom_product_migration\Controller;

use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_product\Entity\Product;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;

/**
 *
 */
class MigrateProduct {

  /**
   *
   */
  public static function migrateProduct($product_array, &$context) {

    $message = 'Migrating Product...';
    $results = [];

    $i = 0;
    $temp = "";
    $session_product_data = [];
    foreach ($product_array as $key => $value) {

      if ($key != 0) {
        $user = \Drupal::currentUser();
        $session_product_data['type'] = $value['Type'];
        $session_product_data['title'] = trim($value['Title']);
        $type = "";
        $pricetable = [];
        // $pricetable[0] = array("field_price_table_amount"=>1,"field_price_table_min_qty"=>2,"field_price_table_max_qty"=>3);
        if (isset($value['Price Table'])) {
          $str = $value['Price Table'];
          $tableHead = explode("<tr>", $str);
          $tableHead = explode("</tr>", $tableHead[1]);
          $tableHead = explode("</th>", $tableHead[0]);
          $arrayHead = [];
          $i = 0;
          foreach ($tableHead as $det) {
            $i++;
            if ($i > 1 && count($tableHead) != $i) {
              $removeTh = str_replace("<th>", "", $det);
              $arrayHead[] = $removeTh;
            }
          }
          $tableValue = explode('<tr class="odd">', $str);
          $tableValue = explode("</tr>", $tableValue[1]);
          $tableValue = explode("</td>", $tableValue[0]);
          $arrayValue = [];
          $i = 0;
          foreach ($tableValue as $det) {
            $i++;
            if ($i > 1 && count($tableValue) != $i) {
              $removeTh = str_replace("<td>", "", $det);
              $arrayValue[] = $removeTh;
            }
          }
          $i = 0;
          foreach ($arrayValue as $det) {
            $inArry = [];
            $minMax = explode("-", $arrayHead[$i]);
            $inArry['min_qty'] = trim($minMax[0]);
            $inArry['max_qty'] = trim(str_replace("Unlimited", "-1", $minMax[1]));
            $inArry['amount'] = str_replace("$", "", $det);
            $inArry['currency_code'] = "USD";
            $pricetable[] = $inArry;
            $i++;
          }
        }

        $value['Price'] = str_replace('$', '', $value['Price']);

        // $variation = array();
        if ($value['Type'] == "Scatch Cards") {
          $variation = ProductVariation::create([
            'title' => trim($value['Title']),
            'type' => 'multi_typed_products',
            'sku' => trim($value['SKU']),
            'price' => new Price((float) $value['Price'], 'USD'),
            'field_price_table' => $pricetable,
            'status' => $value['Status'],
            // 'list_price' => new Price((float) $value['Average Price'], 'USD'),
            'avatax_tax_code' => 0,
          ]);

          $type = "multi_typed_products";
        }
        else {
          $variation = ProductVariation::create([
            'title' => trim($value['Title']),
            'type' => 'default',
            'sku' => trim($value['SKU']),
            'price' => new Price((float) $value['Price'], 'USD'),
            'field_price_table' => $pricetable,
            'status' => $value['Status'],
           // 'list_price' => new Price((float) $value['Average Price'], 'USD'),
            'avatax_tax_code' => 0,
          ]);
          $type = "product";
        }

        // print_r($variation);exit;
        $variations = [$variation];

        if (!empty($value['Related Image'])) {

          $related_image = $value['Related Image'];
          $related_image1 = file_get_contents($related_image, TRUE);
          $remove_http = str_replace('http://', '', $related_image);

          $split_parameters = explode('/', $remove_http);

          if (!empty($split_parameters) && count($split_parameters) > 1) {
            $filesaved = file_save_data($related_image1, 'public://' . end($split_parameters), 'FILE_EXISTS_REPLACE');
            $related_image_alt = $value['Related Image Alt'];
            $related_image_title = $value['Related Image Caption'];

            $file = File::create([
              'uri' => end($split_parameters),
            ]);
            $file->save();
            $string = $file->getFilename();
            $pieces = explode("?", $string);
            $related_name = $pieces[0];
            $related_image_title_tid = $file->id();
          }
        }
        else {

          $related_image_alt = "";
          $related_image_title = "";
          $related_image_title_tid = "";

        }

        if (!empty($value['Related File'])) {

          $related_file = $value['Related File'];
          $related_file1 = file_get_contents($related_file, TRUE);
          $remove_http = str_replace('https://', '', $related_file);
          $split_parameters = explode('/', $remove_http);
          if (!empty($split_parameters) && count($split_parameters) > 1) {
            $filesaved = file_save_data($related_file1, 'public://' . end($split_parameters), 'FILE_EXISTS_REPLACE');

            $file = File::create([
              'uri' => end($split_parameters),
            ]);

            $file->save();
            $related_file_title_tid = $file->id();

          }
          // $session_product_data['field_product_file'] = self::related_file($value['Related File']);
        }
        else {
          $related_file_title_tid = "";
        }

        $entity_manager = \Drupal::entityManager();
        $store = $entity_manager->getStorage('commerce_store')->loadDefault();
        // if($value['Type'] != "Scatch Cards"){.
        if (!empty($value['Related Image'])) {
          $media = Media::create([
            'bundle' => 'image',
            'uid' => $user->id(),
            'status' => 1,
            'field_media_image' => [
              'target_id' => $file->id(),
              'alt' => $value['Related Image Alt'],
              'title' => $value['Related Image Caption'],
            ],
          ]);
          $media->save();

          $related_image_title_tid = $media->id();
        }
        else {
          $related_image_title_tid = '';
        }

        $product_array_create = [
          'type' => $type,
          'product_id' => $value['Product ID'],
          'title' => trim($value['Title']),
          'uid' => $user->id(),
          'body' => ['value' => $value['Body'], 'summary' => $value['summary'],'format' => "full_html"],
          'body_format' => "full_html",
          'field_average_price' => new Price((float) $value['Average Price'], 'USD'),
          'field_multi_type' => $value['Card Type'],
          'field_packaging' => $value['Packaging'],
            // 'field_commerce_price' => new Price((float) $value['Price'], 'USD'),
            // 'field_sku' => $value['SKU'],
          'status' => $value['Status'],
          'field_media_product_image' => [
            'target_id' => $related_image_title_tid,
            'alt' => $related_image_alt,
            'title' => $related_image_title,
          ],
          'stores' => $store->id(),
          'field_product_file' => [
            'target_id' => $related_file_title_tid,
          ],
          'field_product_term' => ['target_id' => $value['Product Type']],
          'field_design_form' => $value['design form'],
          'variations' => $variations,
        ];
        // }
        $product = Product::create($product_array_create);
        \Drupal::logger('my_module')->notice(json_encode($product_array_create));
        $results[] = $product->save();
        unset($product_array_create);
      }
    }

    // print_r($results);
    $context['message'] = $message;
    $context['results'] = $results;
    // exit();
  }

  /**
   *
   */
  public function variation($value) {
    return [
      'type' => $value['Type'],
      'sku' => trim($value['SKU']),
      'price' => new Price($value['Price'], 'USD'),
      'status' => $value['Status'],
      'list_price' => $value['Average Price'],
    ];
  }

  /**
   *
   */
  public static function related_image($related, $related_alt, $related_title) {
    $related_image = $related;
    $related_image1 = file_get_contents($related_image, TRUE);
    $remove_http = str_replace('https://', '', $related_image1);
    $split_parameters = explode('/', $remove_http);
    \Drupal::logger('some_channel_name1')->warning('<pre><code>' . print_r($split_parameters, TRUE) . '</code></pre>');
    if (!empty($split_parameters)) {
      $filesaved = file_save_data($related_image1, 'public://' . end($split_parameters), 'FILE_EXISTS_REPLACE');
      $related_image_alt = $related_alt;
      $related_image_title = $related_title;

      $file = File::create([
        'uri' => end($split_parameters),
      ]);
      $file->save();
      $string = $file->getFilename();
      $pieces = explode("?", $string);
      $related_name = $pieces[0];
      $related_image_title_tid = $file->id();
      return ['target_id' => $related_image_title_tid, 'alt' => $related_image_alt, 'title' => $related_image_title];
    }
  }

  /**
   *
   */
  public static function related_file($related) {
    $related_file = $related;
    $related_file1 = file_get_contents($related_file, TRUE);
    $remove_http = str_replace('http://', '', $related_file1);
    $split_parameters = explode('/', $remove_http);
    if (!empty($split_parameters)) {
      $filesaved = file_save_data($related_file1, 'public://' . end($split_parameters), 'FILE_EXISTS_REPLACE');

      $file = File::create([
        'uri' => end($split_parameters),
      ]);

      $file->save();
      $related_image_title_tid = $file->id();
      return ['target_id' => $related_image_title_tid];
    }

  }

  /**
   *
   */
  public function deleteNodeExampleFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

}
