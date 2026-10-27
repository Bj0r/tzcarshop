# Worksheet 4.2 — Database Failure and Recovery Drill: Guide and Answers

Environment: local MySQL, database `book` (from `DB_DATABASE` in `.env`), 127.0.0.1:3306. No credentials are recorded here.
All steps ran on the local development database only. Recovery target was a separate database, `books_recovery_test`. `.env` was not changed.

## How to understand the activity

The worksheet teaches two different "undo" tools and asks you to prove each one works:

| Tool | Undoes | Works when | Part |
|---|---|---|---|
| `ROLLBACK` | Changes in an open (uncommitted) transaction | Before `COMMIT`, InnoDB tables | C |
| Backup restore | Anything saved (committed) or lost, back to the time of the backup | A backup file exists and has been tested | D, E |

Flow: check the database (B), show rollback on a harmless table (C), export a backup (D), import it into a different database (E), compare the copy with the original, then answer scenario and reflection questions (F, G, H).

## A. Two recovery situations

**Q1. Difference between rolling back an uncommitted transaction and restoring a backup?**
A rollback cancels changes that were never committed. The database simply discards them, so it is instant and loses nothing that was saved. A backup restore rebuilds data from a saved copy taken earlier. It is needed when a change was already committed or data was lost, and it returns the data only to the moment the backup was taken, so anything newer is lost unless logs are replayed.

**Q2. Why is a backup not reliable until restoration is tested?**
A backup file existing proves only that a file was written. It may be empty, truncated, corrupt, missing tables, made with the wrong options, or too slow or too large to import. Only a successful restore, followed by checking the data, shows the backup is usable.

## B. Check the test database

- Test database name: `book`
- Initial number of books: `100` (`SELECT COUNT(*) AS total_books FROM books;`)

Checkpoint 1: `127.0.0.1` local server, local dev data (factory-generated), so it is not a live university database.

## C. Transaction rollback

Commands (one session, one connection):

```sql
CREATE TABLE recovery_lab_test (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

START TRANSACTION;
INSERT INTO recovery_lab_test (note) VALUES ('This row should be rolled back');
SELECT * FROM recovery_lab_test;   -- first SELECT
ROLLBACK;
SELECT * FROM recovery_lab_test;   -- second SELECT
```

- First SELECT showed: one row, `id 1`, `This row should be rolled back`.
- Second SELECT showed: no rows (count 0). The insert was cancelled.

Why: inside a transaction the insert is only provisional. `ROLLBACK` throws it away. This needs InnoDB (MyISAM has no transactions) and the same connection for all statements.

Screenshot 1: take it in phpMyAdmin's SQL tab after running the block above, showing the statements and the empty second result.

## D. Create a backup

Method 2 (command line), saved to `files/Worksheet_4.2_Backup/books_lab_backup.sql` (about 55 KB, not empty):

```bash
mysqldump -u YOUR_USERNAME -p book > books_lab_backup.sql
```

For Method 1 in phpMyAdmin: select `book`, Export, Quick, SQL, Export, then rename the file to `books_lab_backup.sql`.

Screenshot 2: the file in the `Worksheet_4.2_Backup` folder (e.g. `ls -l` output or the file manager). Hide any credentials.

Note: the backup was taken after `recovery_lab_test` was created, so that empty table is in the backup too.

## E. Restore into a separate database

```sql
CREATE DATABASE books_recovery_test;
```
```bash
mysql -u YOUR_USERNAME -p books_recovery_test < books_lab_backup.sql
```
(phpMyAdmin: select `books_recovery_test`, Import, choose the file, SQL format, Go.)

Verification in `books_recovery_test`:

```sql
SELECT COUNT(*) AS total_books FROM books;
SELECT id, title, stock, amount FROM books ORDER BY id LIMIT 10;
```

- Restored number of books: `100`
- Did the sample records match? Yes. The count matched the original (100), and the sum of `stock` (46991) and `amount` (440325.02) matched across both databases. The first 10 rows (ids 1 to 10) have the same titles, stock and amount as the original. No data changed between export and verification, so no discrepancy needs explaining.

