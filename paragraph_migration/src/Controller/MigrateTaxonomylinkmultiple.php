<?php

namespace Drupal\paragraph_migration\Controller;

use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

class MigrateTaxonomylinkmultiple {

  public static function migrateResourcelink($link_array, &$context){
    $message = 'Migrating Taxonomy...';
    $results = array();
    
    $i=0;
    $j = 0; 
    $k= 0;
    $temp = "";
    $link = array();
    foreach ($link_array as $key => $value) {
        
        if(is_numeric($value[2])){
            
            if(!empty($value[0]) && !empty($value[1]) ){
                $term = Term::load($value[2]);
               
                $str_url = explode(',',$value[0]);
                $str_title = explode(',',$value[1]);
                
                foreach($str_url as $vu => $val_url){
                    foreach($str_title as $vt => $val_title){
                        if($vt == $vu){
                        $link[] =  array('uri' => $val_url,'title' => $val_title);
                        }
                    }
                } 

              \Drupal::logger('some_channel_name')->warning('<pre><code>'.$term->id(). print_r($link, TRUE) . '</code></pre>');    
               $term->set('field_resources_link_list',$link);

              $results[] = $term->save(); 

               unset($link);
            }


            
             
        }

        
        
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