# Audit Trail (POC)

This project includes a simple audit trail for important actions (admin CRUD, login attempts, etc.). This file explains how to install and maintain it.

## Install
1. Run the SQL migration to create the table:

   mysql -u your_user -p your_database < sql/create_audit_logs.sql

2. Confirm the table exists:

   SELECT COUNT(*) FROM audit_logs;

3. The helper is available as `Audit::log(...)` and is auto-loaded from `config/constants.php`.

4. Quick smoke test:

   - Update your `config` environment so the site can connect to DB, then run the test script:

     php scripts/audit_test.php

   - That will insert a sample audit entry and print the most recent ones.


## What is logged
- user_type (admin/customer/anonymous)
- user_id and username (where available)
- action (e.g. `create_category`, `update_category`, `customer_login`)
- entity and entity_id (e.g. `tbl_category`, `42`)
- old_value and new_value (JSON TEXT)
- meta (JSON TEXT) — free-form extra data such as success/failure, reasons
- ip, user_agent, url, created_at

Sensitive fields such as `password`, `card_number`, and `cvv` are redacted by default.

## Retention policy (suggested)
- Keep logs for 1 year by default and archive/purge older entries.
- Example purge (run monthly):

  DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

Consider moving older logs to a separate archival table or external log store if you need long-term retention.

## Notes
- The current implementation is a POC. It uses TEXT fields for broad MySQL compatibility.
- The admin viewer `admin/manage-audit.php` provides a basic web interface and CSV export.
- For production, consider:
  - Using JSON column type if your MySQL version supports it.
  - Adding more indexes for search performance.
  - Centralizing logging to an external system (ELK, Papertrail, etc.) for heavy workloads.
