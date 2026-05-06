# User Manual — Bionic Backend Application

## Table of Contents

1. [Document Purpose](#1-document-purpose)
2. [Scope](#2-scope)
3. [Roles and Access Rights](#3-roles-and-access-rights)
4. [General Usage Flow](#4-general-usage-flow)
   - [4.1 Before You Start](#41-before-you-start)
   - [4.2 Login](#42-login)
   - [4.3 Logout](#43-logout)
   - [4.4 Task Status Meaning](#44-task-status-meaning)
5. [Admin Guide](#5-admin-guide)
   - [5.1 Admin Dashboard](#51-admin-dashboard)
   - [5.2 User Management](#52-user-management)
   - [5.3 Task Master Management](#53-task-master-management)
   - [5.4 Admin End-of-Session Checklist](#54-admin-end-of-session-checklist)
6. [Operator Guide](#6-operator-guide)
   - [6.1 Operator Dashboard](#61-operator-dashboard)
   - [6.2 Submit a Task](#62-submit-a-task)
   - [6.3 Handling Revisions](#63-handling-revisions)
   - [6.4 Cancel Submission](#64-cancel-submission)
7. [Verifikator Guide](#7-verifikator-guide)
   - [7.1 Submission Review](#71-submission-review)
   - [7.2 Per-Task Verification](#72-per-task-verification)
   - [7.3 Bulk Verification](#73-bulk-verification)
   - [7.4 Recap and Export](#74-recap-and-export)
8. [Troubleshooting](#8-troubleshooting)
9. [FAQ](#9-faq)
10. [WIP Feature Notes](#10-wip-feature-notes)
11. [Document Revision Control](#11-document-revision-control)

---

## 1. Document Purpose

This document is a user guide for three main roles: Admin, Operator, and Verifikator. It focuses on daily operational workflows, not technical setup.

---

## 2. Scope

This guide covers:

1. Login and logout.
2. Main feature workflows by role.
3. Task status definitions.
4. Common end-user troubleshooting.

This guide does **not** cover:

1. Server, database, or deployment setup.
2. Environment configuration.
3. Developer API reference.

---

## 3. Roles and Access Rights

| Role | Responsibilities |
|------|-----------------|
| **Admin** | Manages users, locations, items, checklist actions, and monitors dashboard summaries. |
| **Operator** | Executes cleaning tasks, scans items, submits work results, and handles revision requests. |
| **Verifikator** | Reviews operator submissions, verifies or requests revisions, and checks report summaries. |

---

## 4. General Usage Flow

### 4.1 Before You Start

1. Ensure you have an active account.
2. Ensure your account role matches your job function.
3. Use a stable browser and internet connection.

---

### 4.2 Login

**Steps:**

1. Open the application login page.
2. Enter your username and password.
3. Click **Login**.
4. The system redirects you to the dashboard based on your role.

**Expected result:**

| Role | Redirected To |
|------|--------------|
| Admin | Admin page |
| Operator | Operator page |
| Verifikator | Verifikator page |

---

### 4.3 Logout

**Steps:**

1. Click **Logout**.
2. The system ends your session.
3. You are redirected back to the login page.

---

### 4.4 Task Status Meaning

| Status | Meaning |
|--------|---------|
| `pending` | The task has been submitted by the operator and is waiting for verification. |
| `verified` | The task has been approved by the verifikator. |
| `revisi` | The task was returned by the verifikator for operator correction. |
| `revised` | The task has been corrected by the operator and is waiting for re-verification. |

---

## 5. Admin Guide

### 5.1 Admin Dashboard

**Purpose:** View operational summary data.

**Steps:**

1. Log in as Admin.
2. Open the Admin Dashboard.
3. Review key statistics such as user totals, location count, and activity summaries.

> **Note:** Use the dashboard as a quick indicator. Validate details from data management pages.

---

### 5.2 User Management

**Purpose:** Create, update, and delete user accounts.

**Add User:**

1. Open the **User Management** menu.
2. Click **Add User**.
3. Fill required fields: name, username, password, and role.
4. Click **Save**.

**Edit User:**

1. Find the user in the table.
2. Click **Edit** on the target row.
3. Update required fields.
4. Click **Save**.

**Delete User:**

1. Find the user in the table.
2. Click **Delete** on the target row.
3. Confirm deletion in the dialog.

**Expected result:** Changes appear in the user table immediately.

---

### 5.3 Task Master Management

**Data management order:** Location → Item → Action

> **Important principles:**
> - An Item must belong to a Location.
> - An Action must belong to an Item.
> - Deleting or modifying parent data can affect child records.

**General steps:**

1. Open the **Task Management** menu.
2. Add or select a **Location**.
3. Add **Items** under that Location.
4. Add **Actions** under that Item.
5. Edit or delete as needed while respecting data relationships.

---

### 5.4 Admin End-of-Session Checklist

Before ending your session, ensure:

- [ ] Latest user data is saved.
- [ ] Location → Item → Action structure is consistent.
- [ ] Tables reflect all changes made.

---

## 6. Operator Guide

### 6.1 Operator Dashboard

**Purpose:** View assigned work locations and today's submission status.

**Steps:**

1. Log in as Operator.
2. Open the operator dashboard.
3. Review the displayed location list along with their current status.
4. Select a work location to start tasks.

---

### 6.2 Submit a Task

**Steps:**

1. From the dashboard, select the target location.
2. Open the scan page for that location.
3. Select or scan the item to process.
4. Open the action checklist form.
5. Mark all completed actions.
6. Click **Submit**.

**Expected result:** Task status becomes `pending`, waiting for verification.

---

### 6.3 Handling Revisions

**Steps:**

1. Open the **Revision** page.
2. Select a task with `revisi` status.
3. Read the correction notes from the verifikator carefully.
4. Perform the required corrections.
5. Upload revision evidence (photo) if available.
6. Click **Resubmit**.

**Expected result:** Status becomes `revised` and the task enters the re-verification queue.

---

### 6.4 Cancel Submission

Use this feature when a submission was sent incorrectly or must be withdrawn before further processing.

**Steps:**

1. Find the submission to cancel.
2. Click **Cancel**.
3. Confirm cancellation.

> **Note:** After cancellation, the task must be resubmitted if it still needs processing.

---

## 7. Verifikator Guide

### 7.1 Submission Review

**Steps:**

1. Log in as Verifikator.
2. Open the verification page.
3. Apply **location** and **date** filters to narrow down the data.
4. Search for target tasks.
5. Click a task row to open its details.

---

### 7.2 Per-Task Verification

**Approve a task:**

1. Open task details.
2. Review checklist data and supporting information.
3. Click **Approve** or **Verify**.

**Request revision:**

1. Open task details.
2. Click **Revision**.
3. Add clear and actionable revision notes so the operator can act immediately.
4. Click **Save**.

---

### 7.3 Bulk Verification

**Prerequisites:**

1. Location and date filters are correctly set.
2. Tasks in the current filter have been sampled or reviewed based on internal SOP.

**Steps:**

1. Apply location and date filters.
2. Click **Verify All**.
3. Confirm that all task statuses are updated correctly.

> **Note:** Use this feature carefully. Bulk Verify All cannot be undone in bulk.

---

### 7.4 Recap and Export

**Steps:**

1. Open the **Recap Report** menu.
2. Select the required data range (location, date).
3. Review the displayed summary.
4. Click **Export** if the report needs to be saved or shared.

---

## 8. Troubleshooting

### 8.1 Session Expired

**Symptom:** You are suddenly redirected to the login page.

**Actions:**

1. Log in again using your credentials.
2. If the issue repeats quickly, contact Admin for account verification.

---

### 8.2 Data Not Appearing in Table

**Actions:**

1. Check date and location filters — ensure the range is correct.
2. Clear all filters and search again.
3. Confirm that the data was actually submitted beforehand.

---

### 8.3 Status Not Updated After Action

**Actions:**

1. Refresh the page and recheck the status.
2. Ensure your last action was confirmed (dialog not closed before saving).
3. Repeat the action process if needed.

---

## 9. FAQ

**Q: Can an Operator verify their own tasks?**
A: No. Verification is performed only by the Verifikator.

**Q: Can Admin change the role of an existing user?**
A: Yes, via the Edit feature in the User Management menu.

**Q: When is it safe to use Verify All?**
A: When filters are correctly set and internal review (sampling) requirements have been met.

**Q: What happens if an operator cancels a pending submission?**
A: The submission is removed and must be resubmitted if still needed.

**Q: Can the verifikator view revision history?**
A: Yes, task details show revision notes and previous status history.

---

## 10. WIP Feature Notes

The following features are still under development and may not be fully functional:

1. **Shift Management** — Operator shift assignment and rotation are not yet fully available.
2. **Advanced verification flows** — Some additional verification workflows are still being developed.
3. **Unique code (QR) tracking** — Unique code-based tracking is available but may behave differently depending on system configuration.

---

## 11. Document Revision Control

| Field | Value |
|-------|-------|
| Version | Draft 1 |
| Date | April 27, 2026 |
| Status | Ready for internal review |
| Created by | GitHub Copilot |