Screenshot 3: phpMyAdmin showing `books_recovery_test` and the result of the two queries.

Important: do not point `.env` `DB_DATABASE` to `books_recovery_test`. The copy is only for inspection.

## F. Failure scenario analysis

| Scenario | Proposed response |
|---|---|
| 1. An insert fails before a transaction commits. | Transaction rollback. Nothing was committed, so `ROLLBACK` discards the partial work immediately. |
| 2. An administrator deletes records and commits the deletion. | Restore from the latest backup, ideally into a separate database, then copy back the lost rows. If binary logs were retained, replay them up to just before the delete (point-in-time recovery). Rollback cannot undo a commit. |
| 3. The database server crashes unexpectedly. | InnoDB crash recovery. On restart it uses the redo log to finish committed work and rolls back uncommitted transactions automatically. A backup is only needed if files are damaged. |
| 4. A backup file exists, but no one has tested importing it. | Treat it as unproven. Test-restore it into a separate database and verify counts and sample data now, before a real failure. |
| 5. A server's storage is permanently lost. | Restore the latest backup (plus retained binary logs if available) onto replacement infrastructure. This only works if backups are stored off the failed server. |

## G. Recovery objectives: RPO and RTO

Scenario: RPO 15 minutes, RTO 2 hours.

**Q18. Would a daily backup alone normally demonstrate the 15-minute RPO can be met?**
No. A daily backup can lose up to 24 hours of data, far above 15 minutes of acceptable loss.

**Q19. What additional arrangements might be needed?**
Binary logging with log shipping or archiving every few minutes for point-in-time recovery, or frequent incremental backups, or a replica that follows the primary. Also off-site or off-server backup storage, backup monitoring and alerts, replacement servers or a standby ready to use, a written runbook, and regular restore tests.

**Q20. How to test whether the two-hour RTO is realistic?**
Run a timed recovery drill: start the clock at the simulated failure, restore the latest backup to replacement infrastructure, replay logs, run verification queries and bring the application up. Compare the total time with two hours, include detection and decision time, repeat it with realistic data size, and fix the slow steps.

## H. Final reflection

**Q21. Why is transaction rollback not a replacement for backups?**
Rollback only works on uncommitted changes in the current transaction. It cannot undo committed deletes, bad updates found later, disk failure, corruption, ransomware or server loss. Backups cover those.

**Q22. Why restore to a separate test database before replacing production?**
It proves the backup works without risking the live data. If the restore fails or the data is wrong, production is untouched. It also lets you check data and time the restore before committing to it.

**Q23. What evidence gives confidence, and what limitations remain?**
Evidence: the backup file was created and non-empty, the import finished without errors, the row count was 100 in both databases, the sums of `stock` and `amount` matched, and the first 10 records matched. Limitations: it is a small local database, tested once and only on `books`; it does not test point-in-time recovery, restore time at real size, replacement hardware, user accounts or permissions, or the application running on the restored copy. Backup stored on the same machine would not survive disk loss.

**Q24. Recommendation for a small university to protect enrollment and grade records?**
Take automated daily full backups plus binary logs or frequent incremental backups to meet a defined RPO. Store copies off-server and offline or in separate cloud storage. Test restores on a schedule and record the results. Set and document RPO and RTO, restrict who can delete or drop data, use transactions for multi-step changes, monitor backup jobs, and keep a written recovery runbook.

## Short report (submission)

- Backup method: `mysqldump` command line (Method 2), file `books_lab_backup.sql`.
- Restore method: `mysql` command line import into new database `books_recovery_test`.
- Verification: row count (100 vs 100), sums of `stock` and `amount` matched, first 10 rows reviewed.
- Errors: none. `recovery_lab_test` and `books_recovery_test` did not exist beforehand, so there were no "already exists" problems.
- Cleanup (optional, only your own lab objects): `DROP TABLE recovery_lab_test;` in `book`, and `DROP DATABASE books_recovery_test;` after screenshots are taken.

## Still for you to do

- Take Screenshots 1 to 3 (the commands above reproduce each in phpMyAdmin).
- Copy the answers into the worksheet fields (Parts A, C, E, G, H) and attach the short report.
