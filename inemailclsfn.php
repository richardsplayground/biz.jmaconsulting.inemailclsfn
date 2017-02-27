<?php
define('SUBADDRESS', '+supportcontact');

require_once 'inemailclsfn.civix.php';

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function inemailclsfn_civicrm_config(&$config) {
  _inemailclsfn_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function inemailclsfn_civicrm_xmlMenu(&$files) {
  _inemailclsfn_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function inemailclsfn_civicrm_install() {
  _inemailclsfn_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function inemailclsfn_civicrm_uninstall() {
  _inemailclsfn_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function inemailclsfn_civicrm_enable() {
  _inemailclsfn_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function inemailclsfn_civicrm_disable() {
  _inemailclsfn_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function inemailclsfn_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _inemailclsfn_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function inemailclsfn_civicrm_managed(&$entities) {
  _inemailclsfn_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function inemailclsfn_civicrm_caseTypes(&$caseTypes) {
  _inemailclsfn_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function inemailclsfn_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _inemailclsfn_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_emailProcessor
 */
function inemailclsfn_civicrm_emailProcessor($type, &$params, $mail, &$result, $action = null) {
  // Check if activity is being created.
  if ($type == "activity" && !empty($mail)) {
    // Fetch the Inbound Email activity type.
    $inbound = reset(CRM_Core_OptionGroup::values('activity_type', FALSE, FALSE, FALSE, " AND v.name = 'Inbound Email'", "value"));
    // Fetch the Support Contact activity type.
    $support = reset(CRM_Core_OptionGroup::values('activity_type', FALSE, FALSE, FALSE, " AND v.name = 'Support contact'", "value"));

    // Check subaddress in FROM address too.
    $isSubAddress = CRM_InEmail_BAO_InEmail::checkSupportContact($mail->from->email, $params, $support);

    // Check if activity created is Inbound Email and iterate through to, bcc, cc emails.
    if (!$isSubAddress) {
      if (!empty($mail->to) && ($inbound == $params['activity_type_id'])) {
        foreach ($mail->to as $to) {
          if (CRM_InEmail_BAO_InEmail::checkSupportContact($to->email, $params, $support)) {
            break;
          }
        }
      }
      if (!empty($mail->cc) && ($inbound == $params['activity_type_id'])) {
        foreach ($mail->cc as $cc) {
          if (CRM_InEmail_BAO_InEmail::checkSupportContact($cc->email, $params, $support)) {
            break;
          }
        }
      }
      if (!empty($mail->bcc) && ($inbound == $params['activity_type_id'])) {
        foreach ($mail->bcc as $bcc) {
          if (CRM_InEmail_BAO_InEmail::checkSupportContact($bcc->email, $params, $support)) {
            break;
          }
        }
      }
    }

    // Create the new support activity and delete the Inbound activity created.
    if ($support == $params['activity_type_id']) {
      try {
        civicrm_api3('Activity', 'create', $params);
      }
      catch (CiviCRM_API3_Exception $e) {
        // Handle error here.
        return array(
          'error' => $e->getMessage(),
          'error_code' => $e->getErrorCode(),
          'error_data' => $e->getExtraParams(),
       );
      }
      try {
        civicrm_api3('Activity', 'delete', array('id' => $result['id']));
      }
      catch (CiviCRM_API3_Exception $e) {
        // Handle error here.
        return array(
          'error' => $e->getMessage(),
          'error_code' => $e->getErrorCode(),
          'error_data' => $e->getExtraParams(),
       );
      }
    }
  }
}