<?php

namespace Drupal\content_import_data\Controller;

/**
 * @file
 * Contains \Drupal\content_import_data\Controller\NodeInsertion.
 */

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use \Drupal\Core\File\FileSystemInterface;

/**
 * Initialize class.
 */
class NodeInsertion extends ControllerBase {

  /**
   * For user edit and update.
   */
  public function nodeimport() {
    $result = [];
    header('Content-type: application/json');
    $input = file_get_contents('php://input');
    $nodedata = json_decode($input, TRUE);
    $nodedata = json_decode($nodedata);
    $userdataname = [];

 

    if (!empty($nodedata[0]->nid) && (isset($nodedata[0]->nid))) {
      $node = Node::load($nodedata[0]->nid);

      if (isset($nodedata[0]->field_author_subscriptions->und)) {
        $field_author_subscriptions = $nodedata[0]->field_author_subscriptions->und;
        foreach ($field_author_subscriptions as $key => $value) {
          $field_author_subscription[] = $value->value;
        }
      }
      else {
        $field_author_subscription = '';
      }
      if (isset($nodedata[0]->vid)) {
        $vid = $nodedata[0]->vid;
      }
      else {
        $vid = '';
      }
      $uid = "";
      if (isset($nodedata[0]->uid)) {
        $uid = $nodedata[0]->uid;
      }

      if (isset($nodedata[0]->type)) {
        $type = $nodedata[0]->type;
      }
      else {
        $type = '';
      }
      $media_org_image = "";
      if (isset($nodedata[4][0][1]) && $type == "org") {  //Logo org get 
        $logo_image = $nodedata[4][0][0];
        $logo_image1 = file_get_contents($logo_image, TRUE);
        $remove_http = str_replace('http://', '', $logo_image);
        $split_parameters = explode('/', $remove_http);
        $media_org_image = "";
        if(!empty($split_parameters)){
          $filesaved = file_save_data($logo_image1, 'public://'.$split_parameters[7] , 'FILE_EXISTS_REPLACE');
          $logo_image_alt = $nodedata[4][0][1];
          $logo_image_title = $nodedata[4][0][2];

          $file = File::create([
          'uri' => $split_parameters[7],
          ]);

          $file->save();
          $string = $file->getFilename();
          $pieces = explode("?", $string);
          $logo_name = $pieces[0];
          $logo_image_title_tid = $file->id();

          $media = Media::create([
          'bundle'=> 'image',
          'uid' => $uid,
          'status' => 1,
          'field_media_image' => [
          'target_id' => $file->id(),
          'alt' => $logo_image_alt,
          'title' => $logo_image_title,
          ],
          ]);
          $media->save();
        }
        else{
          $media_org_image = 1;
        }


      }
      else {
        $logo_image = '';
      }
      
      if (isset($nodedata[4][1][0]) && $type == "article") { // For the field article image
        $field_article_image = $nodedata[4][1][0];
        $field_article_image1 = file_get_contents($field_article_image, TRUE);
        $field_article_image_alt = $nodedata[4][1][1];
        $field_article_image_title = $nodedata[4][1][2];
        $media_article_image = "";
        if(!empty($split_parameters)){

          $filesaved = file_save_data($field_article_image, 'public://'.end($split_parameters) , 'FILE_EXISTS_REPLACE');

          $file1 = File::create([
            //'uri' => $field_article_image,
            'uri' => end($split_parameters),
          ]);
          $file1->save();
          $string = $file1->getFilename();
          $pieces = explode("?", $string);
          $field_article_image_name = $pieces[0];
          $field_article_image_tid = $file1->id();

          $media_article_img = Media::create([
            'bundle'=> 'image',
            'uid' => $uid,
            'status' => 1,
            'field_media_image' => [
              'target_id' => $file1->id(),
              'alt' => $field_article_image_alt,
              'title' => $field_article_image_title,
            ],
          ]);
          $media_article_img->save();
        }
        else{
          $media_article_image = 1;
        }

      }
      else {
        $field_article_image = '';
      }

      if (isset($nodedata[4][2][0]) && $type == "article") { // For the Home page image
        $field_aux_article_image = $nodedata[4][2][0];
        $field_aux_article_image1 = file_get_contents($field_aux_article_image, TRUE);
        $field_aux_article_image_alt = $nodedata[4][2][1];
        $field_aux_article_image_title = $nodedata[4][2][2];

        $remove_http = str_replace('http://', '', $field_aux_article_image);
        $split_parameters = explode('/', $remove_http);

        $filesaved = file_save_data($field_aux_article_image, 'public://'.end($split_parameters) , 'FILE_EXISTS_REPLACE');

        $file2 = File::create([
          //'uri' => $field_aux_article_image,
          'uri' => end($split_parameters),
        ]);
        $file2->save();
        $string = $file2->getFilename();
        $pieces = explode("?", $string);
        $field_aux_article_image_name = $pieces[0];
        $field_aux_article_image_tid = $file2->id();

        $media = Media::create([
          'bundle'=> 'image',
          'uid' => $uid,
          'status' => 1,
          'field_media_image' => [
            'target_id' => $file2->id(),
            'alt' => $field_aux_article_image_alt,
            'title' => $field_aux_article_image_title,
          ],
        ]);
        $media->save();

      }
      else {
        $field_aux_article_image = '';
      }

     /* if (isset($nodedata[4][3][0]) && $type == "article") { // For the rotating image 
        $field_main_rotating_image = $nodedata[4][3][0];
        $field_main_rotating_image1 = file_get_contents($field_main_rotating_image, TRUE);

        $remove_http = str_replace('http://', '', $field_main_rotating_image);

        $split_parameters = explode('/', $remove_http);

        \Drupal::logger('some_channel_name')->warning('<pre><code>' . print_r($split_parameters, TRUE) . '</code></pre>');

        return false;

        $filesaved = file_save_data($field_main_rotating_image1, 'public://'.$split_parameters[7] , 'FILE_EXISTS_REPLACE');

        $field_main_rotating_image_alt = $nodedata[4][3][1];
        $field_main_rotating_image_title = $nodedata[4][3][2];

        $file3 = File::create([
          //'uri' => $field_main_rotating_image,
          'uri' => $split_parameters[7],
        ]);
        $file3->save();
        $string = $file3->getFilename();
        $pieces = explode("?", $string);
        $field_main_rotating_image_name = $pieces[0];
        $field_main_rotating_image_tid = $file3->id();

        $media = Media::create([
          'bundle'=> 'image',
          'uid' => $uid,
          'status' => 1,
          'field_media_image' => [
            'target_id' => $file->id(),
            'alt' => $field_main_rotating_image_alt,
            'title' => $field_main_rotating_image_title,
          ],
        ]);
        $media->save();

      }
      else {
        $field_main_rotating_image = '';
      } */

      if (isset($nodedata[4][4][0]) && $type == "author") { // For the author profile pic
        $field_author_pic = $nodedata[4][4][0];
        $field_author_pic1 = file_get_contents($field_author_pic, TRUE);

        $remove_http = str_replace('http://', '', $field_author_pic);

        $split_parameters = explode('/', $remove_http);

     

        $filesaved = file_save_data($field_author_pic1, 'public://'.$split_parameters[5] , 'FILE_EXISTS_REPLACE');
        $field_author_pic_alt = $nodedata[4][4][1];
        $field_author_pic_title = $nodedata[4][4][2];
        $file4 = File::create([
          //'uri' => $field_author_pic,
          'uri' => $split_parameters[5],
        ]);
        $file4->save();
        $string = $file4->getFilename();
        $pieces = explode("?", $string);
        $field_author_pic_name = $pieces[0];
        $field_author_pic_tid = $file4->id();

        $media = Media::create([
          'bundle'=> 'image',
          'uid' => $uid,
          'status' => 1,
          'field_media_image' => [
            'target_id' => $file4->id(),
            'alt' => $field_author_pic_alt,
            'title' => $field_author_pic_title,
          ],
        ]);
        $media->save();

      }
      else {
        $field_author_pic = '';
      }

      if (isset($nodedata[0]->field_author)) {
        $field_author = $nodedata[0]->field_author->und[0]->nid;
      }
      else {
        $field_author = '';
      }
      if (isset($nodedata[0]->field_org_org_info)) {
        $field_org_org_info = $nodedata[0]->field_org_org_info->und[0]->nid;
      }
      else {
        $field_org_org_info = '';
      }
      if (isset($nodedata[0]->field_article_source)) {
        $field_article_source = $nodedata[0]->field_article_source->und[0]->nid;
      }
      else {
        $field_article_source = '';
      }
      if (isset($nodedata[0]->field_grade)) {
        $field_grade = $nodedata[0]->field_grade->und[0]->value;
      }
      else {
        $field_grade = '';
      }
      if (isset($nodedata[0]->field_author_2)) {
        $field_author_2 = $nodedata[0]->field_author_2->und[0]->nid;
      }
      else {
        $field_author_2 = '';
      }
      if (isset($nodedata[0]->field_sub_contrib)) {
        $field_sub_contrib = $nodedata[0]->field_sub_contrib->und[0]->nid;
      }
      else {
        $field_sub_contrib = '';
      }
      if (isset($nodedata[0]->field_event_type)) {
        $field_event_type = $nodedata[0]->field_event_type->und[0]->value;
      }
      else {
        $field_event_type = '';
      }
      if (isset($nodedata[0]->field_event_hosting_group)) {
        $field_event_hosting_group = $nodedata[0]->field_event_hosting_group->und[0]->nid;
      }
      else {
        $field_event_hosting_group = '';
      }
      if (isset($nodedata[0]->field_event_date)) {
        $field_event_date = $nodedata[0]->field_event_date->und[0]->value;
        $time = strtotime($field_event_date);
        $field_event_date = \Drupal::service('date.formatter')->format($time, 'custom', 'Y-m-d\TH:i:s');
      }
      else {
        $field_event_date = '';
      }
      if (isset($nodedata[0]->field_link_to_registration)) {
        $field_link_to_registration = $nodedata[0]->field_link_to_registration->und[0]->value;
      }
      else {
        $field_link_to_registration = '';
      }
      if (isset($nodedata[0]->field_event_location)) {
        $field_event_location = $nodedata[0]->field_event_location->und[0]->value;
      }
      else {
        $field_event_location = '';
      }
      if (isset($nodedata[0]->taxonomy_vocabulary_3)) {
        $juri_id = $nodedata[0]->taxonomy_vocabulary_3->und;
        foreach ($juri_id as $value) {
          $jurisdiction_id[] = $value->tid;
        }
      }
      else {
        $jurisdiction_id = '';
      }

      foreach ($node->taxonomy_vocabulary_3 as $tid) {
        $term = taxonomy_term_load($tid[0]["tid"]);
        $jurisdiction[] = $term->name;
      }
      if (isset($nodedata[0]->uid)) {
        $uid = $nodedata[0]->uid;
      }
      else {
        $uid = '';
      }
      if (isset($nodedata[0]->title)) {
        $node_title = $nodedata[0]->title;
      }
      else {
        $node_title = '';
      }

      if (isset($nodedata[0]->field_award_type_of_law)) {
        $award_type_law = $nodedata[0]->field_award_type_of_law->und;
        foreach ($award_type_law as $key => $value) {
          $award_law[] = $value->tid;
        }
      }
      else {
        $award_law = '';
      }

      if (isset($nodedata[0]->field_award_year)) {
        $field_award_year = $nodedata[0]->field_award_year->und[0]->value;
      }
      else {
        $field_award_year = '';
      }
      if (isset($nodedata[0]->field_award_tagline)) {
        $field_award_tagline = $nodedata[0]->field_award_tagline->und[0]->value;
      }
      else {
        $field_award_tagline = '';
      }
      if (isset($nodedata[0]->field_award_description)) {
        $field_award_description = $nodedata[0]->field_award_description->und[0]->value;
      }
      else {
        $field_award_description = '';
      }
      if (isset($nodedata[0]->field_award_author)) {
        $field_award_author = $nodedata[0]->field_award_author->und[0]->nid;

      }
      else {
        $field_award_author = '';
      }
      if (isset($nodedata[0]->field_award_firm)) {
        $field_award_firm = $nodedata[0]->field_award_firm->und[0]->nid;
      }
      else {
        $field_award_firm = '';
      }
      if (isset($nodedata[0]->field_award_type_of_law)) {
        $field_award_type_of_law = $nodedata[0]->field_award_type_of_law->und[0]->tid;
      }
      else {
        $field_award_type_of_law = '';
      }
      if (isset($nodedata[0]->field_award_tile_label)) {
        $field_award_tile_label = $nodedata[0]->field_award_tile_label->und[0]->value;
      }
      else {
        $field_award_tile_label = '';
      }
      if (isset($nodedata[0]->field_award_tol_url)) {
        $field_award_tol_url = $nodedata[0]->field_award_tol_url->und[0]->value;
      }
      else {
        $field_award_tol_url = '';
      }
      if (isset($nodedata[0]->field_award_article)) {
        $field_award_article = $nodedata[0]->field_award_article->und[0]->value;
      }
      else {
        $field_award_article = '';
      }
      if (isset($nodedata[0]->field_award_author2)) {
        $field_award_author2 = $nodedata[0]->field_award_author2->und[0]->nid;
      }
      else {
        $field_award_author2 = '';
      }
      if (isset($nodedata[0]->field_award_article_title)) {
        $field_award_article_title = $nodedata[0]->field_award_article_title->und[0]->value;
      }
      else {
        $field_award_article_title = '';
      }
      if (isset($nodedata[0]->field_award_author3)) {
        $field_award_author3 = $nodedata[0]->field_award_author3->und[0]->nid;
      }
      else {
        $field_award_author3 = '';
      }
      if (isset($nodedata[0]->field_award_author4)) {
        $field_award_author4 = $nodedata[0]->field_award_author4->und[0]->nid;
      }
      else {
        $field_award_author4 = '';
      }
      if (isset($nodedata[0]->field_aoy_snippet)) {
        $field_aoy_snippet = $nodedata[0]->field_aoy_snippet->und[0]->value;
      }
      else {
        $field_aoy_snippet = '';
      }
      if (isset($nodedata[0]->field_award_author5)) {
        $field_award_author5 = $nodedata[0]->field_award_author5->und[0]->nid;
      }
      else {
        $field_award_author5 = '';
      }
      if (isset($nodedata[0]->field_award_author6)) {
        $field_award_author6 = $nodedata[0]->field_award_author6->und[0]->nid;
      }
      else {
        $field_award_author6 = '';
      }
      if (isset($nodedata[0]->field_award_author6)) {
        $field_award_author7 = $nodedata[0]->field_award_author7->und[0]->nid;
      }
      else {
        $field_award_author7 = '';
      }
      if (isset($nodedata[0]->field_award_author6)) {
        $field_award_author8 = $nodedata[0]->field_award_author8->und[0]->nid;
      }
      else {
        $field_award_author8 = '';
      }

      if (isset($nodedata[0]->field_newsletter_date)) {
        $date_data = $nodedata[0]->field_newsletter_date->und;
        foreach ($date_data as $data) {
          $newsletter_date_value = $data->value;
          $newsletter_show_todate = $data->show_todate;
          $newsletter_timezone = $data->timezone;
          $newsletter_offset = $data->offset;
          $newsletter_offset2 = $data->offset2;
          $newsletter_date_value = $data->value2;
          $newsletter_timezonedb = $data->timezone_db;
          $newsletter_date_type = $data->date_type;

        }
        $time = strtotime($newsletter_date_value);
        $field_newsletter_date = \Drupal::service('date.formatter')->format($time, 'custom', 'Y-m-d');
      }
      else {
        $field_newsletter_date = '';
      }
      if (isset($nodedata[0]->field_award_author5)) {
        $field_award_author5 = $nodedata[0]->field_award_author5->und[0]->value;
      }
      else {
        $field_award_author5 = '';
      }

      if (isset($nodedata[0]->body)) {
        $body = $nodedata[0]->body;
        foreach ($body as $body_data) {
          $body_summary = $body_data[0]->summary;
          $body_value = $body_data[0]->value;
          $body_format = $body_data[0]->format;
        }
      }
      else {
        $body = '';
      }
      if (isset($nodedata[0]->log)) {
        $log = $nodedata[0]->log;
      }
      else {
        $log = '';
      }
      if (isset($nodedata[0]->status)) {
        $status = $nodedata[0]->status;
      }
      else {
        $status = '';
      }
      if (isset($nodedata[0]->name)) {
        $author_name = $nodedata[0]->name;
      }
      else {
        $author_name = '';
      }
      if (isset($nodedata[0]->comment)) {
        $comment = $nodedata[0]->comment;
      }
      else {
        $comment = '';
      }
      if (isset($nodedata[0]->tnid)) {
        $tnid = $nodedata[0]->tnid;
      }
      else {
        $tnid = '';
      }
      // Meta Tag Data Collection
      if (isset($nodedata[0]->metatags)) {
        $metatags = $nodedata[0]->metatags->und;
       /* foreach ($metatags as $metatag) {
          $description = $metatag->description->value;
          $keywords = $metatag->keywords->value;
          $revisit = $metatag->{"revisit-after"}->value;
          $meta_title = $metatag->title->value;
        } */

        $description = $metatags->description->value;
        $keywords = $metatags->keywords->value;
        $abstract = $metatags->abstract->value;
        $newskeywords = $metatags->news_keywords->value;
        $revisit = $metatags->{"revisit-after"}->value;
        $meta_title = $metatags->title->value;
        $abstract = $metatags->abstract->value;
        $image_src = $metatags->image_src->value;
        $canonical_url = $metatags->canonical_url->value;
        $shortlink = $metatags->shortlink->value;
        $standout = $metatags->standout->value;
        $rating = $metatags->rating->value; 
        $referrer = $metatags->referrer->value; 
        $rights = $metatags->rights->value; 
        $generator = $metatags->generator->value;
        $originalsource = $metatags->{"original-source"}->value;
        $prev = $metatags->prev->value;
        $next = $metatags->next->value;
        $contentlanguage = $metatags->{"content-language"}->value;
        $geoposition = $metatags->{"geo.position"}->value;
        $geoplacename = $metatags->{"geo.placename"}->value;
        $georegion = $metatags->{"geo.region"}->value;
        $icbm = $metatags->icbm->value;
        $refresh = $metatags->refresh->value;
        $pragma = $metatags->pragma->value;
        $cachecontrol = $metatags->{"cache-control"}->value;
        $expires  = $metatags->expires->value;
        $google = $metatags->google->value;
        $set_cookie = $metatags->set_cookie->value; 


      }
      else {
        $meta_title = '';
        $description = '';
        $keywords = '';
        $revisit = '';
        $abstract = ''; 
        $image_src = '';
        $canonical_url = '';
        $shortlink = '';
        $standout = '';
        $rating = '';
        $referrer = '';
        $rights = '';
        $generator = '';
        $originalsource = '';
        $prev = '';
        $next = '';
        $contentlanguage = '';
        $geoposition = '';
        $geoplacename = '';
        $georegion = '';
        $icbm = '';
        $refresh = '';
        $pragma = '';
        $cachecontrol = '';
        $expires  = '';
        $google = '';
        $set_cookie = ''; 
        $abstract = '';

      }
      if (isset($nodedata[0]->promote)) {
        $promote = $nodedata[0]->promote;
      }
      else {
        $promote = '';
      }
      if (isset($nodedata[0]->sticky)) {
        $sticky = $nodedata[0]->sticky;
      }
      else {
        $sticky = '';
      }
      if (isset($nodedata[0]->nid)) {
        $nid = $nodedata[0]->nid;
      }
      else {
        $nid = '';
      }
      if (isset($nodedata[0]->type)) {
        $type = $nodedata[0]->type;
      }
      else {
        $type = '';
      }
      if (isset($nodedata[0]->language)) {
        $language = $nodedata[0]->language;
      }
      else {
        $language = '';
      }
      if (isset($nodedata[0]->created)) {
        $created = $nodedata[0]->created;
      }
      else {
        $created = '';
      }
      if (isset($nodedata[0]->changed)) {
        $changed = $nodedata[0]->changed;
      }
      else {
        $changed = '';
      }
      if (isset($nodedata[0]->field_author_org)) {
        $field_author_org_event = $nodedata[0]->field_author_org->und[0]->nid;
      }
      else {
        $field_author_org_event = '';
      }

      if (isset($nodedata[0]->translate)) {
        $translate = $nodedata[0]->translate;
      }
      else {
        $translate = '';
      }
      if (isset($nodedata[0]->field_alternate_title)) {
        $field_alternate_title = $nodedata[0]->field_alternate_title->und[0]->value;
      }
      else {
        $field_alternate_title = '';
      }

      if (isset($nodedata[0]->taxonomy_vocabulary_2)) {
        $vocab = $nodedata[0]->taxonomy_vocabulary_2->und;
        foreach ($vocab as $key => $tid) {
          $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($tid->tid);
          $title = $term->name->value;
        }
      }
      else {
        $taxonomy_vocabulary_2 = '';
      }

      if (isset($nodedata[0]->revision_timestamp)) {
        $revision_timestamp = $nodedata[0]->revision_timestamp;
      }
      else {
        $revision_timestamp = '';
      }
      if (isset($nodedata[0]->field_article_linked)) {
        $field_article_linked = $nodedata[0]->field_article_linked->und[0]->url;
      }
      else {
        $field_article_linked = '';
      }
      if (isset($nodedata[0]->revision_uid)) {
        $revision_uid = $nodedata[0]->revision_uid;
      }
      else {
        $revision_uid = '';
      }
      if (isset($nodedata[0]->field_org_phone)) {
        foreach ($nodedata[0]->field_org_phone as $field_org_phone) {
          $field_org_phone = $field_org_phone[0]->value;
        }
      }
      else {
        $field_org_phone = '';
      }
      if (isset($nodedata[0]->field_org_about)) {
        foreach ($nodedata[0]->field_org_about as $field_org_about) {
          $field_org_about = $field_org_about[0]->value;
          $field_org_about_format = $field_org_about[0]->format;

        }
      }
      else {
        $field_org_about = '';
      }
      if (isset($nodedata[0]->field_org_website)) {
        foreach ($nodedata[0]->field_org_website as $field_org_website) {
          $field_org_website = $field_org_website[0]->value;
        }
      }
      else {
        $field_org_website = '';
      }
      if (isset($nodedata[0]->field_org_copyright)) {
        foreach ($nodedata[0]->field_org_copyright as $field_org_copyright) {
          $field_org_copyright = $field_org_copyright[0]->value;
        }
      }
      else {
        $field_org_copyright = '';
      }
      if (isset($nodedata[0]->field_hide_as_contributor)) {
        foreach ($nodedata[0]->field_hide_as_contributor as $field_hide_as_contributor) {
          $field_hide_as_contributor = $field_hide_as_contributor[0]->value;
        }
      }
      else {
        $field_hide_as_contributor = '';
      }
      if (isset($nodedata[0]->field_org_blog)) {
        foreach ($nodedata[0]->field_org_blog as $field_org_blog) {
          $field_org_blog = $field_org_blog[0]->value;
        }
      }
      else {
        $field_org_blog = '';
      }
      if (isset($nodedata[0]->field_org_linkedin_profile)) {
        foreach ($nodedata[0]->field_org_linkedin_profile as $field_org_linkedin_profile) {
          $field_org_linkedin_profile_title = $field_org_linkedin_profile[0]->title;
          $field_org_linkedin_profile_url = $field_org_linkedin_profile[0]->url;
        }
      }
      else {
        $field_org_linkedin_profile = '';
      }
      if (isset($nodedata[0]->field_org_twitter_handle)) {
        foreach ($nodedata[0]->field_org_twitter_handle as $field_org_twitter_handle) {
          $field_org_twitter_handle_title = $field_org_twitter_handle[0]->title;
          $field_org_twitter_handle_url = $field_org_twitter_handle[0]->url;
        }
      }
      else {
        $field_org_twitter_handle = '';
      }
      if (isset($nodedata[0]->field_org_facebook)) {
        foreach ($nodedata[0]->field_org_facebook as $field_org_facebook) {
          $field_org_facebook_title = $field_org_facebook[0]->title;
          $field_org_facebook_url = $field_org_facebook[0]->url;
        }
      }
      else {
        $field_org_facebook = '';
      }
      if (isset($nodedata[0]->field_archive_link)) {
        foreach ($nodedata[0]->field_archive_link as $field_archive_link) {
          $field_archive_link = $field_archive_link[0]->url;
        }
      }
      else {
        $field_archive_link = '';
      }
      // $file_url = $service_d7_base_url . $imgSrc;
      // $private_file_folder_path = str_replace('/system/files/', '', $imgSrc);
      // $private_file_split = explode('/', $private_file_folder_path);
      // $private_real_folder_file_path = str_replace(
      // "%20", " ", "private://".$private_file_folder_path);
      // unset($private_file_split[count($private_file_split)-1]);
      // $private_real_folder_path =
      // "private://".implode('/', $private_file_split)."/";
      if (isset($nodedata[0]->field_org_logo)) {
        foreach ($nodedata[0]->field_org_logo as $field_org_logo) {
          $field_org_logo_alt = $field_org_logo[0]->alt;
          $field_org_logo_title = $field_org_logo[0]->title;
          $field_org_logo_fid = $field_org_logo[0]->fid;
          $field_org_logo_display = $field_org_logo[0]->display;
          $field_org_logo_width = $field_org_logo[0]->width;
          $field_org_logo_height = $field_org_logo[0]->height;
          $field_org_logo_desc = $field_org_logo[0]->description;
        }
      }
      else {
        $field_org_blog = '';
      }
      if (isset($nodedata[0]->field_author_email)) {
        foreach ($nodedata[0]->field_author_email as $field_author_email) {
          $field_author_email = $field_author_email[0]->value;
        }
      }
      else {
        $field_author_email = '';
      }
      if (isset($nodedata[0]->field_author_phone)) {
        foreach ($nodedata[0]->field_author_phone as $field_author_phone) {
          $field_author_phone = $field_author_phone[0]->value;
        }
      }
      else {
        $field_author_phone = '';
      }
      if (isset($nodedata[0]->field_author_website_link)) {
        foreach ($nodedata[0]->field_author_website as $field_author_website) {
          $field_author_website = $field_author_website[0]->value;
        }
      }
      else {
        $field_author_website = '';
      }
      if (isset($nodedata[0]->field_author_bio)) {
        foreach ($nodedata[0]->field_author_bio as $field_author_bio) {
          $field_author_bio = $field_author_bio[0]->value;
        }
      }
      else {
        $field_author_bio = '';
      }
      if (isset($nodedata[0]->field_author_display_rank)) {
        foreach ($nodedata[0]->field_author_display_rank as $field_author_display_rank) {
          $field_author_display_rank = $field_author_display_rank[0]->value;
        }
      }
      else {
        $field_author_display_rank = '';
      }
      if (isset($nodedata[0]->field_author_last_name)) {
        foreach ($nodedata[0]->field_author_last_name as $field_author_last_name) {
          $field_author_last_name = $field_author_last_name[0]->value;
        }
      }
      else {
        $field_author_last_name = '';
      }
      if (isset($nodedata[0]->field_author_title)) {
        foreach ($nodedata[0]->field_author_title as $field_author_title) {
          $field_author_title = $field_author_title[0]->value;
        }
      }
      else {
        $field_author_title = '';
      }
      if (isset($nodedata[0]->field_author_blog)) {
        foreach ($nodedata[0]->field_author_blog as $field_author_blog) {
          $field_author_blog = $field_author_blog[0]->value;
        }
      }
      else {
        $field_author_blog = '';
      }
      if (isset($nodedata[0]->field_author_firm_profile)) {
        foreach ($nodedata[0]->field_author_firm_profile as $field_author_firm_profile) {
          $field_author_firm_profile = $field_author_firm_profile[0]->value;
        }
      }
      else {
        $field_author_firm_profile = '';
      }
      if (isset($nodedata[0]->field_author_linkedin_profile)) {
        foreach ($nodedata[0]->field_author_linkedin_profile as $field_author_linkedin_profile) {
          $field_author_linkedin_profile = $field_author_linkedin_profile[0]->value;
        }
      }
      else {
        $field_author_linkedin_profile = '';
      }
      if (isset($nodedata[0]->field_author_twitter_handle)) {
        foreach ($nodedata[0]->field_author_twitter_handle as $field_author_twitter_handle) {
          $field_author_twitter_handle = $field_author_twitter_handle[0]->value;
        }
      }
      else {
        $field_author_twitter_handle = '';
      }
      if (isset($nodedata[0]->field_author_googleplus)) {
        foreach ($nodedata[0]->field_author_googleplus as $field_author_googleplus) {
          $field_author_googleplus = $field_author_googleplus[0]->value;
        }
      }
      else {
        $field_author_googleplus = '';
      }
      if (isset($nodedata[0]->field_author_org)) {
        foreach ($nodedata[0]->field_author_org as $field_author_org) {
          $field_author_org = $field_author_org[0]->value;
        }
      }
      else {
        $field_author_org = '';
      }
      if (isset($nodedata[0]->field_author_blog_link)) {
        foreach ($nodedata[0]->field_author_blog_link as $field_author_blog_link) {
          $field_author_blog_link = $field_author_blog_link[0]->value;
        }
      }
      else {
        $field_author_blog_link = '';
      }
      if (isset($nodedata[0]->path)) {
        $path_alias = $nodedata[0]->path->alias;
        $path_pid = $nodedata[0]->path->pid;
        $path_source = $nodedata[0]->path->source;
        $path_language = $nodedata[0]->path->language;
        $path_original_pid = $nodedata[0]->path->original->pid;
        $path_original_pid = $nodedata[0]->path->original->source;
        $path_original_alias = $nodedata[0]->path->original->alias;
      }
      else {
        $field_author_blog_link = '';
      }

    }
    // For ORG type.
    if (!empty($node) && $type == "org") {

     
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->field_org_about = strip_tags($field_org_about);
      $node->field_org_about->format = $field_org_about_format;
      $node->field_org_website = $field_org_website;
      $node->field_org_copyright = $field_org_copyright;
      if (!empty($field_org_linkedin_profile_url)) {
        $uri_lin = "http://$field_org_linkedin_profile_url";
      }
      else {
        $uri_lin = '';
      }
      $node->field_org_linkedin_profile = [
        "uri" => $uri_lin,
        "title" => "$field_org_linkedin_profile_title",
        "options" => ["target" => "_blank"],
      ];
      $node->field_org_phone = $field_org_phone;
    /*  $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
      ];
      $node->field_meta_tags = serialize($metatag); */
      if (!empty($field_org_twitter_handle_url)) {
        $uri = "http://$field_org_twitter_handle_url";
      }
      else {
        $uri = '';
      }
      $node->field_org_twitter_handle = [
        "uri" => $uri,
        "title" => $field_org_twitter_handle_title,
        "options" => ["target" => "_blank"],
      ];
      $node->field_org_facebook = [
        "uri" => $field_org_facebook_title,
        "title" => $field_org_facebook_url,
        "options" => ["target" => "_blank"],
      ];

    /*  $node->field_org_logo = [
        'target_id' => $logo_image_title_tid,
        'alt' => $logo_image_alt,
        'title' => $logo_image_title,
      ]; */

      $node->field_org_logo =  [
        'target_id' => !empty($media_org_image) ? "" : $media->id(),
        'alt' => $logo_image_alt,
        'title' => $logo_image_title,
      ];

      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);
      
      if (!empty($path_original_alias)) {
        $node->path = "/$path_original_alias";
      }
      else {
        $node->path = '';
      }
      $node->field_org_website = $field_org_website;

      $node->save();
    }
    // For ARTICLE node type.
    elseif (!empty($node) && $type == "article") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      if (!empty($revision_timestamp)) {
        $node->revision_timestamp = $revision_timestamp;
      }
      $node->revision_uid = $revision_uid;
      $node->field_event_hosting_group = $field_event_hosting_group;
      $node->field_org_org_info = [
        'target_id' => $field_org_org_info,
        'target_type' => 'node',
      ];
      $node->field_article_source = [
        'target_id' => $field_article_source,
      ];
      $node->field_grade = $field_grade;
      $node->field_accompanying_image = [
        //'target_id' => $field_article_image_tid,
        'target_id' => !empty($media_article_image) ? "" : $media_article_img->id(),
        'alt' => $field_article_image_alt,
        'title' => $field_article_image_title,
      ];

