<?php

namespace Drupal\paragraph_migration\Controller;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;

class MigrateProductAccordion {

  public static function migrateProductAccordion($accordion_array, &$context){
    $message = 'Migrating Accordion...';
    $results = array();
    
    $i=0;
    $temp = "";
    foreach ($accordion_array as $key => $value) {
     if(is_numeric($value[0])){
      $field_accordion_section_1_value = !empty($value[1]) ? $value[1] : "";
      $field_accordion_section_1_format = "full_html";
      $accordion = array(
          "value"  =>  $field_accordion_section_1_value,
          "format" => $field_accordion_section_1_format
      );
        $nid = ($temp == $value[0]) ? $temp : $value[0];
        
        $node = Node::load($nid);
        $node->field_accordion_section_1[] = $accordion;
        
        $results[] = $node->save();

        $temp = $value[0];
      }
      $i++;
    }
    $context['message'] = $message;
    $context['results'] = $results;
    //exit();
  }

  function deleteNodeExampleFinishedCallback($success, $results, $operations) {
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