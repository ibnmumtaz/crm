# PRD — CRM & Sales Management System

**Version:** 1.2
**Status:** Draft
**Difficulty:** Intermediate to Advanced
**Stack:**
- **Backend:** PHP 8.3+ · Laravel 13
- **Database:** SQLite 3 (WAL mode)
- **Frontend:** HTML5 · Tailwind CSS · JS
- **Templating:** Laravel Blade
- **Build:** Vite (Tailwind + JS bundling)
- **Artisan primitives used:** Migrations · Factories · Seeders · PHP Enums · Policies · Notifications · Scheduled Commands

**Document Owner:** Product / Engineering Lead
**Last Updated:** 2025-01-15

> **Note on Laravel version:** This PRD targets Laravel 13. If you are on Laravel 11 or 12, every pattern here still applies — `Gate::before`, backed enums (PHP 8.1+), migration/factory/seeder structure, and the scheduler API are unchanged. Only minor configuration differences exist.

---

## Table of Contents

1. [Overview](#1-overview)
2. [Glossary](#2-glossary)
3. [Personas](#3-personas)
4. [Goals & Success Metrics](#4-goals--success-metrics)
5. [Scope](#5-scope)
6. [User Roles & Permissions](#6-user-roles--permissions)
7. [Features & Functional Requirements](#7-features--functional-requirements)
8. [State Machines](#8-state-machines)
9. [Data Model (SQLite)](#9-data-model-sqlite)
10. [PHP Enums](#10-php-enums)
11. [Migrations](#11-migrations)
12. [Factories](#12-factories)
13. [Seeders](#13-seeders)
14. [Technical Architecture](#14-technical-architecture)
15. [API Specification](#15-api-specification)
16. [Notification System](#16-notification-system)
17. [Audit Log Specification](#17-audit-log-specification)
18. [Security & Compliance](#18-security--compliance)
19. [Performance & Caching (SQLite)](#19-performance--caching-sqlite)
20. [Rate Limiting & Throttling](#20-rate-limiting--throttling)
21. [Error Handling Conventions](#21-error-handling-conventions)
22. [Webhooks](#22-webhooks)
23. [Import / Export](#23-import--export)
24. [Non-Functional Requirements](#24-non-functional-requirements)
25. [User Stories & Acceptance Criteria](#25-user-stories--acceptance-criteria)
26. [UI / UX Outline](#26-ui--ux-outline)
27. [Onboarding Flow](#27-onboarding-flow)
28. [Testing Strategy](#28-testing-strategy)
29. [CI/CD & Deployment](#29-cicd--deployment)
30. [Monitoring, Alerting & Logging](#30-monitoring-alerting--logging)
31. [Backup & Disaster Recovery (SQLite)](#31-backup--disaster-recovery-sqlite)
32. [Data Retention & GDPR](#32-data-retention--gdpr)
33. [Dependencies & Environment Variables](#33-dependencies--environment-variables)
34. [Release Plan & Roadmap](#34-release-plan--roadmap)
35. [Acceptance Criteria (Definition of Done)](#35-acceptance-criteria-definition-of-done)
36. [Risks & Mitigations](#36-risks--mitigations)
37. [Open Questions](#37-open-questions)
38. [Revision History](#38-revision-history)

---

## 1. Overview

### 1.1 Product Summary
A CRM & Sales Management System that helps companies manage **leads, customers, and sales teams** in one place. It replaces spreadsheets and scattered notes with a single workflow engine that tracks every prospect, records every interaction, prompts the next action, and measures performance against sales targets.

Built with a **simple, portable stack**: Laravel 13 on the backend, SQLite 3 as the database, and Blade + Tailwind CSS + vanilla JavaScript on the frontend. No SPA, no Redis, no external services required to run — making it ideal for SMBs, single-server deployments, and interview-grade portfolio projects.

### 1.2 Problem Statement
Sales teams lose deals because:
- Leads are forgotten after the first contact.
- Communication history is stored in personal inboxes and lost when a rep leaves.
- Managers have no real-time visibility into pipeline health.
- Targets are tracked manually and reported late.
- Permissions are unclear — everyone can see and edit everything.

### 1.3 Product Vision
> A CRM that is not a digital address book, but an **active workflow system**: a lead enters the pipeline, every interaction is recorded, the system proactively prompts the next action, and results are measured against goals — all governed by a clear hierarchy of roles and permissions.

### 1.4 Business Case
| Cost of doing nothing | Value of building |
|---|---|
| ~27% of leads never get a follow-up contact | Automated reminders recover 15–25% of at-risk leads |
| Reps spend ~20% of the week on manual reporting | Dashboard APIs cut reporting to < 5 min/day |
| Lost deals due to rep turnover (no history) | Institutional memory preserved on handoff |
| No visibility into target attainment | Real-time target tracking |

### 1.5 Target Users
- B2B and B2C sales companies (SMB to mid-market)
- Sales teams of 3–100 reps
- Sales operations and management
- Single-server / on-prem deployments where SQLite is preferred

---

## 2. Glossary

| Term | Definition |
|---|---|
| **Lead** | A prospect who has shown interest but has not yet been converted to a customer |
| **Opportunity / Deal** | A lead that has been qualified and has an estimated value and close date |
| **Pipeline** | An ordered set of stages a lead/deal moves through toward close |
| **Stage** | A single step in a pipeline (e.g., "Contacted", "Negotiating") |
| **Won / Lost** | Terminal stages indicating closed-won or closed-lost |
| **Communication** | Any logged interaction — call, email, meeting, note, proposal |
| **Task** | A follow-up action item tied to a lead/deal, with due date and assignee |
| **Target** | A numeric goal (revenue, deal count, call count) per period |
| **Scope** | The data a user can see or act on: `own`, `team`, or `all` |
| **Bypass** | Super Admin's ability to skip permission checks via `Gate::before` |
| **Impersonation** | Temporarily acting as another user for support |
| **Soft delete** | Marking a record deleted without removing it from the DB |
| **Stalled lead** | A lead with no logged contact in N days (default 14) |
| **Enum** | A PHP 8.1+ backed enum representing a fixed set of values (e.g., `LeadStatus`) |
| **Factory** | Laravel class that generates fake model instances for testing/seeding |
| **Seeder** | Laravel class that inserts reference or demo data into the DB |
| **WAL mode** | SQLite's Write-Ahead Logging mode — enables concurrent readers with one writer |

---

## 3. Personas

### 3.1 Persona A — "Rashid, the Sales Rep" (daily user)
- **Age / Role:** 27, SMB sales rep
- **Daily goal:** Work 30 leads, log calls, never miss a follow-up
- **Frustrations:** Losing leads in spreadsheets; forgetting callbacks
- **Needs:** Fast logging, clear "what to do today" view, mobile-friendly UI
- **Success:** Zero overdue tasks; above target

### 3.2 Persona B — "Ayesha, the Sales Manager"
- **Age / Role:** 35, manages 8 reps
- **Daily goal:** Know the team’s pipeline health before the 9 AM standup
- **Frustrations:** Reporting takes hours; no real-time visibility
- **Needs:** Team pipeline value, stalled leads, rep leaderboard, target progress
- **Success:** Reports to leadership in 5 minutes; spots problems early

### 3.3 Persona C — "Bilal, the CRM Admin"
- **Age / Role:** 32, Sales Ops
- **Daily goal:** Keep the CRM matching the company’s sales process
- **Frustrations:** Rigid CRM that can't reflect their workflow
- **Needs:** Configure pipelines, custom stages, templates, permissions
- **Success:** CRM evolves with the process, not against it

### 3.4 Persona D — "Sarah, the Analyst"
- **Age / Role:** 30, Business Analyst
- **Daily goal:** Produce weekly/monthly sales reports
- **Frustrations:** Pulling raw data from multiple sources
- **Needs:** Clean dashboards, CSV export, trend charts
- **Success:** Reports ready on time; numbers trustworthy

### 3.5 Persona E — "Zara, the Super Admin"
- **Age / Role:** 40, CTO
- **Daily goal:** Keep the platform secure and operational
- **Frustrations:** Silent failures, no audit trail
- **Needs:** System health, audit logs, impersonation for debugging, 2FA
- **Success:** Zero security incidents; fast incident response

---

## 4. Goals & Success Metrics

| Goal | Metric | Target |
|---|---|---|
| Reduce lost leads due to missed follow-up | % of leads with an overdue task | < 5% |
| Improve pipeline visibility | % of deals with complete stage history | 100% |
| Increase follow-up consistency | Avg. days between touches on active lead | ≤ 3 days |
| Improve manager oversight | Time to view team pipeline report | < 10 seconds |
| Improve accountability | % of reps with a set monthly target | 100% |
| Secure access control | Unauthorized access attempts blocked | 100% |
| Reduce reporting time | Avg. minutes to generate a weekly report | ≤ 5 |
| Uptime | Monthly availability | ≥ 99.5% |

---

## 5. Scope

### 5.1 In Scope (v1)
- Lead tracking and sales pipelines
- Customer communication history
- Follow-up reminders and tasks
- Sales targets and analytics dashboards
- Authentication, authorization, and role-based access control
- Scheduled commands and notifications
- Dashboard APIs
- Import/export (CSV)
- Audit logging
- Webhooks (outbound)

### 5.2 Out of Scope (v1)
- Email inbox sync (IMAP/Gmail/Outlook)
- Telephony / VoIP integration
- Marketing automation (campaigns, drip emails)
- Billing and invoicing
- Customer-facing portal
- Mobile native apps (responsive web only)
- Multi-tenant SaaS mode (planned for v2)
- AI-based lead scoring (planned for v3)
- SPA / frontend framework (React / Vue / Inertia) — **Blade + Tailwind + JS only**

---

## 6. User Roles & Permissions

### 6.1 Role Hierarchy

```
Super Admin        → platform/system owner (bypasses all checks)
   │
   ▼
Admin              → runs the company's CRM
   │
   ▼
Sales Manager      → runs a team
   │
   ▼
Sales Representative → works own leads
   │
   ▼
Analyst / Viewer   → read-only insight
```

### 6.2 Role Definitions

#### 6.2.1 Super Admin
**Purpose:** System/platform owner. Runs the CRM itself, not the sales business.

**Capabilities:**
- Bypass **all** permission and policy checks via `Gate::before`
- Manage Admins (create, suspend, delete)
- Reset passwords for any user
- Force logout / revoke sessions and API tokens
- Impersonate any user (audit-logged, signed short-lived token)
- Access system health, queues, failed jobs, and logs
- Manage API keys, webhooks, and integrations
- Restore or permanently delete any record (including soft-deleted)
- Cross-tenant access (when multi-tenant mode is enabled)
- Manage role definitions and permission lists

**Constraints:**
- Seeded only via artisan command (`php artisan crm:create-super-admin`)
- Mandatory 2FA
- Every action audit-logged
- Last Super Admin cannot be deleted or demoted
- Excluded from analytics and leaderboards

#### 6.2.2 Admin (CRM Administrator)
**Purpose:** Runs the company’s CRM day to day.

**Capabilities:**
- Manage users (except Super Admin)
- Configure pipelines, stages, lead sources, custom fields
- View all leads, customers, deals, tasks, and analytics
- Reassign leads and override ownership
- Set company-wide targets
- Access all reports and audit logs
- Manage notification templates

**Constraints:**
- Cannot manage Super Admin
- Cannot change system-level settings reserved for Super Admin
- Still bound by policies (no bypass)

#### 6.2.3 Sales Manager
**Purpose:** Responsible for a sales team’s performance.

**Capabilities:**
- View all leads/deals **for their team**
- Reassign leads within the team
- Set targets for sales reps
- View team analytics: win rate, pipeline value, rep leaderboard
- Create/edit tasks for team members
- View team communication history
- Approve deal stage changes (if approval workflow enabled)

**Constraints:**
- Cannot change system settings or manage user roles
- Cannot see other teams’ data unless explicitly permitted

#### 6.2.4 Sales Representative
**Purpose:** Frontline user who works leads daily.

**Capabilities:**
- Create and manage **their own** leads and deals
- Move deals through pipeline stages
- Log calls, emails, meetings, and notes
- Create and complete own follow-up tasks
- View own targets and performance
- View communication history for own accounts

**Constraints:**
- Cannot see other reps’ leads (unless shared)
- Cannot set targets, view company-wide analytics, or delete records permanently
- Cannot change pipeline settings

#### 6.2.5 Analyst / Viewer
**Purpose:** Management or reporting users who need insight without modifying data.

**Capabilities:**
- View dashboards, targets, pipeline reports, win/loss analytics
- Export reports (if permitted)
- View leads/deals in read-only mode

**Constraints:**
- Cannot create, edit, or delete any sales records
- Cannot log communications or reassign deals
- Cannot change targets

### 6.3 Permission Matrix

| Permission | Super Admin | Admin | Sales Manager | Sales Rep | Analyst |
|---|:---:|:---:|:---:|:---:|:---:|
| Manage Super Admins | ✅ | ❌ | ❌ | ❌ | ❌ |
| Manage Admins | ✅ | ❌ | ❌ | ❌ | ❌ |
| Manage users | ✅ | ✅ (except SA) | ❌ | ❌ | ❌ |
| System settings / integrations | ✅ | Limited | ❌ | ❌ | ❌ |
| Pipeline config | ✅ | ✅ | ❌ | ❌ | ❌ |
| View all leads | ✅ | ✅ | Team only | Own only | Read-only |
| Create/edit leads | ✅ | ✅ | ✅ | ✅ own | ❌ |
| Delete/restore records | ✅ | Limited | ❌ | ❌ | ❌ |
| Reassign leads | ✅ | ✅ | Team | ❌ | ❌ |
| Log communications | ✅ | ✅ | ✅ | ✅ own | ❌ |
| Create follow-up tasks | ✅ | ✅ | ✅ team | ✅ own | ❌ |
| Set targets | ✅ | ✅ | ✅ team | ❌ | ❌ |
| View analytics | ✅ all | ✅ all | ✅ team | ✅ own | ✅ read-only |
| Export reports | ✅ | ✅ | ✅ | ❌ | ✅ if allowed |
| Impersonate users | ✅ | ❌ | ❌ | ❌ | ❌ |
| Access logs / queues | ✅ | ❌ | ❌ | ❌ | ❌ |
| Cross-tenant access | ✅ | ❌ | ❌ | ❌ | ❌ |

### 6.4 Authorization Design Rules
1. **Two layers:** Permission (what action) + Data scope (which records).
2. **Data scopes:** `own` → `team` → `all`.
3. **Policies** enforce record-level checks (`LeadPolicy::update($user, $lead)`).
4. **Gate::before** grants Super Admin a universal bypass.
5. **Query scoping** by `owner_id`, `team_id`, or `manager_id` at the repository/query level.
6. **UI-level checks** (`@can`) mirror server-side checks — never as the only enforcement.
7. **API tokens** inherit the user’s permissions and scope — never more.

---

## 7. Features & Functional Requirements

### 7.1 Lead Tracking & Sales Pipelines

**Functional Requirements**

| ID | Requirement | Priority |
|---|---|---|
| FR-1.1 | Create leads with: name, company, email, phone, source, owner, status, value, expected close date | Must |
| FR-1.2 | Configurable lead sources (Website, Referral, Cold Call, Event, Other) | Must |
| FR-1.3 | Configurable pipelines with ordered stages (New → Contacted → Interested → Negotiating → Won/Lost) | Must |
| FR-1.4 | Kanban board with drag-and-drop stage changes (permission-checked, vanilla JS + SortableJS) | Must |
| FR-1.5 | List/table view with filters (owner, stage, source, value, date) | Must |
| FR-1.6 | Automatic stage-change history logging (who, when, from → to) | Must |
| FR-1.7 | Lead reassignment (Admin/Manager; audit-logged) | Must |
| FR-1.8 | Soft delete + restore (Admin+); permanent delete (Super Admin only) | Must |
| FR-1.9 | CSV import/export (Admin+) | Should |
| FR-1.10 | Duplicate detection on email/phone at creation time | Should |
| FR-1.11 | Bulk actions (assign, stage change, delete) for Admin/Manager | Should |
| FR-1.12 | Custom fields per pipeline (v1.1) | Could |
| FR-1.13 | Lead conversion to customer on Won stage | Must |
| FR-1.14 | File attachments on leads (proposals, contracts) — stored on `storage/app/` | Should |

**Business Rules**
- A lead must have exactly one `owner_id`.
- A lead cannot skip stages backward without a reason note (audit-logged).
- Won/Lost stages are terminal; reopening requires Admin permission.
- Duplicate detection: block if email OR phone matches existing lead in same pipeline.
- Deleted leads are soft-deleted; a scheduled command purges after 90 days (Super Admin configurable).

---

### 7.2 Customer Communication History

| ID | Requirement | Priority |
|---|---|---|
| FR-2.1 | Log interactions: Call, Email, Meeting, Note, Proposal, Other | Must |
| FR-2.2 | Each entry stores: user, timestamp, type, subject, body, outcome, attachments | Must |
| FR-2.3 | Chronological timeline per lead/customer/deal | Must |
| FR-2.4 | Filter timeline by type, user, and date range | Must |
| FR-2.5 | Immutable history — edits allowed only within 15 min and audit-logged | Must |
| FR-2.6 | Communication history preserved after reassignment | Must |
| FR-2.7 | Full-text search on subject + body (SQLite FTS5) | Should |
| FR-2.8 | @-mentions of team members in notes trigger notifications | Could |
| FR-2.9 | File attachments (max 10 MB per file, 5 files per entry) | Should |

**Business Rules**
- Communications cannot be deleted by Sales Rep; soft delete only by Admin+.
- Timestamps are always stored in UTC; displayed in user timezone.
- Editing a communication creates a new audit entry.

---

### 7.3 Follow-Up Reminders & Tasks

| ID | Requirement | Priority |
|---|---|---|
| FR-3.1 | Create follow-up tasks tied to a lead/deal: title, due date/time, assignee, priority, notes | Must |
| FR-3.2 | Task statuses: Pending, In Progress, Completed, Overdue, Cancelled | Must |
| FR-3.3 | In-app + email + database notifications when a task is due or overdue | Must |
| FR-3.4 | Scheduled commands: flag overdue, dispatch reminders, flag stalled leads | Must |
| FR-3.5 | Automatic task creation triggers (on stage change, assignment, inactivity) | Must |
| FR-3.6 | Task views: My Tasks, Team Tasks (Manager), All Tasks (Admin) | Must |
| FR-3.7 | Calendar view of upcoming follow-ups (vanilla JS + FullCalendar) | Should |
| FR-3.8 | Task templates (reusable) | Should |
| FR-3.9 | Recurring tasks (daily/weekly) | Could |
| FR-3.10 | Task completion requires an outcome note (configurable) | Should |

**Business Rules**
- A task can only be assigned to a user with permission on the linked lead.
- Completing a task does not auto-change the lead stage (explicit action).
- Overdue threshold = task `due_at` + 1 minute.
- Stalled lead threshold = configurable (default 14 days).

---

### 7.4 Sales Targets & Analytics

| ID | Requirement | Priority |
|---|---|---|
| FR-4.1 | Set targets by period (monthly/quarterly) for individuals, teams, or company | Must |
| FR-4.2 | Target types: revenue, deal count, call count | Must |
| FR-4.3 | Real-time progress display vs. target | Must |
| FR-4.4 | Dashboard metrics (pipeline value, win rate, source performance, leaderboard, overdue tasks) | Must |
| FR-4.5 | Dashboard APIs return aggregated JSON | Must |
| FR-4.6 | Date range filtering on all analytics | Must |
| FR-4.7 | Export reports to CSV (permission-checked). PDF optional. | Should |
| FR-4.8 | Super Admin excluded from analytics and leaderboards | Must |
| FR-4.9 | Scheduled digest email (weekly) of team performance | Should |
| FR-4.10 | Forecast metrics (weighted pipeline by stage probability) | Could |

**Business Rules**
- A user/team can have at most one target per type per period.
- Overlapping periods are rejected (e.g., two monthly targets for January).
- Analytics exclude soft-deleted and Super Admin records.
- Numbers cached for 60 s and invalidated on any write to relevant tables.

---

## 8. State Machines

### 8.1 Lead Lifecycle

```
       ┌────────┐   ┌──────────┐   ┌────────────┐   ┌─────────────┐   ┌───────┐
New ──▶│Contacted│──▶│Interested│──▶│Negotiating  │──▶│Won         │
       └────────┘   └──────────┘   └────────────┘   └─────────────┘
           │             │               │                 ▲
           ▼             ▼               ▼                 │
       ┌─────────────────────────────────────┐            │
       │              Lost                   │────────────┘
       └─────────────────────────────────────┘  (reopen: Admin+)
```

| From | To | Guard |
|---|---|---|
| Any | Any forward | `lead.update` permission + ownership scope |
| Any | Lost | `lead.update` permission + reason note required |
| Won → Any | Reopen | `lead.reopen` permission (Admin only) |
| Any | Deleted | `lead.delete` (Admin+); permanent = Super Admin |

### 8.2 Task Lifecycle

```
       ┌─────────┐   ┌────────────┐   ┌───────────┐
       │ Pending │──▶│ In Progress│──▶│ Completed │
       └─────────┘   └────────────┘   └───────────┘
            │              │
            ▼              ▼
       ┌─────────┐   ┌───────────┐
       │ Overdue │──▶│ Cancelled │
       └─────────┘   └───────────┘
```

### 8.3 User Account Lifecycle

```
Active ──▶ Suspended ──▶ Deleted (soft)
   ▲            │
   └────────────┘  (reactivate: Admin+)
```

---

## 9. Data Model (SQLite)

### 9.1 SQLite Configuration

SQLite is the **single source of truth**. Enable WAL mode on app boot for read concurrency and set foreign keys per connection.

**Migration (`AppServiceProvider` or middleware)**
```php
DB::statement('PRAGMA journal_mode = WAL');
DB::statement('PRAGMA foreign_keys = ON');
DB::statement('PRAGMA busy_timeout = 5000');
DB::statement('PRAGMA synchronous = NORMAL');
```

**`.env`**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

**Rules:**
- All foreign keys enforced (`foreign_keys = ON`).
- Timestamps stored as **TEXT in ISO-8601 UTC** (Laravel default).
- Enums stored as `VARCHAR` with `CHECK` constraints (SQLite has no native `ENUM`).
- JSON stored as `TEXT` with `json_extract()` for queries (JSON1 extension is bundled).
- Booleans stored as `TINYINT(1)` (SQLite has no `BOOLEAN`).
- Decimal values stored as `NUMERIC` — sums cast to `float`/`int` in PHP where needed.
- Single-writer model: one queue worker, one scheduler process. Batch writes when possible.

### 9.2 Core Tables

| Table | Purpose |
|---|---|
| `users` | All users (all roles) |
| `roles` | Role definitions |
| `permissions` | Permission definitions |
| `role_has_permissions` | Many-to-many role ↔ permission |
| `model_has_roles` | User ↔ role assignment |
| `teams` | Sales teams (for scoping) |
| `team_user` | Team membership |
| `leads` | Lead records |
| `pipelines` | Pipeline definitions |
| `pipeline_stages` | Ordered stages per pipeline |
| `lead_stage_history` | Stage transitions (audit) |
| `lead_sources` | Configurable sources |
| `customers` | Converted customers (from won leads) |
| `communications` | Interaction log entries |
| `attachments` | File attachments (polymorphic) |
| `tasks` | Follow-up tasks |
| `targets` | Sales targets per user/team |
| `activities` | Global audit log |
| `notifications` | Laravel database notifications |
| `webhooks` | Outbound webhook configs |
| `webhook_deliveries` | Webhook delivery log |
| `jobs` | Database queue (v1 — no Redis) |
| `failed_jobs` | Failed queue jobs |
| `cache` | Database cache store |
| `cache_locks` | Database cache locks |
| `sessions` | Session storage |

### 9.3 Selected Table Schemas (SQLite)

#### `leads`

```sql
CREATE TABLE leads (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    owner_id        INTEGER NOT NULL,
    team_id         INTEGER NULL,
    pipeline_id     INTEGER NOT NULL,
    stage_id        INTEGER NOT NULL,
    source_id       INTEGER NULL,
    name            VARCHAR(255) NOT NULL,
    company         VARCHAR(255) NULL,
    email           VARCHAR(255) NULL,
    phone           VARCHAR(50) NULL,
    value           NUMERIC DEFAULT 0,
    currency        CHAR(3) DEFAULT 'USD',
    expected_close  DATE NULL,
    status          VARCHAR(20) NOT NULL DEFAULT 'active'
                    CHECK (status IN ('active','won','lost','archived')),
    lost_reason     VARCHAR(255) NULL,
    last_contact_at DATETIME NULL,
    stalled_at      DATETIME NULL,
    created_at      DATETIME NULL,
    updated_at      DATETIME NULL,
    deleted_at      DATETIME NULL,
    FOREIGN KEY (owner_id)    REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id)     REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (pipeline_id) REFERENCES pipelines(id) ON DELETE RESTRICT,
    FOREIGN KEY (stage_id)    REFERENCES pipeline_stages(id) ON DELETE RESTRICT,
    FOREIGN KEY (source_id)   REFERENCES lead_sources(id) ON DELETE SET NULL
);
CREATE INDEX idx_leads_owner ON leads(owner_id);
CREATE INDEX idx_leads_team ON leads(team_id);
CREATE INDEX idx_leads_stage ON leads(stage_id);
CREATE INDEX idx_leads_last_contact ON leads(last_contact_at);
CREATE UNIQUE INDEX idx_leads_email_pipeline ON leads(email, pipeline_id) WHERE email IS NOT NULL;
```

#### `communications`

```sql
CREATE TABLE communications (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    lead_id        INTEGER NOT NULL,
    user_id        INTEGER NOT NULL,
    type           VARCHAR(20) NOT NULL
                   CHECK (type IN ('call','email','meeting','note','proposal','other')),
    subject        VARCHAR(255) NULL,
    body           TEXT NULL,
    outcome        VARCHAR(255) NULL,
    occurred_at    DATETIME NOT NULL,
    editable_until DATETIME NULL,
    created_at     DATETIME NULL,
    updated_at     DATETIME NULL,
    deleted_at     DATETIME NULL,
    FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX idx_comm_lead_occurred ON communications(lead_id, occurred_at);
```

#### `tasks`

```sql
CREATE TABLE tasks (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    lead_id      INTEGER NULL,
    assignee_id  INTEGER NOT NULL,
    creator_id   INTEGER NOT NULL,
    title        VARCHAR(255) NOT NULL,
    notes        TEXT NULL,
    priority     VARCHAR(20) NOT NULL DEFAULT 'medium'
                 CHECK (priority IN ('low','medium','high','urgent')),
    status       VARCHAR(20) NOT NULL DEFAULT 'pending'
                 CHECK (status IN ('pending','in_progress','completed','overdue','cancelled')),
    due_at       DATETIME NOT NULL,
    completed_at DATETIME NULL,
    outcome      TEXT NULL,
    created_at   DATETIME NULL,
    updated_at   DATETIME NULL,
    deleted_at   DATETIME NULL,
    FOREIGN KEY (lead_id)     REFERENCES leads(id) ON DELETE CASCADE,
    FOREIGN KEY (assignee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id)  REFERENCES users(id) ON DELETE CASCADE
);
CREATE INDEX idx_tasks_assignee_status_due ON tasks(assignee_id, status, due_at);
```

#### `targets`

```sql
CREATE TABLE targets (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id      INTEGER NULL,
    team_id      INTEGER NULL,
    type         VARCHAR(20) NOT NULL
                 CHECK (type IN ('revenue','deal_count','call_count')),
    period       VARCHAR(20) NOT NULL
                 CHECK (period IN ('monthly','quarterly')),
    period_start DATE NOT NULL,
    period_end   DATE NOT NULL,
    amount       NUMERIC NOT NULL,
    created_by   INTEGER NOT NULL,
    created_at   DATETIME NULL,
    updated_at   DATETIME NULL,
    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id)    REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    CHECK (user_id IS NOT NULL OR team_id IS NOT NULL)
);
CREATE UNIQUE INDEX uniq_target_user_period
    ON targets(user_id, type, period_start) WHERE user_id IS NOT NULL;
CREATE UNIQUE INDEX uniq_target_team_period
    ON targets(team_id, type, period_start) WHERE team_id IS NOT NULL;
```

#### `activities` (audit log)

```sql
CREATE TABLE activities (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    actor_id     INTEGER NULL,
    action       VARCHAR(100) NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id   INTEGER NULL,
    properties   TEXT NULL,   -- JSON
    ip_address   VARCHAR(45) NULL,
    user_agent   VARCHAR(255) NULL,
    created_at   DATETIME NULL,
    FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
);
CREATE INDEX idx_act_subject ON activities(subject_type, subject_id);
CREATE INDEX idx_act_actor_created ON activities(actor_id, created_at);
```

### 9.4 Full-Text Search (Optional, FTS5)

```sql
CREATE VIRTUAL TABLE communications_fts USING fts5(
    subject, body, content='communications', content_rowid='id'
);
-- keep in sync via triggers
```

### 9.5 Key Relationships
- `users` ↔ `roles` (many-to-many)
- `users` ↔ `teams` (many-to-many)
- `leads.owner_id` → `users.id`
- `leads.pipeline_id` → `pipelines.id`
- `leads.stage_id` → `pipeline_stages.id`
- `communications.lead_id` → `leads.id`
- `tasks.lead_id` → `leads.id`, `tasks.assignee_id` → `users.id`
- `targets.user_id` / `targets.team_id`

### 9.6 Soft Deletes
- `leads`, `communications`, `tasks` → soft-deletable
- Restore: Admin+; Permanent delete: Super Admin only

---

## 10. PHP Enums

All enumerated columns are represented by **PHP 8.1+ backed enums** and cast at the model level. Enums are backed by `string` so they serialize to stable values in SQLite.

### 10.1 `LeadStatus`

```php
namespace App\Enums;

enum LeadStatus: string
{
    case Active   = 'active';
    case Won      = 'won';
    case Lost     = 'lost';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active   => 'Active',
            self::Won      => 'Won',
            self::Lost     => 'Lost',
            self::Archived => 'Archived',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Won, self::Lost], true);
    }

    public function color(): string
    {
        return match ($this) {
            self::Active   => 'blue',
            self::Won      => 'green',
            self::Lost     => 'red',
            self::Archived => 'gray',
        };
    }
}
```

### 10.2 `TaskStatus`

```php
namespace App\Enums;

enum TaskStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Overdue    = 'overdue';
    case Cancelled  = 'cancelled';

    public function isOpen(): bool
    {
        return in_array($this, [self::Pending, self::InProgress, self::Overdue], true);
    }
}
```

### 10.3 `TaskPriority`

```php
namespace App\Enums;

enum TaskPriority: string
{
    case Low    = 'low';
    case Medium = 'medium';
    case High   = 'high';
    case Urgent = 'urgent';

    public function weight(): int
    {
        return match ($this) {
            self::Low    => 1,
            self::Medium => 2,
            self::High   => 3,
            self::Urgent => 4,
        };
    }
}
```

### 10.4 `CommunicationType`

```php
namespace App\Enums;

enum CommunicationType: string
{
    case Call     = 'call';
    case Email    = 'email';
    case Meeting  = 'meeting';
    case Note     = 'note';
    case Proposal = 'proposal';
    case Other    = 'other';
}
```

### 10.5 `TargetType`

```php
namespace App\Enums;

enum TargetType: string
{
    case Revenue    = 'revenue';
    case DealCount  = 'deal_count';
    case CallCount  = 'call_count';
}
```

### 10.6 `TargetPeriod`

```php
namespace App\Enums;

enum TargetPeriod: string
{
    case Monthly   = 'monthly';
    case Quarterly = 'quarterly';

    public function startOf(\DateTimeInterface $date): \Carbon\Carbon
    {
        $c = \Carbon\Carbon::parse($date);
        return match ($this) {
            self::Monthly   => $c->copy()->startOfMonth(),
            self::Quarterly => $c->copy()->startOfQuarter(),
        };
    }

    public function endOf(\DateTimeInterface $date): \Carbon\Carbon
    {
        $c = \Carbon\Carbon::parse($date);
        return match ($this) {
            self::Monthly   => $c->copy()->endOfMonth(),
            self::Quarterly => $c->copy()->endOfQuarter(),
        };
    }
}
```

### 10.7 `UserStatus`

```php
namespace App\Enums;

enum UserStatus: string
{
    case Active    = 'active';
    case Suspended = 'suspended';
}
```

### 10.8 Model Casts

In each model, cast the enum-backed attributes:

```php
namespace App\Models;

use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $casts = [
        'status'          => LeadStatus::class,
        'expected_close'  => 'date',
        'last_contact_at' => 'datetime',
        'stalled_at'      => 'datetime',
        'value'           => 'decimal:2',
    ];
}
```

### 10.9 Using Enums in Blades

```blade
<select name="status">
    @foreach (App\Enums\LeadStatus::cases() as $status)
        <option value="{{ $status->value }}"
            @selected(old('status', $lead->status?->value) === $status->value)>
            {{ $status->label() }}
        </option>
    @endforeach
</select>
```

### 10.10 Using Enums in Validation

```php
use App\Enums\LeadStatus;
use Illuminate\Validation\Rules\Enum;

$request->validate([
    'status' => [new Enum(LeadStatus::class)],
]);
```

### 10.11 Using Enums in Query Scopes

```php
Lead::where('status', LeadStatus::Active)->get();
Lead::whereIn('status', [LeadStatus::Active, LeadStatus::Won])->get();
```

---

## 11. Migrations

Migrations live in `database/migrations/` and are run with `php artisan migrate`. All schema changes flow through migrations — no manual SQL against the live DB (except read-only queries).

### 11.1 Migration File List (in order)

```
2025_01_01_000001_create_users_table.php
2025_01_01_000002_create_cache_table.php
2025_01_01_000003_create_jobs_table.php
2025_01_01_000004_create_sessions_table.php
2025_01_01_000010_create_permission_tables.php        (spatie/laravel-permission)
2025_01_01_000020_create_teams_table.php
2025_01_01_000021_create_team_user_table.php
2025_01_01_000030_create_pipelines_table.php
2025_01_01_000031_create_pipeline_stages_table.php
2025_01_01_000032_create_lead_sources_table.php
2025_01_01_000040_create_leads_table.php
2025_01_01_000041_create_lead_stage_history_table.php
2025_01_01_000042_create_customers_table.php
2025_01_01_000050_create_communications_table.php
2025_01_01_000051_create_attachments_table.php
2025_01_01_000060_create_tasks_table.php
2025_01_01_000070_create_targets_table.php
2025_01_01_000080_create_activities_table.php
2025_01_01_000090_create_webhooks_table.php
2025_01_01_000091_create_webhook_deliveries_table.php
```

### 11.2 Example Migration — `create_leads_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('pipeline_id')->constrained('pipelines')->restrictOnDelete();
            $table->foreignId('stage_id')->constrained('pipeline_stages')->restrictOnDelete();
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->decimal('value', 15, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->date('expected_close')->nullable();
            $table->enum('status', ['active', 'won', 'lost', 'archived'])->default('active');
            $table->string('lost_reason')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('stalled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('team_id');
            $table->index('stage_id');
            $table->index('last_contact_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
```

> **Note:** `$table->enum(...)` on the SQLite driver creates a `VARCHAR` with a `CHECK` constraint — exactly what we want.

### 11.3 Migration Best Practices
- **Never** edit an existing migration that has run in any shared environment — add a new one.
- SQLite has limited `ALTER TABLE` support. Adding/dropping columns works; changing types requires a table rebuild (Laravel handles this transparently but it can be slow on large tables).
- Use `$table->foreignId()->constrained()` for FK safety.
- Always implement `down()` correctly for rollback.
- Enable `PRAGMA foreign_keys = ON` before migrating (see §9.1).

---

## 12. Factories

Factories generate fake model instances for tests and demo seeding. Every model that appears in tests should have a factory.

### 12.1 Factory File List

```
database/factories/UserFactory.php
database/factories/TeamFactory.php
database/factories/PipelineFactory.php
database/factories/PipelineStageFactory.php
database/factories/LeadSourceFactory.php
database/factories/LeadFactory.php
database/factories/CommunicationFactory.php
database/factories/TaskFactory.php
database/factories/TargetFactory.php
```

### 12.2 Example — `LeadFactory`

```php
<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'owner_id'       => User::factory(),
            'team_id'        => Team::factory(),
            'pipeline_id'    => Pipeline::factory(),
            'stage_id'       => PipelineStage::factory(),
            'source_id'      => null,
            'name'           => $this->faker->name(),
            'company'        => $this->faker->company(),
            'email'          => $this->faker->unique()->safeEmail(),
            'phone'          => $this->faker->phoneNumber(),
            'value'          => $this->faker->randomFloat(2, 1000, 100000),
            'currency'       => 'USD',
            'expected_close' => $this->faker->dateTimeBetween('now', '+90 days'),
            'status'         => LeadStatus::Active,
            'lost_reason'    => null,
            'last_contact_at'=> now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => ['status' => LeadStatus::Won]);
    }

    public function lost(): static
    {
        return $this->state(fn () => [
            'status'      => LeadStatus::Lost,
            'lost_reason' => 'Price too high',
        ]);
    }

    public function forOwner(User $user): static
    {
        return $this->state(fn () => [
            'owner_id' => $user->id,
            'team_id'  => $user->teams()->first()?->id,
        ]);
    }
}
```

### 12.3 Example — `TaskFactory`

```php
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'lead_id'     => Lead::factory(),
            'assignee_id' => User::factory(),
            'creator_id'  => User::factory(),
            'title'       => $this->faker->sentence(4),
            'notes'       => $this->faker->optional()->sentence(),
            'priority'    => TaskPriority::Medium,
            'status'      => TaskStatus::Pending,
            'due_at'      => now()->addDays($this->faker->numberBetween(1, 14)),
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Overdue,
            'due_at' => now()->subDays(2),
        ]);
    }

    public function forLead(Lead $lead): static
    {
        return $this->state(fn () => ['lead_id' => $lead->id]);
    }
}
```

### 12.4 Using Factories in Tests

```php
$lead = Lead::factory()
    ->forOwner($rep)
    ->won()
    ->create();

$tasks = Task::factory()
    ->count(5)
    ->forLead($lead)
    ->overdue()
    ->create();
```

---

## 13. Seeders

Seeders populate the DB with **reference data** (roles, permissions, pipelines) and optionally **demo data** (fake leads for development).

### 13.1 Seeder File List

```
database/seeders/DatabaseSeeder.php
database/seeders/RolesAndPermissionsSeeder.php
database/seeders/PipelineSeeder.php
database/seeders/LeadSourceSeeder.php
database/seeders/SuperAdminSeeder.php
database/seeders/DemoDataSeeder.php        (env-gated)
```

### 13.2 `DatabaseSeeder`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            PipelineSeeder::class,
            LeadSourceSeeder::class,
        ]);

        if (app()->environment(['local', 'development', 'testing'])) {
            $this->call(DemoDataSeeder::class);
        }

        if (app()->environment(['production']) && env('SEED_SUPER_ADMIN')) {
            $this->call(SuperAdminSeeder::class);
        }
    }
}
```

### 13.3 `RolesAndPermissionsSeeder`

```php
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // leads
            'lead.view', 'lead.create', 'lead.update', 'lead.delete',
            'lead.restore', 'lead.reassign', 'lead.reopen', 'lead.export', 'lead.import',
            // communications
            'communication.view', 'communication.create', 'communication.update', 'communication.delete',
            // tasks
            'task.view', 'task.create', 'task.update', 'task.delete',
            // targets
            'target.view', 'target.create', 'target.update', 'target.delete',
            // analytics
            'analytics.view', 'analytics.export',
            // admin
            'user.manage', 'role.manage', 'pipeline.manage',
            'source.manage', 'template.manage', 'audit.view',
            // super admin
            'system.manage', 'superadmin.manage', 'impersonate', 'pii.view',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'system.manage', 'superadmin.manage', 'impersonate',
        ])->get());

        $manager = Role::firstOrCreate(['name' => 'sales-manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'lead.view', 'lead.update', 'lead.reassign', 'lead.export',
            'communication.view', 'communication.create',
            'task.view', 'task.create', 'task.update',
            'target.view', 'target.create', 'target.update',
            'analytics.view', 'analytics.export',
        ]);

        $rep = Role::firstOrCreate(['name' => 'sales-rep', 'guard_name' => 'web']);
        $rep->syncPermissions([
            'lead.view', 'lead.create', 'lead.update',
            'communication.view', 'communication.create',
            'task.view', 'task.create', 'task.update',
            'target.view', 'analytics.view',
        ]);

        $analyst = Role::firstOrCreate(['name' => 'analyst', 'guard_name' => 'web']);
        $analyst->syncPermissions([
            'lead.view', 'communication.view', 'task.view',
            'target.view', 'analytics.view', 'analytics.export',
        ]);
    }
}
```

### 13.4 `PipelineSeeder`

```php
class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        $pipeline = Pipeline::firstOrCreate(
            ['name' => 'Default Sales Pipeline'],
            ['is_default' => true],
        );

        $stages = [
            ['name' => 'New',         'order' => 1, 'probability' => 10,  'is_won' => false, 'is_lost' => false],
            ['name' => 'Contacted',   'order' => 2, 'probability' => 25,  'is_won' => false, 'is_lost' => false],
            ['name' => 'Interested',  'order' => 3, 'probability' => 50,  'is_won' => false, 'is_lost' => false],
            ['name' => 'Negotiating', 'order' => 4, 'probability' => 75,  'is_won' => false, 'is_lost' => false],
            ['name' => 'Won',         'order' => 5, 'probability' => 100, 'is_won' => true,  'is_lost' => false],
            ['name' => 'Lost',        'order' => 6, 'probability' => 0,   'is_won' => false, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            PipelineStage::firstOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                $stage,
            );
        }
    }
}
```

### 13.5 `SuperAdminSeeder`

```php
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $pass  = env('SUPER_ADMIN_PASSWORD', Str::random(24));

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make($pass),
                'status'   => UserStatus::Active,
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole('super-admin');

        $this->command->info("Super Admin: {$email}");
        if (env('SEED_SUPER_ADMIN_SHOW_PASSWORD')) {
            $this->command->info("Password: {$pass}");
        }
    }
}
```

> **Best practice:** Only seed Super Admin via an artisan command (`php artisan crm:create-super-admin`) in production — never via `php artisan db:seed`.

### 13.6 `DemoDataSeeder`

```php
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // 3 teams, 8 reps, 2 managers, 1 admin, 1 analyst
        $admin = User::factory()->create(['email' => 'admin@demo.test']);
        $admin->assignRole('admin');

        $manager = User::factory()->create(['email' => 'manager@demo.test']);
        $manager->assignRole('sales-manager');

        $reps = User::factory()->count(8)->create();
        $reps->each->assignRole('sales-rep');

        $analyst = User::factory()->create(['email' => 'analyst@demo.test']);
        $analyst->assignRole('analyst');

        // 50 leads spread across reps
        Lead::factory()
            ->count(50)
            ->recycle($reps)
            ->create()
            ->each(function (Lead $lead) {
                // 1–5 communications per lead
                Communication::factory()
                    ->count(rand(1, 5))
                    ->forLead($lead)
                    ->create();

                // 0–3 tasks per lead
                Task::factory()
                    ->count(rand(0, 3))
                    ->forLead($lead)
                    ->create();
            });

        // 1 monthly target per rep
        $reps->each(function (User $rep) use ($faker) {
            Target::factory()->create([
                'user_id'      => $rep->id,
                'type'         => TargetType::Revenue,
                'period'       => TargetPeriod::Monthly,
                'period_start' => now()->startOfMonth(),
                'period_end'   => now()->endOfMonth(),
                'amount'       => $faker->numberBetween(50000, 200000),
                'created_by'   => $rep->id,
            ]);
        });
    }
}
```

### 13.7 Seeding Order

```
php artisan migrate:fresh --seed
```

Executes in this order:
1. `RolesAndPermissionsSeeder`
2. `PipelineSeeder`
3. `LeadSourceSeeder`
4. `DemoDataSeeder` (local/testing only)
5. `SuperAdminSeeder` (only if env flag set)

---

## 14. Technical Architecture

### 14.1 Stack Summary

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.3+ · Laravel 13 |
| **Database** | SQLite 3 (WAL mode, foreign keys ON) |
| **Frontend** | HTML5 · Blade · Tailwind CSS · Vanilla JavaScript |
| **Asset Build** | Vite + Tailwind CSS plugin |
| **Drag & Drop** | SortableJS (vanilla) |
| **Charts** | Chart.js (vanilla) |
| **Calendar** | FullCalendar (vanilla) |
| **Auth (web)** | Laravel Fortify (session + 2FA) |
| **Auth (API)** | Laravel Sanctum |
| **Roles & Permissions** | `spatie/laravel-permission` |
| **Queue** | `database` driver (no Redis) |
| **Cache** | `database` driver (no Redis) |
| **Session** | `database` driver |
| **Mail** | SMTP (Mailpit locally) |
| **Scheduler** | Laravel Scheduler (cron `* * * * *`) |

### 14.2 Why This Stack

- **SQLite:** zero ops, single file, perfect for SMBs and single-server deployments. WAL mode handles concurrent reads while one writer works.
- **Blade + Tailwind + vanilla JS:** fast to build, no SPA complexity, no Node build chain beyond Vite. Progressive enhancement via small JS modules.
- **No Redis:** database queue + database cache are sufficient for SMB-scale workloads (< 1M jobs/day). Redis can be added later without code changes.

### 14.3 Application Layers

```
HTTP Layer        → Controllers, Form Requests, Middleware, API Resources
Domain Layer      → Services, Actions, Policies, Events, Enums
Data Layer        → Eloquent Models, Repositories, Query Scopes, Factories
Infrastructure    → Queue (database), Cache (database), Mail, Notifications, Scheduler
Presentation      → Blade views, Tailwind components, Vanilla JS modules
```

### 14.4 Laravel Skills Mapping

| Laravel Skill | Role in the CRM |
|---|---|
| **Authentication** | Fortify (web) + Sanctum (API); 2FA for Super Admin |
| **Permissions** | `spatie/laravel-permission` + policies + `Gate::before` bypass |
| **Scheduled Commands** | Kernel schedule: overdue task flagging, reminders, inactivity detection |
| **Notifications** | Mail + database + optional broadcast channels; queued |
| **Dashboard APIs** | Versioned REST API with aggregated endpoints consumed by Blade + JS |
| **Migrations** | Schema definition for every table (§11) |
| **Factories** | Test data and demo data generation (§12) |
| **Seeders** | Reference data (roles, pipelines, sources) and demo data (§13) |
| **Enums** | PHP 8.1+ backed enums for all fixed-value fields (§10) |

### 14.5 Authentication

- **Fortify** for web (session-based auth, login, 2FA).
- **Sanctum** for API tokens (dashboard APIs, mobile in future).
- Super Admin: mandatory 2FA via Fortify.
- Session lifetime: 120 min default; 30 min for Super Admin.
- Password reset: signed email link.
- Force logout: Super Admin revokes sessions + tokens.
- Password policy: min 12 chars, mixed case, number, symbol, HIBP check optional.

### 14.6 Authorization

- `spatie/laravel-permission` for roles and permissions.
- `Gate::before` in `AppServiceProvider`:

```php
Gate::before(function (User $user) {
    return $user->hasRole('super-admin') ? true : null;
});
```

- Policies per model: `LeadPolicy`, `CommunicationPolicy`, `TaskPolicy`, `TargetPolicy`.
- Data scoping via query scopes: `scopeOwnedBy`, `scopeForTeam`, `scopeVisibleTo($user)`.

### 14.7 Scheduled Commands

| Command | Frequency | Purpose |
|---|---|---|
| `crm:flag-overdue-tasks` | Every 15 min | Mark tasks past due as Overdue |
| `crm:send-task-reminders` | Every 15 min | Notify assignees of due/overdue tasks |
| `crm:flag-stalled-leads` | Daily 06:00 | Flag leads with no contact in N days |
| `crm:recalculate-targets` | Hourly | Refresh target progress aggregates |
| `crm:purge-expired-tokens` | Daily | Clean expired Sanctum tokens |
| `crm:purge-soft-deleted` | Daily 03:00 | Permanently delete records past retention |
| `crm:send-weekly-digest` | Monday 07:00 | Team performance digest email |
| `crm:retry-failed-webhooks` | Every 30 min | Retry webhook deliveries with backoff |

Scheduled in `app/Console/Kernel.php` (or `routes/console.php` in Laravel 11+):

```php
Schedule::command('crm:flag-overdue-tasks')->everyFifteenMinutes();
Schedule::command('crm:send-task-reminders')->everyFifteenMinutes();
Schedule::command('crm:flag-stalled-leads')->dailyAt('06:00');
Schedule::command('crm:recalculate-targets')->hourly();
Schedule::command('crm:purge-expired-tokens')->daily();
Schedule::command('crm:purge-soft-deleted')->dailyAt('03:00');
Schedule::command('crm:send-weekly-digest')->weeklyOn(1, '07:00');
Schedule::command('crm:retry-failed-webhooks')->everyThirtyMinutes();
```

**Server cron entry:**
```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### 14.8 Queue & Jobs (Database Driver)

- Queue driver: `database` (jobs stored in `jobs` table).
- Worker: `php artisan queue:work --tries=3 --backoff=60,300,900`
- Run **a single worker** (or two at most) — SQLite has one-writer semantics.
- Failed jobs: `failed_jobs` table, retried manually or via Horizon alternative.
- Job types: notification dispatch, reminder batches, report generation, CSV imports, webhook delivery.

**Supervisor config (recommended):**
```ini
[program:crm-worker]
command=php /var/www/crm/artisan queue:work --sleep=3 --tries=3 --max-time=3600
process_name=%(program_name)s_%(process_num)02d
numprocs=1
autostart=true
autorestart=true
```

### 14.9 Frontend

- **Blade** as the templating engine — server-rendered pages.
- **Tailwind CSS** for styling — no other CSS framework.
- **Vanilla JS modules** (not jQuery) for interactivity:
  - `resources/js/kanban.js` — SortableJS drag-and-drop
  - `resources/js/charts.js` — Chart.js dashboards
  - `resources/js/calendar.js` — FullCalendar tasks view
  - `resources/js/forms.js` — fetch-based form submission + validation
  - `resources/js/notifications.js` — polling for new notifications (or SSE)
- **Vite** compiles Tailwind + JS bundles.
- **Progressive enhancement:** core pages work without JS (form posts); JS enhances UX.

---

## 15. API Specification

### 15.1 Conventions
- **Base URL:** `/api/v1`
- **Auth:** Sanctum bearer tokens
- **Content-Type:** `application/json`
- **Pagination:** `?page=1&per_page=25` (max 100)
- **Sorting:** `?sort=-created_at`
- **Filtering:** `?filter[stage_id]=3&filter[owner_id]=5`
- **Search:** `?q=term`
- **Includes:** `?include=owner,stage`

### 15.2 Response Envelope

```json
{
  "data": { ... },
  "meta": {
    "request_id": "req_01H...",
    "timestamp": "2025-01-15T10:30:00Z"
  }
}
```

### 15.3 Endpoint Catalog

**Auth**
```
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/2fa/challenge
POST   /api/v1/auth/2fa/verify
```

**Leads**
```
GET    /api/v1/leads
POST   /api/v1/leads
GET    /api/v1/leads/{id}
PATCH  /api/v1/leads/{id}
DELETE /api/v1/leads/{id}
POST   /api/v1/leads/{id}/restore
POST   /api/v1/leads/{id}/reassign
POST   /api/v1/leads/{id}/stage
GET    /api/v1/leads/{id}/timeline
POST   /api/v1/leads/import
GET    /api/v1/leads/export
```

**Communications**
```
GET    /api/v1/leads/{id}/communications
POST   /api/v1/leads/{id}/communications
PATCH  /api/v1/communications/{id}
DELETE /api/v1/communications/{id}
```

**Tasks**
```
GET    /api/v1/tasks
POST   /api/v1/tasks
PATCH  /api/v1/tasks/{id}
POST   /api/v1/tasks/{id}/complete
POST   /api/v1/tasks/{id}/cancel
GET    /api/v1/tasks/calendar
```

**Targets**
```
GET    /api/v1/targets
POST   /api/v1/targets
PATCH  /api/v1/targets/{id}
DELETE /api/v1/targets/{id}
GET    /api/v1/targets/progress
```

**Dashboard & Analytics**
```
GET    /api/v1/dashboard/summary
GET    /api/v1/dashboard/pipeline-value
GET    /api/v1/dashboard/win-rate
GET    /api/v1/dashboard/lead-source-performance
GET    /api/v1/dashboard/rep-leaderboard
GET    /api/v1/dashboard/target-progress
GET    /api/v1/dashboard/tasks-overdue
GET    /api/v1/analytics/deals?from=&to=
```

**Admin**
```
GET    /api/v1/admin/users
POST   /api/v1/admin/users
PATCH  /api/v1/admin/users/{id}
DELETE /api/v1/admin/users/{id}
GET    /api/v1/admin/roles
GET    /api/v1/admin/pipelines
POST   /api/v1/admin/pipelines
PATCH  /api/v1/admin/pipelines/{id}
GET    /api/v1/admin/audit-log
```

**Super Admin**
```
GET    /api/v1/super/system-health
GET    /api/v1/super/queues
POST   /api/v1/super/users/{id}/impersonate
POST   /api/v1/super/users/{id}/force-logout
```

### 15.4 Example — Pipeline Value

**Request:**
```http
GET /api/v1/dashboard/pipeline-value?from=2025-01-01&to=2025-01-31
Authorization: Bearer eyJ0eXAi...
```

**Response (200):**
```json
{
  "data": {
    "pipeline_value": {
      "New": 45000,
      "Contacted": 120000,
      "Interested": 87000,
      "Negotiating": 210000
    },
    "currency": "USD",
    "period": { "from": "2025-01-01", "to": "2025-01-31" }
  },
  "meta": {
    "request_id": "req_01HX...",
    "timestamp": "2025-01-15T10:30:00Z"
  }
}
```

---

## 16. Notification System

### 16.1 Channels
- **mail** — SMTP / Postmark / SES
- **database** — in-app bell feed
- **broadcast** (optional v1.1) — Pusher / Laravel Reverb
- **sms** (v1.1) — Twilio optional

### 16.2 Notification Types

| Type | Trigger | Channel | Recipient |
|---|---|---|---|
| `TaskDue` | Task due within 60 min | mail + database | Assignee |
| `TaskOverdue` | Task past due | mail + database | Assignee |
| `LeadAssigned` | Lead ownership change | mail + database | New owner |
| `DealStageChanged` | Stage transition | database | Owner + Manager |
| `TargetReached` | Target progress ≥ 100% | mail + database | Rep + Manager + Admin |
| `WeeklyDigest` | Scheduled (Mon 07:00) | mail | Manager, Admin |
| `LeadStalled` | No contact > N days | database | Owner + Manager |
| `SuperAdminLogin` | SA login | mail | Super Admin |
| `Impersonation` | SA impersonates user | mail + database | SA + target user |

### 16.3 Notification Class Example

```php
class TaskDue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task due soon: '.$this->task->title)
            ->line("Your task \"{$this->task->title}\" is due at {$this->task->due_at}.")
            ->action('Open Task', url("/tasks/{$this->task->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title'   => $this->task->title,
            'due_at'  => $this->task->due_at,
        ];
    }
}
```

---

## 17. Audit Log Specification

### 17.1 Logged Actions
- Role changes (grant, revoke)
- User creation, suspension, deletion
- Impersonation (start, end)
- Super Admin any action
- Settings changes
- Lead reassignment
- Stage transitions (also `lead_stage_history`)
- Soft delete, restore, permanent delete
- Login failures, 2FA failures
- Webhook creation/deletion
- Export of data (who, what, how many rows)

### 17.2 Entry Structure

| Field | Description |
|---|---|
| `actor_id` | User who performed the action (null if system) |
| `action` | Verb, e.g., `lead.reassigned` |
| `subject_type` | Model class |
| `subject_id` | Record ID |
| `properties` | JSON payload with before/after |
| `ip_address` | Client IP |
| `user_agent` | Client UA |
| `created_at` | UTC timestamp |

### 17.3 Retention
- Audit log retained for **2 years** minimum (configurable).
- Immutable — no update/delete through the UI.

### 17.4 Access
- Admin: view own company’s audit log.
- Super Admin: view all.
- Rep/Manager: no access.

---

## 18. Security & Compliance

### 18.1 Authentication & Session
- 2FA mandatory for Super Admin.
- Password hashing: **argon2id**.
- Cookies: `Secure`, `HttpOnly`, `SameSite=Lax`.
- Session lifetime: 120 min default; 30 min for Super Admin.
- Failed logins: 5 → 15 min lockout.
- IP-based throttling on auth endpoints.

### 18.2 Authorization
- Every route protected by middleware + policy.
- API tokens scoped to abilities.
- CSRF protection on web routes.
- Rate-limited API endpoints (see §20).

### 18.3 Data Protection
- TLS 1.2+ enforced.
- DB file permissions: `0600` owned by app user only.
- Backup files encrypted at rest.
- PII fields masked in logs and exports unless `pii.view` permission granted.

### 18.4 Threat Mitigation

| Threat | Mitigation |
|---|---|
| SQL injection | Eloquent + query bindings |
| XSS | Blade escaping + CSP headers |
| CSRF | Laravel CSRF tokens |
| Mass assignment | Form Requests + `$fillable` |
| Broken auth | Session timeouts + 2FA + rotation |
| IDOR | Policy checks + scoped queries |
| Sensitive data exposure | Masking + no raw dumps |
| Webhook spoofing | HMAC signatures |
| SQLite file theft | Filesystem permissions + optional SQLCipher |

### 18.5 Compliance
- **GDPR:** right to erasure (Super Admin action), data export, retention configurable.
- **OWASP Top 10 (2021):** baseline compliance required.

---

## 19. Performance & Caching (SQLite)

### 19.1 Performance Budgets

| Endpoint / View | Target P95 |
|---|---|
| Dashboard summary API | < 500 ms |
| Leads list (25/page) | < 300 ms |
| Kanban board (100 cards) | < 800 ms |
| Login | < 400 ms |
| Lead save | < 250 ms |
| Analytics report (30d) | < 1 s |

### 19.2 Caching Strategy

Cache store: **`database`** (table `cache`), since no Redis.

| Data | TTL | Invalidation |
|---|---|---|
| Dashboard aggregates | 60 s | On any lead/task/target write |
| Pipeline value | 60 s | On lead stage change or value update |
| Rep leaderboard | 5 min | On deal close |
| Lead sources list | 1 h | On source change |
| Role/permission matrix | 24 h | On role/permission change |
| User profile | 15 min | On user update |

Cache keys are scoped by `user_id` and data scope to prevent leakage across roles.

```php
Cache::remember("dashboard:{$user->id}:pipeline_value", 60, function () use ($user) {
    return Lead::visibleTo($user)
        ->select('stage_id', DB::raw('SUM(value) as total'))
        ->groupBy('stage_id')
        ->pluck('total', 'stage_id')
        ->toArray();
});
```

### 19.3 SQLite-Specific Tuning

- **WAL mode** — set on boot (see §9.1). Enables one writer + many concurrent readers.
- **Busy timeout** — set to 5000 ms so brief locks don't fail immediately.
- **Synchronous = NORMAL** — good balance of safety and speed with WAL.
- **Indexes** — every FK, plus `owner_id`, `team_id`, `stage_id`, `due_at`, `last_contact_at`, and `occurred_at`.
- **Avoid large transactions** — commit often to release write locks.
- **Batch inserts** — use `insert()` for bulk CSV imports.
- **`VACUUM` weekly** — run during low traffic to reclaim space (scheduled command).

### 19.4 Known SQLite Limits

| Limit | Value |
|---|---|
| Max DB size | ~281 TB (theoretical) |
| Recommended practical size | < 100 GB for this app |
| Concurrent writers | 1 |
| Concurrent readers | Unlimited (WAL) |
| Max row size | ~1 GB |

For this CRM’s target (100k leads, 1M communications), SQLite is comfortably in range.

---

## 20. Rate Limiting & Throttling

| Endpoint Group | Limit |
|---|---|
| `POST /auth/login` | 5 / 15 min per IP |
| `POST /auth/2fa/*` | 5 / 15 min per user |
| General authenticated API | 120 / min per user |
| Dashboard APIs | 60 / min per user |
| Export endpoints | 5 / hour per user |
| Import endpoints | 10 / hour per user |
| Super Admin actions | 300 / min |

Exceeded limits return `429 Too Many Requests` with `Retry-After`.

---

## 21. Error Handling Conventions

### 21.1 Status Codes

| Code | Use |
|---|---|
| 200 | OK |
| 201 | Created |
| 204 | No Content |
| 400 | Bad Request |
| 401 | Unauthenticated |
| 403 | Forbidden |
| 404 | Not found |
| 409 | Conflict |
| 422 | Validation errors |
| 429 | Rate limit |
| 500 | Server error |

### 21.2 Validation Error Format

```json
{
  "error": {
    "code": "VALIDATION_FAILED",
    "message": "The given data was invalid.",
    "details": {
      "email": ["The email field is required."],
      "value": ["The value must be a number."]
    },
    "request_id": "req_01HX..."
  }
}
```

### 21.3 Application Error Codes

| Code | Meaning |
|---|---|
| `VALIDATION_FAILED` | Input validation |
| `UNAUTHENTICATED` | Missing/invalid token |
| `FORBIDDEN` | Permission denied |
| `NOT_FOUND` | Record missing or out of scope |
| `DUPLICATE_LEAD` | Duplicate email/phone detected |
| `TARGET_OVERLAP` | Overlapping target period |
| `LAST_SUPER_ADMIN` | Cannot delete last Super Admin |
| `TASK_LOCKED` | Task already completed |
| `STAGE_TERMINAL` | Cannot transition from Won/Lost |

---

## 22. Webhooks

### 22.1 Supported Events

| Event | Payload |
|---|---|
| `lead.created` | Lead object |
| `lead.updated` | Lead + changed fields |
| `lead.stage_changed` | Lead + from/to stage |
| `lead.assigned` | Lead + old/new owner |
| `deal.won` | Lead + value |
| `deal.lost` | Lead + reason |
| `task.created` | Task object |
| `task.completed` | Task + outcome |
| `target.reached` | Target + actual |

### 22.2 Delivery
- HTTP POST to registered URL
- `X-CRM-Signature: sha256=<hmac>` header
- `X-CRM-Event: lead.created`
- `X-CRM-Delivery: uuid`
- Timeout: 10 s
- Retries: 3 with exponential backoff (1m, 5m, 15m)
- Delivery log stored in `webhook_deliveries`

---

## 23. Import / Export

### 23.1 CSV Import

**Supported entities:** leads

**Flow:**
1. User uploads CSV (max 10 MB, max 10,000 rows).
2. System validates headers against template.
3. Dry-run validation: reports errors per row before import.
4. User confirms → queued import job (database queue).
5. Per-row: create or skip on duplicate (configurable).
6. Import summary email: created, skipped, failed.

**Template columns (leads):**
```
name, company, email, phone, value, source, owner_email, stage, expected_close
```

### 23.2 CSV Export

- Triggered from list views or analytics.
- Queued for > 5,000 rows; email link when ready.
- Signed URL valid for 24 h.
- Admin+ only; audit-logged with row count.

### 23.3 Field-Level Permissions
- Export excludes PII fields if user lacks `pii.view`.
- Super Admin sees all fields.

---

## 24. Non-Functional Requirements

| Category | Requirement |
|---|---|
| **Performance** | Dashboard APIs < 500 ms (cached); list views paginated; page load < 2 s |
| **Scalability** | Support up to 100k leads, 1M communications per install |
| **Security** | OWASP Top 10 compliant; passwords hashed (argon2id) |
| **Auditability** | All Super Admin and Admin actions logged |
| **Backup** | Daily DB backups, 30-day retention (see §31) |
| **Availability** | 99.5% uptime target |
| **Accessibility** | WCAG 2.1 AA on core views |
| **Localization** | i18n-ready (timezone, currency, date formats); Arabic RTL support ready |
| **Browser support** | Latest 2 versions of Chrome, Firefox, Safari, Edge |
| **Mobile** | Responsive down to 375 px wide |
| **Observability** | Structured logs (JSON), metrics endpoints |

---

## 25. User Stories & Acceptance Criteria

### 25.1 Sample User Stories

| # | As a… | I want to… | So that… |
|---|---|---|---|
| US-01 | Sales Rep | See only my leads on the Kanban board | I can focus on my pipeline |
| US-02 | Sales Rep | Log a call with notes | My manager can see what happened |
| US-03 | Sales Rep | Get a reminder when a follow-up is due | I never miss a callback |
| US-04 | Sales Manager | View my team’s pipeline value by stage | I can forecast this quarter |
| US-05 | Sales Manager | Set a monthly target for a rep | Accountability is clear |
| US-06 | Admin | Configure a new pipeline stage | The CRM matches our sales process |
| US-07 | Admin | Reassign a lead to another rep | Deals don’t get stuck when someone is on leave |
| US-08 | Analyst | View win-rate trend over 6 months | I can report to leadership |
| US-09 | Super Admin | Impersonate a user to debug an issue | Support is fast and accurate |
| US-10 | Super Admin | Review all audit logs | I can investigate security incidents |

### 25.2 Acceptance Criteria (Gherkin)

**US-01 — Rep sees only own leads**
```gherkin
Feature: Lead visibility scoping
  Scenario: Rep lists leads
    Given I am logged in as a Sales Representative
    And 20 leads belong to me
    And 30 leads belong to other reps
    When I request GET /api/v1/leads
    Then I receive exactly 20 leads
    And every returned lead has owner_id equal to my user id

  Scenario: Rep attempts to view another rep's lead
    Given a lead owned by another rep exists with id 999
    When I request GET /api/v1/leads/999
    Then the response status is 404
```

**US-03 — Task reminder**
```gherkin
Feature: Task due reminders
  Scenario: Reminder sent 60 minutes before due time
    Given a task assigned to me due at "2025-01-15 15:00"
    When the scheduler runs at "2025-01-15 14:00"
    Then a TaskDue notification is queued for me
    And the notification is delivered via mail and database
```

**US-07 — Lead reassignment**
```gherkin
Feature: Lead reassignment
  Scenario: Admin reassigns a lead
    Given a lead owned by "rep-a"
    And I am logged in as Admin
    When I POST /api/v1/leads/{id}/reassign with new_owner "rep-b"
    Then the lead.owner_id becomes "rep-b"
    And an activity entry "lead.reassigned" is created
    And rep-b receives a LeadAssigned notification

  Scenario: Sales Rep attempts reassignment
    Given I am logged in as a Sales Representative
    When I POST /api/v1/leads/{id}/reassign
    Then the response status is 403
```

**US-09 — Super Admin impersonation**
```gherkin
Feature: Super Admin impersonation
  Scenario: Super Admin impersonates a user
    Given I am logged in as Super Admin with 2FA verified
    When I POST /api/v1/super/users/{id}/impersonate
    Then I receive a short-lived signed token (TTL 15 minutes)
    And an activity entry "user.impersonated" is created
    And the target user receives an email notifying them

  Scenario: Impersonation token expiry
    Given an impersonation token issued 16 minutes ago
    When I make a request using the token
    Then the response status is 401
```

---

## 26. UI / UX Outline

### 26.1 Screens
- Login / 2FA
- Dashboard (role-scoped)
- Leads List
- Lead Detail (timeline, tasks, stage history)
- Pipeline Kanban
- Tasks (My / Team / All)
- Targets (list + progress)
- Analytics (charts, filters, export)
- Communications Timeline
- Admin: Users, Roles, Pipelines, Sources, Templates
- Super Admin: System Settings, Audit Log, Impersonation

### 26.2 Frontend Implementation

- **Blade layouts:** `layouts/app.blade.php` (auth), `layouts/guest.blade.php` (login).
- **Tailwind components:** badges, cards, tables, modals, dropdowns, toasts — as Blade partials under `resources/views/components/`.
- **Kanban:** server renders columns + cards; `kanban.js` (SortableJS) enables drag-and-drop; on drop, POST to `/leads/{id}/stage`.
- **Charts:** server passes aggregates as JSON; `charts.js` (Chart.js) renders.
- **Modals:** Blade `@include` + small JS for open/close (no library).
- **Notifications bell:** polling `/api/v1/notifications` every 60 s; optional SSE in v1.1.
- **Forms:** server-side validation with error display via Blade `@error`; optional fetch-based async submit for inline UX.

### 26.3 UX Rules
- Kanban drag-and-drop only for permitted users (server-side re-checked).
- Empty states guide the user to the next action.
- Analytics always show the current date range.
- Read-only views clearly marked for Analysts.
- Super Admin panel visually distinguished (accent color, banner).
- All destructive actions require confirmation.
- All forms show field-level validation inline.

### 26.4 Accessibility
- WCAG 2.1 AA on core screens.
- Visible focus states.
- ARIA labels on icon buttons.
- Contrast ratio ≥ 4.5:1.
- Screen-reader tested.
- Kanban board has keyboard alternative (dropdown on card).

### 26.5 Responsive
- Desktop-first, fully usable on tablets.
- Mobile: read + quick actions (log call, complete task); Kanban in list mode.

---

## 27. Onboarding Flow

### 27.1 Admin Onboarding
1. Sign up → verify email.
2. Guided setup wizard:
   - Company name, timezone, currency
   - Invite initial team (emails)
   - Choose default pipeline template
   - Set lead sources
   - Set first monthly targets (optional)
3. Land on empty dashboard with "Create first lead" CTA.

### 27.2 Rep Onboarding
1. Receive invite email → set password.
2. Optional 2FA setup.
3. On first login: brief product tour (5 steps).
4. Land on "My Tasks" with empty state and "Create first lead" CTA.

### 27.3 Sample Data
A "Load sample data" button (Admin only) seeds:
- 1 pipeline, 6 stages
- 3 lead sources
- 20 demo leads
- 10 communications
- 5 tasks
- 1 monthly target per rep

Sample data flagged with `is_demo=true`; purged with one click.

---

## 28. Testing Strategy

### 28.1 Test Pyramid

| Layer | Tool | Coverage Target |
|---|---|---|
| Unit tests | PHPUnit | Services, actions, enums |
| Feature tests | PHPUnit + Laravel | Endpoints, policies, notifications |
| Browser tests | Pest + Dusk or Playwright | Kanban drag, critical flows |
| Load tests | k6 / JMeter | 500 concurrent users |
| Security tests | OWASP ZAP baseline | OWASP Top 10 |

### 28.2 Critical Test Cases
- Every permission boundary (allowed + denied) for each role.
- Super Admin bypass works.
- Impersonation token expires.
- Scheduled commands dispatch expected jobs.
- Notifications sent on correct triggers.
- Dashboard APIs return correct aggregates.
- Scoping: rep cannot see others' leads; manager sees team; admin sees all.
- Soft delete + restore + permanent delete chain.
- Rate limiting triggers at threshold.
- CSV import handles malformed rows gracefully.
- **SQLite-specific:** transactions roll back cleanly; WAL survives concurrent read/write.

### 28.3 Test Database
- SQLite in-memory for feature tests: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`.
- Each test runs `RefreshDatabase` for isolation.
- Factories used everywhere (no hardcoded IDs).

### 28.4 CI Test Gate
- All tests must pass before merge.
- Min 80% coverage on Domain layer.
- Static analysis (Larastan / PHPStan level 8).
- Code style enforced with Pint.

---

## 29. CI/CD & Deployment

### 29.1 Pipeline

```
Push → Lint (Pint) → Static Analysis (PHPStan) → Unit/Feature Tests
     → Build Assets (Vite) → Deploy (staging)
     → Smoke Tests → Manual Approval → Deploy (production)
```

### 29.2 Environments
- **Local** — Laravel Sail or `php artisan serve` + SQLite file + Mailpit
- **Staging** — mirror of production, seeded demo data
- **Production** — real data, strict access

### 29.3 Server Requirements (Production)
- PHP 8.3+ with extensions: `pdo_sqlite`, `mbstring`, `openssl`, `xml`, `ctype`, `json`, `bcmath`
- Nginx or Apache with `php-fpm`
- SQLite file on fast local disk (not NFS)
- Cron: `* * * * * php artisan schedule:run`
- Supervisor: 1 queue worker (`php artisan queue:work`)
- Storage: local disk (`storage/app/`); S3 optional

### 29.4 Deploy Steps
```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan schedule:clear-cache
npm ci && npm run build
```

### 29.5 Deploy Safety
- Zero-downtime via symlink swap (Envoyer pattern) or atomic reload
- **SQLite backup before migrate:** `sqlite3 database.sqlite ".backup 'database.sqlite.bak-$(date +%s)'"`
- Backward-compatible migrations only
- Rollback < 5 min

---

## 30. Monitoring, Alerting & Logging

### 30.1 Metrics
- Request rate, error rate, latency (per endpoint)
- Queue depth and failed jobs
- Scheduled command execution (success/fail)
- SQLite file size + WAL file size
- Slow queries
- Cache hit ratio
- Login failures spike
- Webhook delivery failures

### 30.2 Alerting

| Alert | Threshold | Channel |
|---|---|---|
| 5xx error rate | > 1% for 5 min | Slack + email |
| Queue backlog | > 500 jobs for 10 min | Slack |
| Failed scheduled command | any failure | Slack |
| Disk usage | > 80% | Slack + email |
| SQLite WAL file | > 500 MB | Slack |
| Login brute-force | > 100 failed / 5 min / IP | Slack + email |
| Webhook failure | > 50% over 15 min | Slack |

### 30.3 Logging
- Structured JSON logs (`JsonFormatter`).
- Levels: `debug` (local), `info` (prod), `warning`, `error`, `critical`.
- Correlation ID (`request_id`) in all logs + API responses.
- PII masked in logs.
- Retention: 30 days hot, 1 year cold.

---

## 31. Backup & Disaster Recovery (SQLite)

### 31.1 Backup Policy

SQLite backup is **file-based** — the safest method is `.backup` (or `VACUUM INTO`), never a raw file copy while the app is running (risk of torn writes).

**Scheduled command `crm:backup-database`:**

```php
class BackupDatabase extends Command
{
    protected $signature = 'crm:backup-database';

    public function handle(): int
    {
        $source = config('database.connections.sqlite.database');
        $target = storage_path('backups/crm-'.now()->format('Ymd-His').'.sqlite');

        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }

        // Safe hot backup using SQLite's .backup command
        $pdo = new \PDO('sqlite:'.$source);
        $pdo->exec("VACUUM INTO '{$target}'");

        // Optional: gzip + upload to S3
        $this->info("Backup created: {$target}");
        return self::SUCCESS;
    }
}
```

Schedule daily at 02:00 UTC:
```php
Schedule::command('crm:backup-database')->dailyAt('02:00');
```

**Retention:** 30 daily backups + 4 weekly + 12 monthly, rotated by a `crm:prune-backups` command.

**Offsite:** `rclone` or AWS CLI sync to S3 (different region).

### 31.2 DR Targets
- **RPO:** ≤ 24 hours (or ≤ 15 min with WAL streaming via Litestream — recommended for production)
- **RTO:** ≤ 4 hours

### 31.3 Litestream (Optional, Recommended)
For continuous SQLite replication to S3:
```yaml
# /etc/litestream.yml
dbs:
  - path: /var/www/crm/database/database.sqlite
    replicas:
      - url: s3://my-crm-backups/database.sqlite
```

### 31.4 Restore Drill
- Quarterly restore test into staging.
- Documented runbook: stop app → copy backup → start app → verify.
- Post-drill report.

---

## 32. Data Retention & GDPR

### 32.1 Retention Policy

| Data | Retention | Notes |
|---|---|---|
| Leads (soft-deleted) | 90 days | Then purged unless legal hold |
| Communications | 2 years from last contact | Configurable |
| Audit logs | 2 years | Immutable |
| Auth logs | 6 months | — |
| Backups | 30 days | — |
| Failed jobs | 7 days | — |
| Session tokens | Configurable (2h–7d) | Revoked on logout |

### 32.2 GDPR Rights
- **Right to access:** export all data per user.
- **Right to erasure:** Super Admin-initiated purge.
- **Right to rectification:** user can update own profile.
- **Right to portability:** CSV export.
- **Processing records:** `activities` provides audit trail.

---

## 33. Dependencies & Environment Variables

### 33.1 Composer Packages

```
laravel/framework                ^13.0
laravel/fortify                  ^1.24
laravel/sanctum                  ^4.0
spatie/laravel-permission        ^6.0
spatie/laravel-query-builder     ^6.0
maatwebsite/excel                ^3.1
barryvdh/laravel-dompdf          ^3.0   (optional PDF exports)
```

Dev:
```
laravel/pint
larastan/larastan
phpunit/phpunit
laravel/dusk                     (optional)
```

### 33.2 npm Packages

```
vite
tailwindcss
@tailwindcss/forms
alpinejs                         (optional, tiny UI helpers)
sortablejs                       (Kanban drag-and-drop)
chart.js                         (analytics)
fullcalendar                     (task calendar)
```

### 33.3 `.env`

```env
APP_NAME=CRM
APP_ENV=production
APP_KEY=base64:...
APP_URL=https://crm.example.com
APP_TIMEZONE=UTC

# SQLite
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/crm/database/database.sqlite
DB_FOREIGN_KEYS=true

# No Redis — use database drivers
CACHE_STORE=database
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no-reply@crm.example.com

# Auth
SANCTUM_STATEFUL_DOMAINS=crm.example.com
SUPER_ADMIN_SESSION_LIFETIME=30

# Files
FILESYSTEM_DISK=local

# Super Admin seed (used once)
SUPER_ADMIN_EMAIL=superadmin@example.com
SUPER_ADMIN_PASSWORD=
SEED_SUPER_ADMIN=false
SEED_SUPER_ADMIN_SHOW_PASSWORD=false

# Security
WEBHOOK_SECRET=...
HIBP_API_KEY=
```

### 33.4 Project Structure (Excerpt)

```
app/
├── Console/Commands/
│   ├── FlagOverdueTasks.php
│   ├── SendTaskReminders.php
│   ├── FlagStalledLeads.php
│   ├── RecalculateTargets.php
│   ├── BackupDatabase.php
│   ├── PurgeSoftDeleted.php
│   └── CreateSuperAdmin.php
├── Enums/
│   ├── LeadStatus.php
│   ├── TaskStatus.php
│   ├── TaskPriority.php
│   ├── CommunicationType.php
│   ├── TargetType.php
│   ├── TargetPeriod.php
│   └── UserStatus.php
├── Models/
│   ├── User.php
│   ├── Team.php
│   ├── Lead.php
│   ├── Pipeline.php
│   ├── PipelineStage.php
│   ├── Communication.php
│   ├── Task.php
│   ├── Target.php
│   └── Activity.php
├── Policies/
│   ├── LeadPolicy.php
│   ├── CommunicationPolicy.php
│   ├── TaskPolicy.php
│   └── TargetPolicy.php
├── Notifications/
│   ├── TaskDue.php
│   ├── TaskOverdue.php
│   ├── LeadAssigned.php
│   └── TargetReached.php
├── Http/
│   ├── Controllers/
│   │   ├── Web/
│   │   │   ├── DashboardController.php
│   │   │   ├── LeadController.php
│   │   │   ├── TaskController.php
│   │   │   └── Admin/
│   │   └── Api/V1/
│   │       ├── LeadController.php
│   │       ├── TaskController.php
│   │       └── DashboardController.php
│   ├── Requests/
│   └── Resources/
└── Providers/
    └── AppServiceProvider.php

database/
├── factories/
├── migrations/
├── seeders/
└── database.sqlite

resources/
├── css/app.css
├── js/
│   ├── app.js
│   ├── kanban.js
│   ├── charts.js
│   └── calendar.js
└── views/
    ├── layouts/
    ├── components/
    ├── leads/
    ├── tasks/
    └── dashboard/

routes/
├── web.php
├── api.php
└── console.php
```

---

## 34. Release Plan & Roadmap

### 34.1 Phase 1 — Foundation (Weeks 1–3)
- Migrations for all core tables
- Enums for all status/type fields
- Factories for all models
- Seeders (roles, pipeline, sources)
- Auth + 2FA for Super Admin
- Roles/permissions + `Gate::before` bypass
- Basic lead CRUD + list view (Blade + Tailwind)

### 34.2 Phase 2 — Pipeline & Communication (Weeks 4–6)
- Kanban board (SortableJS)
- Stage history + communication log
- Lead assignment/reassignment
- File attachments

### 34.3 Phase 3 — Workflow (Weeks 7–9)
- Tasks + reminders
- Scheduled commands (database queue)
- Automatic task triggers
- Calendar view

### 34.4 Phase 4 — Analytics (Weeks 10–12)
- Targets (CRUD + progress)
- Dashboard APIs + database cache
- Analytics screens (Chart.js)
- Weekly digest

### 34.5 Phase 5 — Hardening (Week 13+)
- Audit logs, impersonation
- Webhooks
- Backup commands + Litestream
- Performance tuning, tests, documentation
- UAT and launch

### 34.6 v1.1 Roadmap
- Custom fields per pipeline
- Email inbox sync (Gmail/Outlook)
- Recurring tasks
- @-mentions
- SSE notifications

### 34.7 v2 Roadmap
- Multi-tenant SaaS mode
- Customer-facing portal
- Mobile apps (iOS, Android)
- AI lead scoring
- Advanced forecasting

---

## 35. Acceptance Criteria (Definition of Done)

A feature is **Done** when:
- All functional requirements (§7) are implemented and tested.
- Migrations, factories, and seeders exist for all new models.
- Enums used instead of raw strings everywhere.
- Role + data-scope checks verified on every endpoint and UI action.
- Super Admin bypass tested and audit-logged.
- Scheduled commands run on schedule and are monitored.
- Notifications dispatched via queue with retry.
- Dashboard APIs respond within the performance budget.
- Unit + feature tests cover permission boundaries.
- Accessibility (WCAG AA) verified on new UI.
- Documentation updated (API + user guide).
- No P1/P2 bugs open.

---

## 36. Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| SQLite single-writer bottleneck under load | Delayed writes | WAL mode, short transactions, batch inserts, single queue worker |
| Over-permissive Super Admin use | Security breach | 2FA, audit logs, seed-only creation, last-admin protection |
| Notification fatigue | Users ignore reminders | Configurable frequency, priority levels, digest mode |
| Analytics slow on large data | Poor UX | Database cache, pre-aggregation, indexed queries |
| Scope-creep (inbox sync, telephony) | Delays v1 | Strict in/out of scope (§5) |
| Role confusion | Access bugs | Single permission matrix (§6.3) and tests |
| Webhook failures | Data sync loss | Retry with backoff + delivery log |
| SQLite file corruption on crash | Data loss | WAL mode, Litestream replication, daily backups |
| Backup file stolen | Privacy breach | Encrypt backups, restrict file permissions |
| Impersonation misuse | Privacy breach | Short TTL tokens, audit logs, user notification |

---

## 37. Open Questions

1. Multi-tenant (SaaS) mode in v2 — confirmed? If yes, SQLite becomes insufficient; migrate to PostgreSQL.
2. Notification channels for v1 — mail + database only, or add SSE broadcast?
3. Approval workflow for deal stage changes — required for Manager role?
4. Default inactivity threshold for stalled leads — 14 days acceptable?
5. Target periods — monthly and quarterly only, or add annual?
6. Custom fields — needed at launch or v1.1?
7. Litestream replication — required at launch or v1.1?
8. Feature flags — build a `settings` table or use env vars?

---

## 38. Revision History

| Version | Date | Author | Change |
|---|---|---|---|
| 1.0 | 2025-01-10 | Product | Initial PRD |
| 1.1 | 2025-01-15 | Product / Eng | Added glossary, personas, state machines, schemas, API spec, notifications, audit, security, caching, rate limiting, errors, webhooks, import/export, onboarding, testing, CI/CD, monitoring, backup, GDPR, dependencies, roadmap |
| 1.2 | 2025-01-15 | Product / Eng | Locked stack to **Laravel 13 + SQLite 3 + Blade + Tailwind + vanilla JS**. Added **§10 PHP Enums**, **§11 Migrations**, **§12 Factories**, **§13 Seeders** as first-class sections. Converted schemas to SQLite syntax (CHECK constraints, WAL mode). Replaced Redis with **database** queue/cache/session. Added §31 SQLite-specific backup & DR (`.backup`, `VACUUM INTO`, Litestream). Updated §33 dependencies, env vars, and project structure |

---

**End of PRD**
