<?php

namespace Drupal\user_import_data\Controller;

/**
 * @file
 * Contains \Drupal\user_import_data\Controller\UserInsertion.
 */

use Drupal\user\Entity\User;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\profile\Entity\Profile;

/**
 * Initialize class.
 */
class UserInsertion extends ControllerBase {

  /**
   * For user edit and update.
   */
  public function userdata() {
    $result = [];
    header('Content-type: application/json');
    $input = file_get_contents('php://input');
    $valuedata = json_decode($input, TRUE);
    $valuedata = json_decode($valuedata);
    $userdataname = [];
    if (!empty($valuedata[0]->mail) && (isset($valuedata[0]->mail))) {
      $user = user_load_by_mail($valuedata[0]->mail);
      if (isset($valuedata[2]->profile_org_country)) {
        $profile_org_country = $valuedata[2]->profile_org_country;
      }
      else {
        $profile_org_country = '';
      }
      if (isset($valuedata[3])) {
        $password = $valuedata[3];
      }
      else {
        $password = '';
      }
      if (isset($valuedata[2]->profile_title)) {
        $profile_title = $valuedata[2]->profile_title;
      }
      else {
        $profile_title = '';
      }
      if (isset($valuedata[0]->uid)) {
        $uid = $valuedata[0]->uid;
      }
      else {
        $uid = '';
      }
      if (isset($valuedata[2]->name)) {
        $name = $valuedata[2]->name;
      }
      else {
        $name = $valuedata[0]->name;
      }
      if (isset($valuedata[2]->profile_org_st_prov)) {
        $profile_org_st_prov = $valuedata[0]->profile_org_st_prov;
      }
      else {
        $profile_org_st_prov = '';
      }
      if (isset($valuedata[0]->pass)) {
        $pass = $valuedata[0]->pass;
      }
      else {
        $pass = '';
      }
      if (isset($valuedata[0]->mail)) {
        $email = $valuedata[0]->mail;
      }
      else {
        $email = '';
      }
      if (isset($valuedata[0]->theme)) {
        $theme = $valuedata[0]->theme;
      }
      else {
        $theme = '';
      }
      if (isset($valuedata[0]->signature)) {
        $signature = $valuedata[0]->signature;
      }
      else {
        $signature = '';
      }
      if (isset($valuedata[0]->signature_format)) {
        $signature_format = $valuedata[0]->signature_format;
      }
      else {
        $signature_format = '';
      }
      if (isset($valuedata[0]->created)) {
        $created = $valuedata[0]->created;
      }
      else {
        $created = '';
      }
      if (isset($valuedata[0]->access)) {
        $access = $valuedata[0]->access;
      }
      else {
        $access = '';
      }
      if (isset($valuedata[0]->login)) {
        $login = $valuedata[0]->login;
      }
      else {
        $login = '';
      }
      if (isset($valuedata[2]->status)) {
        $status = $valuedata[2]->status;
      }
      else {
        $status = '';
      }
      if (isset($valuedata[0]->timezone)) {
        $timezone = $valuedata[0]->timezone;
      }
      else {
        $timezone = '';
      }
      if (isset($valuedata[0]->language)) {
        $language = $valuedata[0]->language;
      }
      else {
        $language = '';
      }
      if (isset($valuedata[0]->init)) {
        $init = $valuedata[0]->init;
      }
      else {
        $init = '';
      }
      if (isset($valuedata[2]->profile_org)) {
        $profile_org = $valuedata[0]->profile_org;
      }
      else {
        $profile_org = '';
      }
      if (isset($valuedata[2]->profile_org)) {
        $profile_org_name = $valuedata[2]->profile_org;
      }
      else {
        $profile_org_name = '';
      }
      if (isset($valuedata[2]->profile_org_city)) {
        $profile_org_city = $valuedata[2]->profile_org_city;
      }
      else {
        $profile_org_city = '';
      }
      if (isset($valuedata[2]->profile_org_phone)) {
        $profile_org_phone = $valuedata[2]->profile_org_phone;
      }
      else {
        $profile_org_phone = '';
      }
      if (isset($valuedata[2]->profile_org_fax)) {
        $profile_org_fax = $valuedata[2]->profile_org_fax;
      }
      else {
        $profile_org_fax = '';
      }
      if (isset($valuedata[2]->profile_org_phone2)) {
        $profile_org_phone2 = $valuedata[2]->profile_org_phone2;
      }
      else {
        $profile_org_phone2 = '';
      }
      if (isset($valuedata[0]->profile_org_zip)) {
        $profile_org_zip = $valuedata[0]->profile_org_zip;
      }
      else {
        $profile_org_zip = '';
      }
      if (isset($valuedata[0]->profile_job_title)) {
        $profile_job_title = $valuedata[0]->profile_job_title;
      }
      else {
        $profile_job_title = '';
      }
      if (isset($valuedata[0]->profile_other_prof)) {
        $profile_other_prof = $valuedata[0]->profile_other_prof;
      }
      else {
        $profile_other_prof = '';
      }
      if (isset($valuedata[0]->profile_org_address)) {
        $profile_org_address = $valuedata[0]->profile_org_address;
      }
      else {
        $profile_org_address = '';
      }
      if (isset($valuedata[2]->profile_first_name)) {
        $profile_first_name = $valuedata[2]->profile_first_name;
      }
      else {
        $profile_first_name = '';
      }
      if (isset($valuedata[2]->profile_org_zip)) {
        $profile_org_zip = $valuedata[2]->profile_org_zip;
      }
      else {
        $profile_org_zip = '';
      }
      if (isset($valuedata[2]->profile_last_name)) {
        $profile_last_name = $valuedata[2]->profile_last_name;
      }
      else {
        $profile_last_name = '';
      }
      if (isset($valuedata[2]->profile_mi_name)) {
        $profile_mi_name = $valuedata[2]->profile_mi_name;
      }
      else {
        $profile_mi_name = '';
      }
      if (isset($valuedata[2]->profile_title)) {
        $profile_title = $valuedata[2]->profile_title;
      }
      else {
        $profile_title = '';
      }
      if (isset($valuedata[2]->profile_org_st_prov)) {
        $profile_org_st_prov = $valuedata[2]->profile_org_st_prov;
      }
      else {
        $profile_org_st_prov = '';
      }
      if (isset($valuedata[0]->profile_address_2)) {
        $profile_address_2 = $valuedata[2]->profile_address_2;
      }
      else {
        $profile_address_2 = '';
      }
      if (isset($valuedata[2]->profile_account_status)) {
        $profile_account_status = $valuedata[2]->profile_account_status;
      }
      else {
        $profile_account_status = '';
      }
      if (isset($valuedata[2]->profile_2016_rate)) {
        $profile_2016_rate = $valuedata[2]->profile_2016_rate;
      }
      else {
        $profile_2016_rate = '';
      }
      if (isset($valuedata[2]->profile_2016_account_notes)) {
        $profile_2016_account_notes = $valuedata[2]->profile_2016_account_notes;
      }
      else {
        $profile_2016_account_notes = '';
      }
      if (isset($valuedata[2]->profile_2015_rate)) {
        $profile_2015_rate = $valuedata[2]->profile_2015_rate;
      }
      else {
        $profile_2015_rate = '';
      }
      if (isset($valuedata[2]->profile_2016_date_invoice_sent)) {
        $profile_2016_date_invoice_sent = $valuedata[2]->profile_2016_date_invoice_sent;
      }
      else {
        $profile_2016_date_invoice_sent = '';
      }
      if (isset($valuedata[2]->profile_2016_date_payment_received)) {
        $profile_2016_date_payment_received = $valuedata[2]->profile_2016_date_payment_received;
      }
      else {
        $profile_2016_date_payment_received = '';
      }
      if (isset($valuedata[2]->profile_2017_date_invoice_sent)) {
        $profile_2017_date_invoice_sent = $valuedata[2]->profile_2017_date_invoice_sent;
      }
      else {
        $profile_2017_date_invoice_sent = '';
      }
      if (isset($valuedata[2]->profile_2017_rate)) {
        $profile_2017_rate = $valuedata[2]->profile_2017_rate;
      }
      else {
        $profile_2017_rate = '';
      }
      if (isset($valuedata[2]->profile_2017_account_notes)) {
        $profile_2017_account_notes = $valuedata[2]->profile_2017_account_notes;
      }
      else {
        $profile_2017_account_notes = '';
      }
      if (isset($valuedata[2]->profile_2017_date_payment_received)) {
        $profile_2017_date_payment_received = $valuedata[2]->profile_2017_date_payment_received;
      }
      else {
        $profile_2017_date_payment_received = '';
      }
      if (isset($valuedata[2]->profile_contract_date)) {
        $profile_contract_date = $valuedata[2]->profile_contract_date;
      }
      else {
        $profile_contract_date = '';
      }
      if (isset($valuedata[2]->profile_2018_rate)) {
        $profile_2018_rate = $valuedata[2]->profile_2018_rate;
      }
      else {
        $profile_2018_rate = '';
      }
      if (isset($valuedata[2]->profile_2018_date_invoice_sent)) {
        $profile_2018_date_invoice_sent = $valuedata[2]->profile_2018_date_invoice_sent;
      }
      else {
        $profile_2018_date_invoice_sent = '';
      }
      if (isset($valuedata[2]->profile_2018_date_payment_received)) {
        $profile_2018_date_payment_received = $valuedata[2]->profile_2018_date_payment_received;
      }
      else {
        $profile_2018_date_payment_received = '';
      }
      if (isset($valuedata[2]->profile_2018_account_notes)) {
        $profile_2018_account_notes = $valuedata[2]->profile_2018_account_notes;
      }
      else {
        $profile_2018_account_notes = '';
      }
      if (isset($valuedata[2]->profile_2019_rate)) {
        $profile_2019_rate = $valuedata[2]->profile_2019_rate;
      }
      else {
        $profile_2019_rate = '';
      }
      if (isset($valuedata[2]->profile_2019_date_invoice_sent)) {
        $profile_2019_date_invoice_sent = $valuedata[2]->profile_2019_date_invoice_sent;
      }
      else {
        $profile_2019_date_invoice_sent = '';
      }
      if (isset($valuedata[2]->profile_2019_date_payment_received)) {
        $profile_2019_date_payment_received = $valuedata[2]->profile_2019_date_payment_received;
      }
      else {
        $profile_2019_date_payment_received = '';
      }
      if (isset($valuedata[2]->profile_2019_account_notes)) {
        $profile_2019_account_notes = $valuedata[2]->profile_2019_account_notes;
      }
      else {
        $profile_2019_account_notes = '';
      }
      if (isset($valuedata[2]->profile_2020_rate)) {
        $profile_2020_rate = $valuedata[2]->profile_2020_rate;
      }
      else {
        $profile_2020_rate = '';
      }
      if (isset($valuedata[2]->profile_2020_date_invoice_sent)) {
        $profile_2020_date_invoice_sent = $valuedata[2]->profile_2020_date_invoice_sent;
      }
      else {
        $profile_2020_date_invoice_sent = '';
      }
      if (isset($valuedata[2]->profile_2020_date_payment_received)) {
        $profile_2020_date_payment_received = $valuedata[2]->profile_2020_date_payment_received;
      }
      else {
        $profile_2020_date_payment_received = '';
      }
      if (isset($valuedata[2]->profile_2020_account_notes)) {
        $profile_2020_account_notes = $valuedata[2]->profile_2020_account_notes;
      }
      else {
        $profile_2020_account_notes = '';
      }
      /*
       * for profile_analytics
       */
      if (isset($valuedata[2]->profile_analytics_siteID)) {
        $profile_analytics_siteID = $valuedata[2]->profile_analytics_siteID;
      }
      else {
        $profile_analytics_siteID = '';
      }
      if (isset($valuedata[2]->profile_analytics_md5_pw)) {
        $profile_analytics_md5_pw = $valuedata[2]->profile_analytics_md5_pw;
      }
      else {
        $profile_analytics_md5_pw = '';
      }
      if (isset($valuedata[2]->profile_analytics_login)) {
        $profile_analytics_login = $valuedata[2]->profile_analytics_login;
      }
      else {
        $profile_analytics_login = '';
      }
      /*
       * for client_specific_details
       */
      if (isset($valuedata[2]->profile_publication_specialist)) {
        $profile_publication_specialist = $valuedata[2]->profile_publication_specialist;
      }
      else {
        $profile_publication_specialist = '';
      }
      if (isset($valuedata[2]->profile_account_checker)) {
        $profile_account_checker = $valuedata[2]->profile_account_checker;
      }
      else {
        $profile_account_checker = '';
      }
      if (isset($valuedata[2]->profile_last_author_update)) {
        $profile_last_author_update = $valuedata[2]->profile_last_author_update;
      }
      else {
        $profile_last_author_update = '';
      }
      if (isset($valuedata[2]->profile_account_instructions)) {
        $profile_account_instructions = $valuedata[2]->profile_account_instructions;
      }
      else {
        $profile_account_instructions = '';
      }
      /*
       * for main_contact
       */
      if (isset($valuedata[2]->profile_org_phone2)) {
        $profile_org_phone2 = $valuedata[2]->profile_org_phone2;
      }
      else {
        $profile_org_phone2 = '';
      }
      if (isset($valuedata[2]->profile_job_title)) {
        $profile_job_title = $valuedata[2]->profile_job_title;
      }
      else {
        $profile_job_title = '';
      }
      if (isset($valuedata[2]->profile_other_prof)) {
        $profile_other_prof = $valuedata[2]->profile_other_prof;
      }
      else {
        $profile_other_prof = '';
      }
      if (isset($valuedata[2]->profile_first_name)) {
        $profile_first_name = $valuedata[2]->profile_first_name;
      }
      else {
        $profile_first_name = '';
      }
      if (isset($valuedata[2]->profile_last_name)) {
        $profile_last_name = $valuedata[2]->profile_last_name;
      }
      else {
        $profile_last_name = '';
      }
      if (isset($valuedata[2]->profile_mi_name)) {
        $profile_mi_name = $valuedata[2]->profile_mi_name;
      }
      else {
        $profile_mi_name = '';
      }
      if (isset($valuedata[2]->profile_title)) {
        $profile_title = $valuedata[2]->profile_title;
      }
      else {
        $profile_title = '';
      }

      /*
       * for membership_options
       */
      if (isset($valuedata[2]->profile_account_request)) {
        $profile_account_request = $valuedata[2]->profile_account_request;
      }
      else {
        $profile_account_request = '';
      }
      if (isset($valuedata[2]->profile_account_type)) {
        $profile_account_type = $valuedata[2]->profile_account_type;
      }
      else {
        $profile_account_type = '';
      }
      if (isset($valuedata[2]->profile_package_request)) {
        $profile_package_request = $valuedata[2]->profile_package_request;
      }
      else {
        $profile_package_request = '';
      }
      if (isset($valuedata[2]->profile_package_type)) {
        $profile_package_type = $valuedata[2]->profile_package_type;
      }
      else {
        $profile_package_type = '';
      }

      /*
       * for normal_day_to_day_contact
       */
      if (isset($valuedata[2]->profile_D2D_title)) {
        $profile_D2D_title = $valuedata[2]->profile_D2D_title;
      }
      else {
        $profile_D2D_title = '';
      }
      if (isset($valuedata[2]->profile_D2D_first_name)) {
        $profile_D2D_first_name = $valuedata[2]->profile_D2D_first_name;
      }
      else {
        $profile_D2D_first_name = '';
      }
      if (isset($valuedata[2]->profile_D2D_last_name)) {
        $profile_D2D_last_name = $valuedata[2]->profile_D2D_last_name;
      }
      else {
        $profile_D2D_last_name = '';
      }
      if (isset($valuedata[2]->profile_D2D_mi_name)) {
        $profile_D2D_mi_name = $valuedata[2]->profile_D2D_mi_name;
      }
      else {
        $profile_D2D_mi_name = '';
      }
      if (isset($valuedata[2]->profile_D2D_email)) {
        $profile_D2D_email = $valuedata[2]->profile_D2D_email;
      }
      else {
        $profile_D2D_email = '';
      }
      if (isset($valuedata[2]->profile_org_phone3)) {
        $profile_org_phone3 = $valuedata[2]->profile_org_phone3;
      }
      else {
        $profile_org_phone3 = '';
      }
      if (isset($valuedata[2]->profile_D2D_additional_name1)) {
        $profile_D2D_additional_name1 = $valuedata[2]->profile_D2D_additional_name1;
      }
      else {
        $profile_D2D_additional_name1 = '';
      }
      if (isset($valuedata[2]->profile_D2D_additional_email1)) {
        $profile_D2D_additional_email1 = $valuedata[2]->profile_D2D_additional_email1;
      }
      else {
        $profile_D2D_additional_email1 = '';
      }
      if (isset($valuedata[2]->profile_D2D_additional_name2)) {
        $profile_D2D_additional_name2 = $valuedata[2]->profile_D2D_additional_name2;
      }
      else {
        $profile_D2D_additional_name2 = '';
      }
      if (isset($valuedata[2]->profile_D2D_additional_email2)) {
        $profile_D2D_additional_email2 = $valuedata[2]->profile_D2D_additional_email2;
      }
      else {
        $profile_D2D_additional_email2 = '';
      }
      /*
       * for user_override
       */
      if (isset($valuedata[2]->profile_override)) {
        $profile_override = $valuedata[2]->profile_override;
      }
      else {
        $profile_override = '';
      }
      if (!empty($user)) {
        $us = $user;
        $user->setEmail($email);
        $user->setUsername($name);
        $ro = $user->getRoles();
        if (!empty($valuedata[1])) {
          foreach ($ro as $rol) {
            $user->removeRole($rol);
            $user->removeRole($ro);
          }
        }

        $user->set('init', $init);
        $user->set('created', $created);
        $user->set('access', $access);
        if($password != ""){
        $user->setPassword($password);
        }
        $user->set('status', $status);
        foreach ($valuedata[1] as $role) {
          if ($role != NULL) {
            $role = strtolower($role);
            $user->addRole($role);
          }
        }

        // $user->set("preferred_langcode", $language);
        // $user->set("preferred_admin_langcode", $language);
        $user->save();
        $result[] = $email . ' Data Updated';
        $uid_p = $user->get('uid')->value;
        $profile_data = \Drupal::entityTypeManager()
          ->getStorage('profile')
          ->loadByProperties([
            'uid' => $user->id(),
          ]);
        foreach ($profile_data as $profile_field) {
          $prof_type = $profile_field->get('type')->getString();
          if (!empty($profile_field) && ($prof_type == "contact_information")) {
            if (!empty($profile_org_address)) {
              $profile_field->set('field_address', $profile_org_address);
            }
            if (!empty($profile_address_2)) {
              $profile_field->set('field_address_2', $profile_address_2);
            }
            if (!empty($profile_org_city)) {
              $profile_field->set('field_city', $profile_org_city);
            }
            if (!empty($profile_org_st_prov)) {
              $profile_field->set('field_state_province', $profile_org_st_prov);
            }
            if (!empty($profile_org_country)) {
              $profile_field->set('field_country', $profile_org_country);
            }
            if (!empty($profile_org_fax)) {
              $profile_field->set('field_fax', $profile_org_fax);
            }
            if (!empty($profile_org_phone)) {
              $profile_field->set('field_main_organization_phone', $profile_org_phone);
            }
            if (!empty($profile_org_name)) {
              $profile_field->set('field_organization_name', $profile_org_name);
            }
            if (!empty($profile_org_zip)) {
              $profile_field->set('field_postal_code', $profile_org_zip);
            }
            if (!empty($profile_org_st_prov)) {
              $profile_field->set('field_state_province', $profile_org_st_prov);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "accounting_info")) {
            if (!empty($profile_contract_date)) {
              $profile_field->set('field_contract_date', $profile_contract_date);
            }
            if (!empty($profile_2015_rate)) {
              $profile_field->set('field_2015_rate', $profile_2015_rate);
            }
            if (!empty($profile_2016_account_notes)) {
              $profile_field->set('field_2016_account_notes', $profile_2016_account_notes);
            }
            if (!empty($profile_2016_date_invoice_sent)) {
              $profile_field->set('field_2016_date_invoice_sent', $profile_2016_date_invoice_sent);
            }
            if (!empty($profile_2016_date_payment_received)) {
              $profile_field->set('field_2016_date_payment_received', $profile_2016_date_payment_received);
            }
            if (!empty($profile_2016_rate)) {
              $profile_field->set('field_2016_rate', $profile_2016_rate);
            }
            if (!empty($profile_2017_account_notes)) {
              $profile_field->set('field_2017_account_notes', $profile_2017_account_notes);
            }
            if (!empty($profile_2017_date_invoice_sent)) {
              $profile_field->set('field_2017_date_invoice_sent', $profile_2017_date_invoice_sent);
            }
            if (!empty($profile_2017_date_payment_received)) {
              $profile_field->set('field_2017_date_payment_received', $profile_2017_date_payment_received);
            }
            if (!empty($profile_2017_rate)) {
              $profile_field->set('field_2017_rate', $profile_2017_rate);
            }
            if (!empty($profile_2018_account_notes)) {
              $profile_field->set('field_2018_account_notes', $profile_2018_account_notes);
            }
            if (!empty($profile_2018_date_invoice_sent)) {
              $profile_field->set('field_2018_date_invoice_sent', $profile_2018_date_invoice_sent);
            }
            if (!empty($profile_2018_date_payment_received)) {
              $profile_field->set('field_2018_date_payment_received', $profile_2018_date_payment_received);
            }
            if (!empty($profile_2018_rate)) {
              $profile_field->set('field_2018_rate', $profile_2018_rate);
            }
            if (!empty($profile_2019_account_notes)) {
              $profile_field->set('field_2019_account_notes', $profile_2019_account_notes);
            }
            if (!empty($profile_2019_date_invoice_sent)) {
              $profile_field->set('field_2019_date_invoice_sent', $profile_2019_date_invoice_sent);
            }
            if (!empty($profile_2019_date_payment_received)) {
              $profile_field->set('field_2019_date_payment_received', $profile_2019_date_payment_received);
            }
            if (!empty($profile_2019_rate)) {
              $profile_field->set('field_2019_rate', $profile_2019_rate);
            }
            if (!empty($profile_2020_account_notes)) {
              $profile_field->set('field_2020_account_notes', $profile_2020_account_notes);
            }
            if (!empty($profile_2020_date_invoice_sent)) {
              $profile_field->set('field_2020_date_invoice_sent', $profile_2020_date_invoice_sent);
            }
            if (!empty($profile_2020_date_payment_received)) {
              $profile_field->set('field_2020_date_payment_received', $profile_2020_date_payment_received);
            }
            if (!empty($profile_2020_rate)) {
              $profile_field->set('field_2020_rate', $profile_2020_rate);
            }
            if (!empty($profile_account_status)) {
              $profile_field->set('field_account_status', $profile_account_status);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "analytics")) {
            if (!empty($profile_analytics_siteID)) {
              $profile_field->set('field_analytics_site_id', $profile_analytics_siteID);
            }
            if (!empty($profile_analytics_login)) {
              $profile_field->set('field_analytics_login', $profile_analytics_login);
            }
            if (!empty($profile_analytics_md5_pw)) {
              $profile_field->set('field_analytics_password', $profile_analytics_md5_pw);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "client_specific_details")) {
            if (!empty($profile_account_checker)) {
              $profile_field->set('field_account_checker', $profile_account_checker);
            }
            if (!empty($profile_account_instructions)) {
              $profile_field->set('field_account_instructions', $profile_account_instructions);
            }
            if (!empty($profile_publication_specialist)) {
              $profile_field->set('field_account_manager', $profile_publication_specialist);
            }
            if (!empty($profile_last_author_update)) {
              $profile_field->set('field_date_of_last_author_update', $profile_last_author_update);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "main_contact")) {
            if (!empty($profile_first_name)) {
              $profile_field->set('field_main_business_contact_s_fi', $profile_first_name);
            }
            if (!empty($profile_org_phone2)) {
              $profile_field->set('field_main_business_contact_s_ph', $profile_org_phone2);
            }
            if (!empty($profile_job_title)) {
              $profile_field->set('field_main_business_contact_s_jo', $profile_job_title);
            }
            if (!empty($profile_last_name)) {
              $profile_field->set('field_main_business_contact_s_la', $profile_last_name);
            }
            if (!empty($profile_mi_name)) {
              $profile_field->set('field_main_business_contact_s_mi', $profile_mi_name);
            }
            if (!empty($profile_job_title)) {
              $profile_field->set('field_main_business_contact_s_ti', $profile_title);
            }
            if (!empty($profile_publication_specialist)) {
              $profile_field->set('field_main_organization_phone', $profile_publication_specialist);
            }
            if (!empty($profile_other_prof)) {
              $profile_field->set('field_other', $profile_other_prof);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "membership_options")) {
            if (!empty($profile_account_type)) {
              $profile_field->set('field_account_type', $profile_account_type);
            }
            if (!empty($profile_account_request)) {
              $profile_field->set('field_account_type_being_request', $profile_account_request);
            }
            if (!empty($profile_package_request)) {
              $profile_field->set('field_package_being_requested', $profile_package_request);
            }
            if (!empty($profile_package_type)) {
              $profile_field->set('field_package_type', $profile_package_type);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "normal_day_to_day_contact")) {
            if (!empty($profile_D2D_additional_email1)) {
              $profile_field->set('field_additional_day_to_email1', $profile_D2D_additional_email1);
            }
            if (!empty($profile_D2D_additional_email2)) {
              $profile_field->set('field_additional_day_to_email2', $profile_D2D_additional_email2);
            }
            if (!empty($profile_D2D_additional_name1)) {
              $profile_field->set('field_additional_day_to_day_cont', $profile_D2D_additional_name1);
            }
            if (!empty($profile_D2D_additional_name2)) {
              $profile_field->set('field_additional_day_to_co_name2', $profile_D2D_additional_name2);
            }
            if (!empty($profile_D2D_email)) {
              $profile_field->set('field_day_to_day_contact_s_email', $profile_D2D_email);
            }
            if (!empty($profile_D2D_first_name)) {
              $profile_field->set('field_day_to_day_contact_s_first', $profile_D2D_first_name);
            }
            if (!empty($profile_D2D_last_name)) {
              $profile_field->set('field_day_to_day_contact_s_last_', $profile_D2D_last_name);
            }
            if (!empty($profile_D2D_mi_name)) {
              $profile_field->set('field_day_to_day_contact_s_middl', $profile_D2D_mi_name);
            }
            if (!empty($profile_org_phone3)) {
              $profile_field->set('field_day_to_day_contact_s_phone', $profile_org_phone3);
            }
            if (!empty($profile_D2D_title)) {
              $profile_field->set('field_day_to_day_contact_s_title', $profile_D2D_title);
            }
          }
          elseif (!empty($profile_field) && ($prof_type == "user_override")) {
            if (!empty($profile_override)) {
              $profile_field->set('field_override', $profile_override);
            }
          }
          $profile_field->save();
        }
      }
      else {
        $drupaluser = User::create();
        if (array_key_exists($email, $userdataname)) {
          $drupaluser->setUsername($lastname);
        }
        else {
          $drupaluser->setUsername($name);
        }
        foreach ($valuedata[1] as $role) {
          if ($role == "anonymous user") {
            $role = NULL;
          }
          elseif ($role == "authenticated") {
            $role = NULL;
          }
          else {
            $role = $role;
          }
          $drupaluser->addRole(strtolower($role));
        }

        $drupaluser->setEmail($email);
        $drupaluser->enforceIsNew();
        $drupaluser->set('init', $init);
        $drupaluser->set('created', $created);
        $drupaluser->set('access', $access);
        // $drupaluser->set("langcode", $language);
        if($password != ""){
        $drupaluser->setPassword($password);
        }
        // $drupaluser->set("preferred_langcode", $language);
        // $drupaluser->set("preferred_admin_langcode", $language);
        $drupaluser->set('status', $status);
        $drupaluser->save();
        if ($status == '1') {
          _user_mail_notify('register_admin_created', $drupaluser);
          $drupaluser->save();
        }
        $getid = $drupaluser->id();

        $profile1 = Profile::create([
          'type' => 'contact_information',
          'uid' => $drupaluser->id(),
          'field_address' => $profile_org_address,
          'field_address_2' => $profile_address_2,
          'field_city' => $profile_org_city,
          'field_country' => $profile_org_country,
          'field_fax' => $profile_org_fax,
          'field_main_organization_phone' => $profile_org_phone,
          'field_organization_name' => $profile_org_name,
          'field_postal_code' => $profile_org_zip,
          'field_state_province' => $profile_org_st_prov,
        ]);
        $profile1->setDefault(TRUE);
        $profile1->save();

        $profile2 = Profile::create([
          'type' => 'accounting_info',
          'uid' => $drupaluser->id(),
          'field_contract_date' => $profile_contract_date,
          'field_2015_rate' => $profile_2015_rate,
          'field_2016_account_notes' => $profile_2016_account_notes,
          'field_2016_date_invoice_sent' => $profile_2016_date_invoice_sent,
          'field_2016_date_payment_received' => $profile_2016_date_payment_received,
          'field_2016_rate' => $profile_2016_rate,
          'field_2017_account_notes' => $profile_2017_account_notes,
          'field_2017_date_invoice_sent' => $profile_2017_date_invoice_sent,
          'field_2017_date_payment_received' => $profile_2017_date_payment_received,
          'field_2017_rate' => $profile_2017_rate,
          'field_2018_account_notes' => $profile_2018_account_notes,
          'field_2018_date_invoice_sent' => $profile_2018_date_invoice_sent,
          'field_2018_date_payment_received' => $profile_2018_date_payment_received,
          'field_2018_rate' => $profile_2018_rate,
          'field_2019_account_notes' => $profile_2019_account_notes,
          'field_2019_date_invoice_sent' => $profile_2019_date_invoice_sent,
          'field_2019_date_payment_received' => $profile_2019_date_payment_received,
          'field_2019_rate' => $profile_2019_rate,
          'field_2020_account_notes' => $profile_2020_account_notes,
          'field_2020_date_invoice_sent' => $profile_2020_date_invoice_sent,
          'field_2020_date_payment_received' => $profile_2020_date_payment_received,
          'field_2020_rate' => $profile_2020_rate,
          'field_account_status' => $profile_account_status,
        ]);
        $profile2->setDefault(TRUE);
        $profile2->save();

        $profile3 = Profile::create([
          'type' => 'analytics',
          'uid' => $drupaluser->id(),
          'field_analytics_login' => $profile_analytics_login,
          'field_analytics_password' => $profile_analytics_md5_pw,
          'field_analytics_site_id' => $profile_analytics_siteID,
        ]);
        $profile3->setDefault(TRUE);
        $profile3->save();

        $profile4 = Profile::create([
          'type' => 'client_specific_details',
          'uid' => $drupaluser->id(),
          'field_account_checker' => $profile_account_checker,
          'field_account_instructions' => $profile_account_instructions,
          'field_account_manager' => $profile_publication_specialist,
          'field_date_of_last_author_update' => $profile_last_author_update,
        ]);
        $profile4->setDefault(TRUE);
        $profile4->save();

        $profile5 = Profile::create([
          'type' => 'main_contact',
          'uid' => $drupaluser->id(),
          'field_main_business_contact_s_fi' => $profile_first_name,
          'field_main_business_contact_s_jo' => $profile_job_title,
          'field_main_business_contact_s_la' => $profile_last_name,
          'field_main_business_contact_s_mi' => $profile_mi_name,
          'field_main_business_contact_s_ph' => $profile_org_phone2,
          'field_main_business_contact_s_ti' => $profile_title,
          'field_main_organization_phone' => $profile_publication_specialist,
          'field_other' => $profile_other_prof,
        ]);
        $profile5->setDefault(TRUE);
        $profile5->save();

        $profile6 = Profile::create([
          'type' => 'membership_options',
          'uid' => $drupaluser->id(),
          'field_account_type' => $profile_account_type,
          'field_account_type_being_request' => $profile_account_request,
          'field_package_being_requested' => $profile_package_request,
          'field_package_type' => $profile_package_type,
        ]);
        $profile6->setDefault(TRUE);
        $profile6->save();

        $profile7 = Profile::create([
          'type' => 'normal_day_to_day_contact',
          'uid' => $drupaluser->id(),
          'field_additional_day_to_email1' => $profile_D2D_additional_email1,
          'field_additional_day_to_email2' => $profile_D2D_additional_email2,
          'field_additional_day_to_day_cont' => $profile_D2D_additional_name1,
          'field_additional_day_to_co_name2' => $profile_D2D_additional_name2,
          'field_day_to_day_contact_s_email' => $profile_D2D_email,
          'field_day_to_day_contact_s_first' => $profile_D2D_first_name,
          'field_day_to_day_contact_s_last_' => $profile_D2D_last_name,
          'field_day_to_day_contact_s_middl' => $profile_D2D_mi_name,
          'field_day_to_day_contact_s_phone' => $profile_org_phone3,
          'field_day_to_day_contact_s_title' => $profile_D2D_title,
        ]);
        $profile7->setDefault(TRUE);
        $profile7->save();

        $profile8 = Profile::create([
          'type' => 'user_override',
          'uid' => $drupaluser->id(),
          'field_override' => $profile_override,
        ]);
        $profile8->setDefault(TRUE);
        $profile8->save();
        $result[] = $email . ' Data Inserted';
      }
    }

    // $msg = $result;
    // \Drupal::logger('SFDC')->notice($msg);
    return new JsonResponse($result);
  }

  /**
   * User data delete Sync.
   */
  public function userdatadelete() {
    $result = [];

    header('Content-type: application/json');
    $input = file_get_contents('php://input');
    $valuedata = json_decode($input, TRUE);
    $valuedata = json_decode($valuedata);
    $userdataname = [];
    if (!empty($valuedata->mail) && (isset($valuedata->mail))) {
      $user = user_load_by_mail($valuedata->mail);
      $userid = $user->id();
      $user->delete();
      $result[] = $email . ' Data Deleted';
    }

    // $msg = $result;
    // \Drupal::logger('SFDC')->notice($msg);
    return new JsonResponse($result);
  }

}