      $node->field_main_rotating_image = [
       // 'target_id' => $field_main_rotating_image_tid,
        'target_id' => $media->id(),
        'alt' => $field_main_rotating_image_name,
        'title' => $field_main_rotating_image_title,
      ];

      $node->field_homepage_image = [
        //'target_id' => $field_aux_article_image_tid,
        'target_id' => $media->id(),
        'alt' => $field_aux_article_image_alt,
        'title' => $field_aux_article_image_title,
      ];
      $node->field_author_org = [
        'target_id' => $field_author_org_event,
        'target_type' => 'node',
      ];
      $node->field_author_2 = [
        'target_id' => $field_author_2,
      ];

      $node->field_author = [
        'target_id' => $field_author,
      ];
      $node->field_sub_contrib = [
        'target_id' => $field_sub_contrib,
      ];
      $node->field_event_location = $field_event_location;
      $node->field_link_to_registration = $field_link_to_registration;
      foreach ($vocab as $key => $tid) {
        $id[] = $tid->tid;
      }
      $node->field_type_of_law = $id;
      $node->field_jurisdiction = $jurisdiction_id;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);

      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    // For EVENT node type $no->field_author.
    elseif (!empty($node) && $type == "event") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $node->field_event_hosting_group = $field_event_hosting_group;
      $time = strtotime($field_event_date);
      $field_event_date = \Drupal::service('date.formatter')->format($time, 'custom', 'Y-m-d\TH:i:s');
      $node->field_event_date = $field_event_date;
      $node->field_event_type = $field_event_type;
      $node->field_author_org = [
        'target_id' => $field_author_org_event,
        'target_type' => 'node',
      ];
      $node->field_author = [
        'target_id' => $field_author,
      ];
      $node->field_event_location = $field_event_location;
      $node->field_link_to_registration = $field_link_to_registration;
      foreach ($vocab as $key => $tid) {
        $id[] = $tid->tid;
      }
      $node->field_type_of_law = $id;
      $node->field_jurisdiction = $jurisdiction_id;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);

      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();

    }

    // For page node type.
    elseif (!empty($node) && $type == "page") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);



      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    elseif (!empty($node) && $type == "do_not_publish") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);



      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    elseif (!empty($node) && $type == "newsletter_archive") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $node->field_archive_link = $field_archive_link;
      $node->field_newsletter_date = $field_newsletter_date;

     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);


      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    elseif (!empty($node) && $type == "practice_groups") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $node->field_archive_link = $field_archive_link;
      $node->field_alternate_title = $field_alternate_title;
      foreach ($vocab as $key => $tid) {
        $id[] = $tid->tid;
      }
      $node->field_type_of_law = $id;

    /*  $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */


      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);


      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    elseif (!empty($node) && $type == "award") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->field_author_last_name = $field_author_last_name;
      $node->field_author_title = $field_author_title;
      $node->field_author_email = $field_author_email;
      $node->award_tol_url = $award_tol_url;
      $node->field_award_year = $field_award_year;
      $node->field_award_tile_label = $field_award_tile_label;
      $node->field_award_tagline = $field_award_tagline;
      $node->field_award_firm = $field_award_firm;
      $node->field_award_description = $field_award_description;
      $node->field_award_author = $field_award_author;
      $node->field_award_article = $field_award_article;
      $node->field_award_article_title = $field_award_article_title;
      $node->field_aoy_snippet = $field_aoy_snippet;
      $node->field_award_author8 = $field_award_author8;
      $node->field_award_author7 = $field_award_author7;
      $node->field_award_author6 = $field_award_author6;
      $node->field_award_author5 = $field_award_author5;
      $node->field_award_author4 = $field_award_author4;
      $node->field_award_author3 = $field_award_author3;
      $node->field_award_author2 = $field_award_author2;
      $node->field_type_of_law = $award_law;
      $node->field_award_tol_url = $field_award_tol_url;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);

      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";

      $node->save();
    }
    elseif (!empty($node) && $type == "author") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->field_author_last_name = $field_author_last_name;
      $node->field_author_title = $field_author_title;
      $node->field_author_email = $field_author_email;
      $node->field_author_firm_profile = $field_author_firm_profile;
      $node->field_author_linkedin_profile = $field_author_linkedin_profile;
      $node->field_author_twitter_handle = $field_author_twitter_handle;
      $node->field_author_website = $field_author_website;
      $node->field_author_phone = $field_author_phone;
      $node->field_author_blog = $field_author_blog;
      $node->field_author_subscriptions = $field_author_subscription;
      if (!empty($field_author_pic_tid)) {
        $node->field_author_pic = [
          //'target_id' => $field_author_pic_tid,
          'target_id' => $media->id(),
          'alt' => $field_author_pic_alt,
          'title' => $field_author_pic_name,
        ];
      }

      $node->field_author_bio = strip_tags($field_author_bio);
      $node->field_author_org->target_id = $user_id;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $node->field_author_org->tid = $field_author_org;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);

      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";

      $node->save();
    }
    elseif (!empty($node) && $type == "article_source") {
      $node = Node::load($nid);
      $node->title = $node_title;
      $node->type = $type;
      $node->body->value = $body_value;
      $node->body->format = 'full_html';
      $node->body->summary = strip_tags($body_summary);
      $node->format = $body_format;
      $node->log = $log;
      $node->status = $status;
      $node->comment = $comment;
      $node->promote = $promote;
      $node->sticky = $sticky;
      $node->language = $uid;
      $node->created = $created;
      $node->changed = $changed;
      $node->tnid = $tnid;
      $node->translate = $translate;
      $node->uid = $uid;
      $node->revision_timestamp = $revision_timestamp;
      $node->revision_uid = $revision_uid;
      $node->field_author_org->tid = $field_author_org;
      $node->field_author_org = [
        'target_id' => $field_author_org,
        'target_type' => 'node',
      ];
      $node->field_article_linked = $field_article_linked;
     /* $metatag = [
        'description' => $description,
        'keywords' => $keywords,
      ];
      $node->field_meta_tags = $metatag; */

      $metatag = [
        'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
        'description' => $description,
        'keywords' => $keywords,
        'abstract' => $abstract,
        'news_keywords' => $newskeywords,
        'revisit-after' => $revisit,
        'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
        'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
        'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
        'standout' => $standout,
        'rating' => $rating,
        'referrer' => $referrer,
        'rights' =>$rights,
        'generator' => $generator,
        'original-source' => $originalsource,
        'prev' => $prev,
        'next' => $next,
        'content-language' => $contentlanguage,
        'geo.position' => $geoposition,
        'geo.placename' => $geoplacename,
        'geo.region' => $georegion,
        'icbm' => $icbm,
        'refresh' => $refresh,
        'pragma' => $pragma,
        'cache-control' => $cachecontrol,
        'expires' => $expires,
        'google' => $google,
        'set_cookie' => $set_cookie,
      ];
      $node->field_meta_tags = serialize($metatag);


      $node->path = !empty($path_original_alias) ? "/$path_original_alias" : "";
      $node->save();
    }
    else {
      foreach ($vocab as $key => $tid) {
        $id[] = $tid->tid;
      }
      
    
      $node = \Drupal::entityTypeManager()->getStorage('node')->create([
        'type' => $type,
        'nid' => $nid,
        'title' => $node_title,
        'log' => $log,
        'body' => $body_value,
        'format' => 'full_html',
        'status' => $status,
        'field_org_about' => $field_author,
        'field_org_about' => strip_tags($field_org_about),
        'field_org_website' => $field_org_website,
        'field_org_copyright' => $field_org_copyright,
        'field_org_phone' => $field_org_phone,
        'field_org_website' => $field_org_website,
        'field_author_email' => $field_author_email,
        'field_author_title' => $field_author_title,
        'field_author_blog' => $field_author_blog,
        'field_author_linkedin_profile' => $field_author_linkedin_profile,
        'field_author_twitter_handle' => $field_author_twitter_handle,
        'field_author_bio' => strip_tags($field_author_bio),
        'field_author_subscriptions' => $field_author_subscription,
        'field_award_tol_url' => $fieldorg_demoaward_author,
        'field_award_article' => $field_award_article,
        'field_award_article_title' => $field_award_article_title,
        'field_aoy_snippet' => $field_aoy_snippet,
        'field_award_author8' => $field_award_author8,
        'field_award_author7' => $field_award_author7,
        'field_award_author6' => $field_award_author6,
        'field_award_author5' => $field_award_author5,
        'field_award_author4' => $field_award_author4,
        'field_award_author3' => $field_award_author3,
        'field_award_author2' => $field_award_author2,
        'field_type_of_law' => $award_law,
        'field_jurisdiction' => $jurisdiction_id,
        'field_event_hosting_group' => $field_event_hosting_group,
        'field_event_date' => $field_event_date,
        'field_event_type' => $field_event_type,
        'field_event_location' => $field_event_location,
        'field_newsletter_date' => $field_newsletter_date,
        'field_award_year' => $field_award_year,
        'field_award_tagline' => $field_award_tagline,
        'field_award_description' => $field_award_description,

        'field_author_org' => [
          'target_id' => $field_author_org,
          'target_type' => 'node',
        ],
        'field_award_tol_url' => $field_award_tol_url,
        'field_link_to_registration' => $field_link_to_registration,
        'field_article_source' => [
          'target_id' => $field_article_source,
        ],
        'field_org_logo' => [
         // 'target_id' => $logo_image_title_tid,
          'target_id' => empty($logo_image) ? "" : $media->id(),
         // 'target_id' => ($type == "org") ? !empty($media_org_image) ? "" : $media->id() : "",
          'alt' => $logo_image_alt,
          'title' => $logo_image_title,
        ],
        
        'field_accompanying_image' => [
          //'target_id' => $field_article_image_tid,
          'target_id' => ($type == "article") ? !empty($media_article_image) ? "" : $media_article_img->id() : "",
          'alt' => $field_article_image_alt,
          'title' => $field_article_image_title,
        ],
      
        
          'field_aux_article_image' => [
            //'target_id' => $field_aux_article_image_tid,
            'target_id' => !empty($field_aux_article_image)?$media->id():"",
            'alt' => $field_aux_article_image_alt,
            'title' => $field_aux_article_image_title,
          ],
        
          'field_author_pic' => [

            'target_id' => !empty($field_author_pic)?$media->id():"",
            'alt' => $field_author_pic_alt,
            'title' => $field_author_pic_title,

          ],
        'field_org_org_info' => [
          'target_id' => $field_org_org_info,
          'target_type' => 'node',
        ],
        'field_grade' => $field_grade,
        'field_org_facebook' => [
          'uri' => $field_org_facebook_url,
          'title' => $field_org_facebook_title,
        ],
        'field_type_of_law' => $id,
        // 'field_org_facebook'=>$field_org_facebook,
        'field_org_linkedin_profile' => [
          "uri" => 'http://' . $field_org_linkedin_profile_url . '',
          "title" => "$field_org_linkedin_profile_title",
        ],
        'field_author_org' => [
          'target_id' => $field_author_org_event,
        ],
        'field_author' => [
          'target_id' => $field_author,
          'target_type' => 'node',
        ],
        'field_author2' => [
          'target_id' => $field_author_2,
          'target_type' => 'node',
        ],
        'field_sub_contrib' => [
          'target_id' => $field_sub_contrib,
          'target_type' => 'node',
        ],
        'field_author_phone' => $field_author_phone,
        'field_author_website' => $field_author_website,
        'field_author_last_name' => $field_author_last_name,
        'field_author_firm_profile' => $field_author_firm_profile,
        'field_org_twitter_handle' => [
          "uri" => "http://'.$field_org_twitter_handle_url.'",
          "title" => $field_org_twitter_handle_title,
        ],
        'field_article_linked' => $field_article_linked,
        'field_archive_link' => $field_archive_link,
        'field_alternate_title' => $field_alternate_title,
        'comment' => $comment,
        'promote' => $promote,
        'sticky' => $sticky,
        'language' => $uid,
        'created' => $created,
        'changed' => $changed,
        'uid' => $uid,
        'translate' => $translate,
       /* $metatag = [
          'description' => $description,
          'keywords' => $keywords,
        ], */
        $metatag = [
          'title' => !empty($meta_title) ? $meta_title : '[node:title] | [site:name]',
          'description' => $description,
          'keywords' => $keywords,
          'abstract' => $abstract,
          'news_keywords' => $newskeywords,
          'revisit-after' => $revisit,
          'image_src' => !empty($image_src) ? $image_src : '[node:field_aux_article_image]',
          'canonical_url' => !empty($canonical_url) ? $canonical_url : '[current-page:url:absolute]',
          'shortlink' => !empty($shortlink) ? $shortlink : '[current-page:url:unaliased]',
          'standout' => $standout,
          'rating' => $rating,
          'referrer' => $referrer,
          'rights' =>$rights,
          'generator' => $generator,
          'original-source' => $originalsource,
          'prev' => $prev,
          'next' => $next,
          'content-language' => $contentlanguage,
          'geo.position' => $geoposition,
          'geo.placename' => $geoplacename,
          'geo.region' => $georegion,
          'icbm' => $icbm,
          'refresh' => $refresh,
          'pragma' => $pragma,
          'cache-control' => $cachecontrol,
          'expires' => $expires,
          'google' => $google,
          'set_cookie' => $set_cookie,
        ],
       'field_meta_tags' => serialize($metatag),
        //'field_meta_tags' => $metatag,
        'path' => "/$path_original_alias",
        'revision_timestamp' => $revision_timestamp,
        'revision_uid' => $revision_uid,
      ]);
      $node->save();
      $node = Node::load($nid);
      $node->set("path", ["pathauto" => TRUE]);
    }

    return new JsonResponse($result);
  }

  /**
   * User data delete Sync.
   */
  public function nodedelete() {
    $result = [];

    header('Content-type: application/json');
    $input = file_get_contents('php://input');
    $valuedata = json_decode($input, TRUE);
    $valuedata = json_decode($valuedata);
    $userdataname = [];
    $nid = $valuedata[0]->nid;
    if (!empty($nid) && (isset($nid))) {
      $node = Node::load($nid);
      if ($node) {
        $node->delete();
      }
    }
    $result[] = ' Data Deleted';

    $msg = $result;
    \Drupal::logger('SFDC')->notice($msg);

    return new JsonResponse($result);
  }

}