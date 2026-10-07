<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
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
 * @copyright CiviCRM LLC (c) 2004-2015
 * $Id$
 *
 */
class CRM_Membershipcustomfieldsreport_Form_Report_MCF extends CRM_Report_Form {

  protected $_addressField = FALSE;

  protected $_emailField = FALSE;

  protected $_phoneField = FALSE;

  protected $_contribField = FALSE;

  protected $_summary = NULL;

  /**
   * Active campaigns, keyed by ID, when CiviCampaign is enabled.
   *
   * @var array
   */
  protected $activeCampaigns = [];

  protected $_customGroupExtends = ['Individual', 'Membership', 'Contribution'];
  protected $_customGroupGroupBy = FALSE;

  /**
   * Class constructor.
   */
  public function __construct() {

    // Check if CiviCampaign is a) enabled and b) has active campaigns
    $config = CRM_Core_Config::singleton();
    $campaignEnabled = in_array("CiviCampaign", $config->enableComponents);
    if ($campaignEnabled) {
      $getCampaigns = CRM_Campaign_BAO_Campaign::getPermissionedCampaigns(NULL, NULL, TRUE, FALSE, TRUE);
      $this->activeCampaigns = $getCampaigns['campaigns'];
      asort($this->activeCampaigns);
    }

    $this->_columns = [
      'civicrm_contact' => [
        'dao' => 'CRM_Contact_DAO_Contact',
        'fields' => $this->getBasicContactFields(),
        'filters' => [
          'sort_name' => [
            'title' => ts('Contact Name'),
            'operator' => 'like',
          ],
          'id' => ['no_display' => TRUE],
        ],
        'order_bys' => [
          'sort_name' => [
            'title' => ts('Last Name, First Name'),
            'default' => '1',
            'default_weight' => '0',
            'default_order' => 'ASC',
          ],
        ],
        'grouping' => 'contact-fields',
      ],
      'civicrm_membership' => [
        'dao' => 'CRM_Member_DAO_Membership',
        'fields' => [
          'membership_type_id' => [
            'title' => 'Membership Type',
            'required' => TRUE,
            'no_repeat' => TRUE,
          ],
          'membership_start_date' => [
            'title' => ts('Start Date'),
            'default' => TRUE,
          ],
          'membership_end_date' => [
            'title' => ts('End Date'),
            'default' => TRUE,
          ],
          'join_date' => [
            'title' => ts('Join Date'),
            'default' => TRUE,
          ],
          'membership_source' => [
            'name' => 'source',
            'title' => ts('Membership Source'),
          ],
        ],
        'filters' => [
          'join_date' => ['operatorType' => CRM_Report_Form::OP_DATE],
          'membership_start_date' => ['operatorType' => CRM_Report_Form::OP_DATE],
          'membership_end_date' => ['operatorType' => CRM_Report_Form::OP_DATE],
          'owner_membership_id' => [
            'title' => ts('Membership Owner ID'),
            'operatorType' => CRM_Report_Form::OP_INT,
          ],
          'tid' => [
            'name' => 'membership_type_id',
            'title' => ts('Membership Types'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Member_PseudoConstant::membershipType(),
          ],
        ],
        'order_bys' => [
          'membership_type_id' => [
            'title' => ts('Membership Type'),
            'type' => CRM_Utils_Type::T_INT,
            'default' => '0',
            'default_weight' => '1',
            'default_order' => 'ASC',
          ],
        ],
        'grouping' => 'member-fields',
        'group_bys' => [
          'id' => [
            'title' => ts('Membership'),
            'default' => TRUE,
          ],
        ],
      ],
      'civicrm_membership_status' => [
        'dao' => 'CRM_Member_DAO_MembershipStatus',
        'alias' => 'mem_status',
        'fields' => [
          'name' => [
            'title' => ts('Status'),
            'default' => TRUE,
          ],
        ],
        'filters' => [
          'sid' => [
            'name' => 'id',
            'title' => ts('Status'),
            'type' => CRM_Utils_Type::T_INT,
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Member_PseudoConstant::membershipStatus(NULL, NULL, 'label'),
          ],
          'is_current_member' => [
            'title' => ts('Is Current Member'),
            'type' => CRM_Utils_Type::T_BOOLEAN,
          ],
        ],
        'grouping' => 'member-fields',
      ],
      'civicrm_email' => [
        'dao' => 'CRM_Core_DAO_Email',
        'fields' => ['email' => NULL],
        'grouping' => 'contact-fields',
      ],
      'civicrm_phone' => [
        'dao' => 'CRM_Core_DAO_Phone',
        'fields' => ['phone' => NULL],
        'grouping' => 'contact-fields',
      ],
      'civicrm_contribution' => [
        'dao' => 'CRM_Contribute_DAO_Contribution',
        'fields' => [
          'contribution_id' => [
            'name' => 'id',
            'no_display' => TRUE,
            'required' => TRUE,
          ],
          'financial_type_id' => ['title' => ts('Financial Type')],
          'contribution_status_id' => ['title' => ts('Contribution Status')],
          'payment_instrument_id' => ['title' => ts('Payment Type')],
          'currency' => [
            'required' => TRUE,
            'no_display' => TRUE,
          ],
          'trxn_id' => NULL,
          'receive_date' => NULL,
          'receipt_date' => NULL,
          'fee_amount' => NULL,
          'net_amount' => NULL,
          'total_amount' => [
            'title' => ts('Payment Amount (most recent)'),
            'statistics' => ['sum' => ts('Amount')],
          ],
        ],
        'filters' => [
          'receive_date' => ['operatorType' => CRM_Report_Form::OP_DATE],
          'financial_type_id' => [
            'title' => ts('Financial Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::financialType(),
            'type' => CRM_Utils_Type::T_INT,
          ],
          'payment_instrument_id' => [
            'title' => ts('Payment Type'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::paymentInstrument(),
            'type' => CRM_Utils_Type::T_INT,
          ],
          'currency' => [
            'title' => 'Currency',
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Core_OptionGroup::values('currencies_enabled'),
            'default' => NULL,
            'type' => CRM_Utils_Type::T_STRING,
          ],
          'contribution_status_id' => [
            'title' => ts('Contribution Status'),
            'operatorType' => CRM_Report_Form::OP_MULTISELECT,
            'options' => CRM_Contribute_PseudoConstant::contributionStatus(),
            'type' => CRM_Utils_Type::T_INT,
          ],
          'total_amount' => ['title' => ts('Contribution Amount')],
        ],
        'order_bys' => [
          'receive_date' => [
            'title' => ts('Receive Date'),
            'default_weight' => '2',
            'default_order' => 'DESC',
          ],
        ],
        'grouping' => 'contri-fields',
      ],
    ] + $this->getAddressColumns([
      // These options are only excluded because they were not previously present.
      'order_by' => FALSE,
      'group_by' => FALSE,
    ]);
    $this->_groupFilter = TRUE;
    $this->_tagFilter = TRUE;

    // If we have active campaigns add those elements to both the fields and filters
    if ($campaignEnabled && !empty($this->activeCampaigns)) {
      $this->_columns['civicrm_membership']['fields']['campaign_id'] = [
        'title' => ts('Campaign'),
        'default' => FALSE,
      ];
      $this->_columns['civicrm_membership']['filters']['campaign_id'] = [
        'title' => ts('Campaign'),
        'operatorType' => CRM_Report_Form::OP_MULTISELECT,
        'options' => $this->activeCampaigns,
      ];
      $this->_columns['civicrm_membership']['order_bys']['campaign_id'] = [
        'title' => ts('Campaign'),
        'type' => CRM_Utils_Type::T_INT,
      ];

    }

    $this->_currencyColumn = 'civicrm_contribution_currency';
    parent::__construct();
  }

  public function preProcess() {
    $this->assign('reportTitle', ts('Membership Detail Report'));
    parent::preProcess();
  }

  public function select() {
    $select = $this->_columnHeaders = $this->_selectAliases = [];

    foreach ($this->_columns as $tableName => $table) {
      if (array_key_exists('fields', $table)) {
        foreach ($table['fields'] as $fieldName => $field) {
          if (!empty($field['required']) || !empty($this->_params['fields'][$fieldName])) {
            if ($tableName == 'civicrm_email') {
              $this->_emailField = TRUE;
            }
            elseif ($tableName == 'civicrm_phone') {
              $this->_phoneField = TRUE;
            }
            elseif ($tableName == 'civicrm_contribution') {
              $this->_contribField = TRUE;
            }
            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
            // Needed by CRM_Report_Form::sectionTotals() when Section Headers are used.
            $this->_selectAliases[] = "{$tableName}_{$fieldName}";
            if (array_key_exists('title', $field)) {
              $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
            }
            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type'] = $field['type'] ?? NULL;
          }
        }
      }
    }

    $this->_select = "SELECT " . implode(', ', $select) . " ";
  }

  public function from() {
    $this->_from = "
         FROM  civicrm_contact {$this->_aliases['civicrm_contact']} {$this->_aclFrom}
               INNER JOIN civicrm_membership {$this->_aliases['civicrm_membership']}
                          ON {$this->_aliases['civicrm_contact']}.id =
                             {$this->_aliases['civicrm_membership']}.contact_id AND {$this->_aliases['civicrm_membership']}.is_test = 0
               LEFT  JOIN civicrm_membership_status {$this->_aliases['civicrm_membership_status']}
                          ON {$this->_aliases['civicrm_membership_status']}.id =
                             {$this->_aliases['civicrm_membership']}.status_id ";

    if ($this->isTableSelected('civicrm_address')) {
      $this->_from .= "
             LEFT JOIN civicrm_address {$this->_aliases['civicrm_address']}
                       ON {$this->_aliases['civicrm_contact']}.id =
                          {$this->_aliases['civicrm_address']}.contact_id AND
                          {$this->_aliases['civicrm_address']}.is_primary = 1\n";
    }

    //used when email field is selected
    if ($this->_emailField) {
      $this->_from .= "
              LEFT JOIN civicrm_email {$this->_aliases['civicrm_email']}
                        ON {$this->_aliases['civicrm_contact']}.id =
                           {$this->_aliases['civicrm_email']}.contact_id AND
                           {$this->_aliases['civicrm_email']}.is_primary = 1\n";
    }
    //used when phone field is selected
    if ($this->_phoneField) {
      $this->_from .= "
              LEFT JOIN civicrm_phone {$this->_aliases['civicrm_phone']}
                        ON {$this->_aliases['civicrm_contact']}.id =
                           {$this->_aliases['civicrm_phone']}.contact_id AND
                           {$this->_aliases['civicrm_phone']}.is_primary = 1\n";
    }
    //used when contribution field is selected
    if ($this->_contribField) {
      $this->_from .= "
             LEFT JOIN civicrm_membership_payment cmp
                 ON {$this->_aliases['civicrm_membership']}.id = cmp.membership_id
             LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']}
                 ON cmp.contribution_id={$this->_aliases['civicrm_contribution']}.id\n";
    }
  }

  public function postProcess() {

    $this->beginPostProcess();

    // get the acl clauses built before we assemble the query
    $this->buildACLClause($this->_aliases['civicrm_contact']);
    $sql = $this->buildQuery(TRUE);

    $rows = [];
    $this->buildRows($sql, $rows);

    $this->formatDisplay($rows);
    $this->doTemplateAssignment($rows);
    $this->endPostProcess($rows);
  }

  /**
   * Build array of section totals for multi-level Section Headers.
   *
   * This duplicates CRM_Report_Form::sectionTotals(), which never
   * increments the loop counter used to detect the lowest-level section
   * alias. That means every alias - not just the higher-level ones - takes
   * the "roll count into total" branch below, reading $totals[$key] before
   * it has been initialised for that key and triggering an "Undefined
   * array key" warning under PHP 8.1+ whenever two or more columns are
   * used as Section Headers at once. The running total ends up correct
   * either way (each key is only encountered once, since the query is
   * grouped by all section aliases), so the only fix needed here is to
   * default a missing total to 0 instead of reading it unset.
   */
  public function sectionTotals() {
    if (empty($this->_selectAliases)) {
      return;
    }

    if (!empty($this->_sections)) {
      $select = str_ireplace('SELECT SQL_CALC_FOUND_ROWS ', 'SELECT ', $this->_select);
      $sql = "{$select} {$this->_from} {$this->_where} {$this->_groupBy} {$this->_having} {$this->_orderBy}";

      $sectionAliases = array_keys($this->_sections);

      $ifnulls = [];
      foreach (array_merge($sectionAliases, $this->_selectAliases) as $alias) {
        $ifnulls[] = "ifnull($alias, '') as $alias";
      }
      $this->_select = "SELECT " . implode(", ", $ifnulls);
      $this->_select = CRM_Contact_BAO_Query::appendAnyValueToSelect($ifnulls, $sectionAliases);

      $query = $this->_select .
        ", count(*) as ct from ($sql) as subquery group by " .
        implode(", ", $sectionAliases);

      $totals = [];
      $dao = CRM_Core_DAO::executeQuery($query);
      while ($dao->fetch()) {
        $rows[0] = $dao->toArray();
        $this->alterDisplay($rows);
        $this->alterCustomDataDisplay($rows);
        $row = $rows[0];

        $values = [];
        $i = 1;
        $aliasCount = count($sectionAliases);
        foreach ($sectionAliases as $alias) {
          $values[] = $row[$alias];
          $key = implode(CRM_Core_DAO::VALUE_SEPARATOR, $values);
          if ($i == $aliasCount) {
            $totals[$key] = $dao->ct;
          }
          else {
            $totals[$key] = ($totals[$key] ?? 0) + $dao->ct;
          }
          $i++;
        }
      }
      $this->assign('sectionTotals', $totals);
    }
  }

  /**
   * Format custom values.
   *
   * @param mixed $value
   * @param array $customField
   * @param array $fieldValueMap
   *
   * @return float|string|void
   */
  public function formatCustomValues($value, $customField, $fieldValueMap) {
    if (CRM_Utils_System::isNull($value)) {
      return NULL;
    }

    $htmlType = $customField['html_type'];

    switch ($customField['data_type']) {
      case 'Boolean':
        if ($value == '1') {
          $retValue = ts('Yes');
        }
        else {
          $retValue = ts('No');
        }
        break;

      case 'Link':
        $retValue = CRM_Utils_System::formatWikiURL($value);
        break;

      case 'File':
        $retValue = $value;
        break;

      case 'Memo':
        $retValue = $value;
        break;

      case 'Float':
        if ($htmlType == 'Text') {
          $retValue = (float) $value;
          break;
        }
      case 'Money':
        if ($htmlType == 'Text') {
          $retValue = CRM_Utils_Money::format($value, NULL, '%a');
          break;
        }
      case 'String':
      case 'Int':
        if (in_array($htmlType, [
          'Text',
          'TextArea',
        ])) {
          $retValue = $value;
          break;
        }
        else {
          $customField['options'] = CRM_Core_BAO_CustomOption::getCustomOption($customField['id']);
          $retValue = $fieldValueMap[$customField['option_group_id']][$value];
          break;
        }

      case 'StateProvince':
      case 'Country':

        switch ($htmlType) {
          case 'Multi-Select Country':
            $value = explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
            $customData = [];
            foreach ($value as $val) {
              if ($val) {
                $customData[] = CRM_Core_PseudoConstant::country($val, FALSE);
              }
            }
            $retValue = implode(', ', $customData);
            break;

          case 'Select Country':
            $retValue = CRM_Core_PseudoConstant::country($value, FALSE);
            break;

          case 'Select State/Province':
            $retValue = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
            break;

          case 'Multi-Select State/Province':
            $value = explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
            $customData = [];
            foreach ($value as $val) {
              if ($val) {
                $customData[] = CRM_Core_PseudoConstant::stateProvince($val, FALSE);
              }
            }
            $retValue = implode(', ', $customData);
            break;

          case 'Select':
          case 'Radio':
          case 'Autocomplete-Select':
            $retValue = $fieldValueMap[$customField['option_group_id']][$value];
            break;

          case 'CheckBox':
          case 'AdvMulti-Select':
          case 'Multi-Select':
            $value = explode(CRM_Core_DAO::VALUE_SEPARATOR, $value);
            $customData = [];
            foreach ($value as $val) {
              if ($val) {
                $customData[] = $fieldValueMap[$customField['option_group_id']][$val];
              }
            }
            $retValue = implode(', ', $customData);
            break;

          default:
            $retValue = $value;
        }
        break;

      default:
        $retValue = $value;
    }

    return $retValue;
  }

  /**
   * Alter display of rows.
   *
   * Iterate through the rows retrieved via SQL and make changes for display purposes,
   * such as rendering contacts as links.
   *
   * @param array $rows
   *   Rows generated by SQL, with an array for each row.
   */
  public function alterDisplay(&$rows) {
    $entryFound = FALSE;
    $checkList = [];

    $contributionTypes = CRM_Contribute_PseudoConstant::financialType();
    $contributionStatus = CRM_Contribute_PseudoConstant::contributionStatus();
    $paymentInstruments = CRM_Contribute_PseudoConstant::paymentInstrument();

    $repeatFound = FALSE;
    foreach ($rows as $rowNum => $row) {
      if ($repeatFound == FALSE ||
        $repeatFound < $rowNum - 1
      ) {
        unset($checkList);
        $checkList = [];
      }
      if (!empty($this->_noRepeats) && $this->_outputMode != 'csv') {
        // not repeat contact display names if it matches with the one
        // in previous row
        foreach ($row as $colName => $colVal) {
          if (in_array($colName, $this->_noRepeats) &&
            $rowNum > 0
          ) {
            if ($rows[$rowNum][$colName] == $rows[$rowNum - 1][$colName] ||
              (!empty($checkList[$colName]) &&
              in_array($colVal, $checkList[$colName]))
              ) {
              $rows[$rowNum][$colName] = "";
              // CRM-15917: Don't blank the name if it's a different contact
              if ($colName == 'civicrm_contact_exposed_id') {
                $rows[$rowNum]['civicrm_contact_sort_name'] = "";
              }
              $repeatFound = $rowNum;
            }
          }
          if (in_array($colName, $this->_noRepeats)) {
            $checkList[$colName][] = $colVal;
          }
        }
      }

      // Always assign an explicit value (rather than leaving a null) so that Section Headers
      // and their totals, which key on this value, behave consistently for rows with no value.
      if (array_key_exists('civicrm_membership_membership_type_id', $row)) {
        $value = $row['civicrm_membership_membership_type_id'];
        $rows[$rowNum]['civicrm_membership_membership_type_id'] = $value ? CRM_Member_PseudoConstant::membershipType($value, FALSE) : '';
        $entryFound = TRUE;
      }

      // Core's alter_display handling (see getAddressColumns()) already converts these to names
      // when rendering the report, so only convert a raw ID, which is what
      // CRM_Report_Form::sectionTotals() passes in when building Section Header totals.
      if (array_key_exists('civicrm_address_address_state_province_id', $row)) {
        $value = $row['civicrm_address_address_state_province_id'];
        if (is_numeric($value)) {
          $rows[$rowNum]['civicrm_address_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
        }
        elseif ($value === NULL) {
          $rows[$rowNum]['civicrm_address_address_state_province_id'] = '';
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_address_address_country_id', $row)) {
        $value = $row['civicrm_address_address_country_id'];
        if (is_numeric($value)) {
          $rows[$rowNum]['civicrm_address_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
        }
        elseif ($value === NULL) {
          $rows[$rowNum]['civicrm_address_address_country_id'] = '';
        }
        $entryFound = TRUE;
      }

      if (array_key_exists('civicrm_contact_sort_name', $row) &&
        $rows[$rowNum]['civicrm_contact_sort_name'] &&
        array_key_exists('civicrm_contact_id', $row)
      ) {
        $url = CRM_Utils_System::url("civicrm/contact/view",
          'reset=1&cid=' . $row['civicrm_contact_id'],
          $this->_absoluteUrl
        );
        $rows[$rowNum]['civicrm_contact_sort_name_link'] = $url;
        $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Contact Summary for this Contact.");
        $entryFound = TRUE;
      }

      if ($value = $row['civicrm_contribution_financial_type_id'] ?? NULL) {
        $rows[$rowNum]['civicrm_contribution_financial_type_id'] = $contributionTypes[$value];
        $entryFound = TRUE;
      }
      if ($value = $row['civicrm_contribution_contribution_status_id'] ?? NULL) {
        $rows[$rowNum]['civicrm_contribution_contribution_status_id'] = $contributionStatus[$value];
        $entryFound = TRUE;
      }
      if ($value = $row['civicrm_contribution_payment_instrument_id'] ?? NULL) {
        $rows[$rowNum]['civicrm_contribution_payment_instrument_id'] = $paymentInstruments[$value];
        $entryFound = TRUE;
      }

      // Convert campaign_id to campaign title
      if (array_key_exists('civicrm_membership_campaign_id', $row)) {
        $value = $row['civicrm_membership_campaign_id'];
        $rows[$rowNum]['civicrm_membership_campaign_id'] = $value ? ($this->activeCampaigns[$value] ?? $value) : '';
        $entryFound = TRUE;
      }
      $entryFound = $this->alterDisplayAddressFields($row, $rows, $rowNum, 'member/detail', 'List all memberships(s) for this ') ? TRUE : $entryFound;
      $entryFound = $this->alterDisplayContactFields($row, $rows, $rowNum, 'member/detail', 'List all memberships(s) for this ') ? TRUE : $entryFound;

      if (!$entryFound) {
        break;
      }
    }
  }

}
