# Memberships with Contact Custom Fields Report (au.com.agileware.membershipcustomfieldsreport)

This is a [CiviCRM](https://civicrm.org) extension that adds an enhanced Membership Detail
report. The standard CiviCRM "Membership Detail Report" does not let you filter or display
custom fields attached to Individual contacts, Memberships, or Contributions. This extension
provides a new report template, "Memberships with Contact Custom Fields Report", which extends
the standard membership report to expose those custom fields as selectable columns and filters,
alongside contact, address, email, phone, membership, membership status, contribution/payment,
and (where CiviCampaign is enabled) campaign details.

The extension is licensed under [AGPL-3.0](https://github.com/agileware/au.com.agileware.membershipcustomfieldsreport/blob/master/LICENSE.txt).

## Usage

Once installed, a new report is available from **Reports > Search > Memberships with Contact
Custom Fields Report** (or via CiviCRM's report listing), report URL
`civicrm/au.com.agileware.membershipcustomfieldsreport/MCF`.

The report supports the following, in addition to the standard CiviCRM report criteria/filter
UI:

* **Contact fields** - basic contact fields, plus any custom fields attached to the
  **Individual** contact type.
* **Membership fields** - membership type, start/join/end dates, membership source, membership
  owner ID, plus any custom fields attached to **Membership**, filterable by membership type and
  membership status.
* **Membership status** - the current status of each membership, filterable by status
  (multi-select) and by **Is Current Member** (yes/no). Is Current Member is true for statuses
  CiviCRM treats as current (by default New, Current and Grace) and false for lapsed ones (by
  default Expired, Pending, Cancelled, Deceased and Awaiting approval), so setting it to **Yes**
  excludes expired memberships. With no filter set, all memberships are listed as before.
* **Contact address, email and phone** - selectable columns for the contact's primary address,
  email and phone.
* **Contribution/payment fields** - financial type, contribution status, payment instrument,
  transaction ID, receive/receipt dates, fee/net/total amount (with a sum statistic), currency,
  plus any custom fields attached to **Contribution**. These are joined via the membership's
  linked payment(s), so a membership can appear multiple times if it has more than one linked
  contribution.
* **Campaign** - if the CiviCampaign component is enabled and active campaigns exist, a Campaign
  column and filter are added to the membership fields.
* **Sorting and grouping** - results can be ordered by contact name, membership type and the
  other Order By options, and any of these can be checked as a **Section Header** to group the
  results into headed, totaled sections (including more than one at once, such as Country and
  then Membership Type).

As with other CiviCRM reports, results can be grouped, sorted, exported (CSV/PDF), and saved or
scheduled like any standard report instance.

## Special configuration requirements

No special configuration, credentials, or setup is required. Simply enable the extension and
the report template becomes available. The **CiviMember** component must be enabled (the report
is registered against it); the Contribution/payment columns rely on the **CiviContribute**
component, and the Campaign column/filter only appear if **CiviCampaign** is enabled and has
active campaigns.

## Requirements

* CiviCRM 6.16+ (as declared in `info.xml`; earlier versions may work but are untested)
* CiviMember component enabled

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin
Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and install it with the
command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl au.com.agileware.membershipcustomfieldsreport@https://github.com/agileware/au.com.agileware.membershipcustomfieldsreport/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this
extension and install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/agileware/au.com.agileware.membershipcustomfieldsreport.git
cv en membershipcustomfieldsreport
```

# About the Authors

This CiviCRM extension was developed by the team at
[Agileware](https://agileware.com.au).

[Agileware](https://agileware.com.au) provide a range of CiviCRM
services including:

* CiviCRM migration
* CiviCRM integration
* CiviCRM extension development
* CiviCRM support
* CiviCRM hosting
* CiviCRM remote training services

Support your Australian [CiviCRM](https://civicrm.org) developers,
[contact Agileware](https://agileware.com.au/contact) today!

![Agileware](https://github.com/agileware/au.com.agileware.membershipcustomfieldsreport/raw/master/docs/logo/agileware-logo.png)
