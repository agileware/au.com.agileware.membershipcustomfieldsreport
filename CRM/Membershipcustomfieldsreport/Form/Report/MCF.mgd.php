<?php
// This file declares a managed database record of type "ReportTemplate".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array(
  0 =>
  array(
    'name' => 'CRM_Membershipcustomfieldsreport_Form_Report_MCF',
    'entity' => 'ReportTemplate',
    'params' =>
    array(
      'version' => 3,
      'label' => 'Memberships with Contact Custom Fields Report',
      'description' => 'Extended membership report with support of filtering/showing custom fields of Individual contact.',
      'class_name' => 'CRM_Membershipcustomfieldsreport_Form_Report_MCF',
      'report_url' => 'au.com.agileware.membershipcustomfieldsreport/MCF',
      'component' => 'CiviMember',
    ),
  ),
);
