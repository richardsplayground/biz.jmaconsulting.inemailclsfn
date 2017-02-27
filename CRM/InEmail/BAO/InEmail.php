<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright JMAConsulting LLC (c) 2004-2017
 * $Id$
 *
 */

/**
 * Class CRM_InEmail_BAO_InEmail
 *
 */
class CRM_InEmail_BAO_InEmail {

  /**
   * Function to check if email contains sub-address. Convert to Support Contact activity if yes.
   *
   * @param string $email
   *  The email address to check against.
   * @param array $params
   *  Array consisting of activity params.
   * @param int $supportContact
   *  Activity type ID of Support Contact.
   *
   * @return bool
   **/
  function checkSupportContact($email, &$params, $supportContact) {
    if (strpos($email, SUBADDRESS) !== FALSE) {
      $params['activity_type_id'] = $supportContact;
      return TRUE;
    }
    return FALSE;
  }
}