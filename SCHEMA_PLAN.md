# Scorz — Multi-Role School Management Auth Schema

## Overview

A normalized, RBAC-compliant MySQL schema supporting three distinct login entry points, multi-school/multi-role users, and pupils as a separate authentication entity.

---

## Architecture Decisions

| Decision | Choice | Reason |
|---|---|---|
| Pupils | Separate `pupils` table | No email/password; different auth contract; avoids nullable columns on `users` |
| PIN | Separate `user_pins` table | One-per-user enforced at DB level; preserves existing `users` migration |
| Super Admin role | `user_roles` global pivot | Super Admins have no school context; NULL `school_id` would be semantically wrong |
| School + role context | `user_school_roles` 3-way pivot | A user can be School Admin at School A and Teacher at School B simultaneously |
| Auth guards | 3 guards: `superadmin`, `web`, `pupil` | Independent session states; no cross-guard auth collisions |

---

## Entity Relationship Diagram

```
users (1) ────────────────── (1) user_pins
users (1) ────────────────── (*) user_roles          ──── (*) roles   [global — Super Admin only]
users (1) ────────────────── (*) user_school_roles   ──── (*) schools [school-scoped]
                                        └──────────────── (*) roles   [school_admin, teacher]
schools (1) ─────────────── (*) pupils
```

---

## Tables

### Existing (do not modify)
| Table | Description |
|---|---|
| `users` | Core user accounts (id, name, email, password) |
| `password_reset_tokens` | Password reset flow |
| `sessions` | Laravel session store |
| `cache` / `cache_locks` | Application cache |
| `jobs` / `job_batches` / `failed_jobs` | Queue system |

---

### New Migrations (in dependency order)

#### 1. ALTER `users` — add auth fields
**File:** `2026_04_04_000001_add_auth_fields_to_users_table.php`

| Column | Type | Default | Purpose |
|---|---|---|---|
| `is_active` | `TINYINT(1)` | `1` | Soft-disable an account without deletion |
| `last_login_at` | `TIMESTAMP` | `NULL` | Audit trail |

**Index added:** `idx_users_email_active (email, is_active)` — single-pass login query.

```sql
ALTER TABLE `users`
    ADD COLUMN `is_active`     TINYINT(1) NOT NULL DEFAULT 1   AFTER `email_verified_at`,
    ADD COLUMN `last_login_at` TIMESTAMP  NULL     DEFAULT NULL AFTER `is_active`,
    ADD INDEX  `idx_users_email_active` (`email`, `is_active`);
```

---

#### 2. `schools`
**File:** `2026_04_04_000002_create_schools_table.php`

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `name` | `VARCHAR(255)` | Full school name |
| `slug` | `VARCHAR(100)` UNIQUE | URL-safe identifier |
| `code` | `VARCHAR(20)` UNIQUE | Short code e.g. `SMSB01` |
| `address` | `TEXT` nullable | School address |
| `is_active` | `TINYINT(1)` | Enable/disable school |
| `created_at` / `updated_at` | `TIMESTAMP` | Laravel timestamps |

```sql
CREATE TABLE `schools` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `slug`       VARCHAR(100)    NOT NULL,
    `code`       VARCHAR(20)     NOT NULL COMMENT 'Short code e.g. SMSB01',
    `address`    TEXT            NULL,
    `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_schools_slug`   (`slug`),
    UNIQUE KEY `uq_schools_code`   (`code`),
    INDEX       `idx_schools_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 3. `roles`
**File:** `2026_04_04_000003_create_roles_table.php`

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `name` | `VARCHAR(100)` | Display name e.g. `School Admin` |
| `slug` | `VARCHAR(100)` UNIQUE | Machine name e.g. `school_admin` |
| `description` | `VARCHAR(255)` nullable | Optional description |
| `created_at` / `updated_at` | `TIMESTAMP` | Laravel timestamps |

**Seed data:** `super_admin`, `school_admin`, `teacher`

```sql
CREATE TABLE `roles` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100)    NOT NULL COMMENT 'Display: School Admin',
    `slug`        VARCHAR(100)    NOT NULL COMMENT 'Machine: school_admin',
    `description` VARCHAR(255)    NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 4. `user_pins`
**File:** `2026_04_04_000004_create_user_pins_table.php`

> One PIN per user — enforced by `UNIQUE KEY` on `user_id` at the database level.

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `user_id` | `BIGINT UNSIGNED` UNIQUE FK | References `users.id` |
| `pin` | `VARCHAR(255)` | bcrypt-hashed 5-digit PIN |
| `created_at` / `updated_at` | `TIMESTAMP` | Laravel timestamps |

```sql
CREATE TABLE `user_pins` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `pin`        VARCHAR(255)    NOT NULL COMMENT 'bcrypt hash of 5-digit PIN',
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_pins_user_id` (`user_id`),

    CONSTRAINT `fk_user_pins_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 5. `user_roles` — Global role assignments (Super Admin only)
**File:** `2026_04_04_000005_create_user_roles_table.php`

> Used exclusively for Super Admin. School-scoped roles go in `user_school_roles`.

| Column | Type | Description |
|---|---|---|
| `user_id` | `BIGINT UNSIGNED` PK part | References `users.id` |
| `role_id` | `BIGINT UNSIGNED` PK part | References `roles.id` |
| `created_at` | `TIMESTAMP` | Assignment timestamp |

```sql
CREATE TABLE `user_roles` (
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `role_id`    BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`user_id`, `role_id`),

    CONSTRAINT `fk_ur_user_id`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_role_id`
        FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 6. `user_school_roles` — School-scoped role assignments
**File:** `2026_04_04_000006_create_user_school_roles_table.php`

> Three-way pivot. A user can hold multiple roles at the same school (e.g., both `school_admin` and `teacher`).

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `user_id` | `BIGINT UNSIGNED` FK | References `users.id` |
| `school_id` | `BIGINT UNSIGNED` FK | References `schools.id` |
| `role_id` | `BIGINT UNSIGNED` FK | References `roles.id` |
| `is_active` | `TINYINT(1)` | Enable/disable this assignment |
| `assigned_at` | `TIMESTAMP` nullable | When the role was granted (audit) |
| `created_at` / `updated_at` | `TIMESTAMP` | Laravel timestamps |

**Unique constraint:** `(user_id, school_id, role_id)` — prevents duplicate assignments.

```sql
CREATE TABLE `user_school_roles` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `school_id`   BIGINT UNSIGNED NOT NULL,
    `role_id`     BIGINT UNSIGNED NOT NULL,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `assigned_at` TIMESTAMP       NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_school_role` (`user_id`, `school_id`, `role_id`),
    INDEX `idx_usr_user_id`   (`user_id`),
    INDEX `idx_usr_school_id` (`school_id`),
    INDEX `idx_usr_role_id`   (`role_id`),
    INDEX `idx_usr_active`    (`is_active`),

    CONSTRAINT `fk_usr_user_id`
        FOREIGN KEY (`user_id`)   REFERENCES `users`   (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `fk_usr_school_id`
        FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `fk_usr_role_id`
        FOREIGN KEY (`role_id`)   REFERENCES `roles`   (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 7. `pupils`
**File:** `2026_04_04_000007_create_pupils_table.php`

> Completely separate from `users`. Pupils authenticate with `pupil_id` + PIN only — no email, no password.
> `pupil_id` is unique **per school** (two schools can both have `2024-001`).

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `school_id` | `BIGINT UNSIGNED` FK | References `schools.id` |
| `pupil_id` | `VARCHAR(50)` | Human-readable ID e.g. `2024-001` |
| `name` | `VARCHAR(255)` | Full name |
| `date_of_birth` | `DATE` nullable | Optional |
| `year_group` | `VARCHAR(20)` nullable | e.g. `Year 7` |
| `pin` | `VARCHAR(255)` | bcrypt-hashed 5-digit PIN |
| `is_active` | `TINYINT(1)` | Enable/disable pupil |
| `last_login_at` | `TIMESTAMP` nullable | Audit trail |
| `created_at` / `updated_at` | `TIMESTAMP` | Laravel timestamps |

```sql
CREATE TABLE `pupils` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `school_id`     BIGINT UNSIGNED NOT NULL,
    `pupil_id`      VARCHAR(50)     NOT NULL COMMENT 'Human-readable e.g. 2024-001',
    `name`          VARCHAR(255)    NOT NULL,
    `date_of_birth` DATE            NULL,
    `year_group`    VARCHAR(20)     NULL,
    `pin`           VARCHAR(255)    NOT NULL COMMENT 'bcrypt hash of 5-digit PIN',
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
    `last_login_at` TIMESTAMP       NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pupils_pupil_id_school` (`pupil_id`, `school_id`),
    INDEX `idx_pupils_pupil_id`  (`pupil_id`),
    INDEX `idx_pupils_school_id` (`school_id`),
    INDEX `idx_pupils_active`    (`is_active`),

    CONSTRAINT `fk_pupils_school_id`
        FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

#### 8. `login_attempts` — Rate limiting & audit
**File:** `2026_04_04_000008_create_login_attempts_table.php`

> Tracks all login attempts across all three guards. The `step` column distinguishes password failures from PIN failures.

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` PK | Auto-increment |
| `guard` | `ENUM` | `superadmin`, `web`, or `pupil` |
| `identifier` | `VARCHAR(255)` | Email (superadmin/web) or pupil_id (pupil) |
| `ip_address` | `VARCHAR(45)` | Supports IPv6 |
| `was_successful` | `TINYINT(1)` | `0` = failed, `1` = success |
| `step` | `TINYINT(1)` | `1` = password step, `2` = PIN step |
| `attempted_at` | `TIMESTAMP` | Auto set to current time |

```sql
CREATE TABLE `login_attempts` (
    `id`             BIGINT UNSIGNED                  NOT NULL AUTO_INCREMENT,
    `guard`          ENUM('superadmin','web','pupil') NOT NULL,
    `identifier`     VARCHAR(255)                     NOT NULL COMMENT 'email or pupil_id',
    `ip_address`     VARCHAR(45)                      NOT NULL,
    `was_successful` TINYINT(1)                       NOT NULL DEFAULT 0,
    `step`           TINYINT(1)                       NOT NULL DEFAULT 1 COMMENT '1=password, 2=PIN',
    `attempted_at`   TIMESTAMP                        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    INDEX `idx_la_identifier_guard` (`identifier`, `guard`),
    INDEX `idx_la_ip_attempted`     (`ip_address`, `attempted_at`),
    INDEX `idx_la_attempted_at`     (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## Index Strategy

| Table | Index | Purpose |
|---|---|---|
| `users` | `idx_users_email_active (email, is_active)` | Single-pass login query |
| `schools` | `uq_schools_slug`, `uq_schools_code` | URL routing + code lookup |
| `roles` | `uq_roles_slug` | Gate/policy lookups by slug |
| `user_pins` | `uq_user_pins_user_id` | One-row-per-user + O(1) lookup |
| `user_school_roles` | `uq_user_school_role (user_id, school_id, role_id)` | Duplicate prevention + join performance |
| `user_school_roles` | `idx_usr_school_id` | List all users in a school |
| `user_school_roles` | `idx_usr_role_id` | List all teachers globally |
| `user_roles` | PK `(user_id, role_id)` | Super Admin check in O(1) |
| `pupils` | `uq_pupils_pupil_id_school (pupil_id, school_id)` | Uniqueness + login lookup |
| `pupils` | `idx_pupils_pupil_id` | Login before school is resolved |
| `login_attempts` | `idx_la_identifier_guard` | Throttle query by identifier |
| `login_attempts` | `idx_la_ip_attempted` | IP-based rate limiting |

---

## Login Flows

### Flow 1: Super Admin — `/superadmin/login`

```
POST /superadmin/login  { email, password }
 └─ SELECT * FROM users WHERE email = ? AND is_active = 1
 └─ Hash::check($password, $user->password)
 └─ SELECT r.slug FROM roles r
      JOIN user_roles ur ON ur.role_id = r.id
      WHERE ur.user_id = ? AND r.slug = 'super_admin'
 └─ Auth::guard('superadmin')->login($user)
```

### Flow 2: School Admin / Teacher — `/login` (two-step)

```
STEP 1 — POST /login  { email, password }
 └─ SELECT * FROM users WHERE email = ? AND is_active = 1
 └─ Hash::check($password, $user->password)
 └─ Verify at least one active row in user_school_roles
 └─ session(['pending_pin_user_id' => $user->id])
 └─ Redirect → /login/pin

STEP 2 — POST /login/pin  { pin }
 └─ $userId = session('pending_pin_user_id')  // redirect if missing
 └─ SELECT * FROM user_pins WHERE user_id = ?
 └─ Hash::check($pin, $userPin->pin)
 └─ Auth::guard('web')->login($user)
 └─ session()->forget('pending_pin_user_id')
```

### Flow 3: Pupil — `/pupil/login`

```
POST /pupil/login  { pupil_id, pin }
 └─ SELECT * FROM pupils WHERE pupil_id = ? AND is_active = 1
    // If multiple rows (same pupil_id, different schools) → show school picker
 └─ Hash::check($pin, $pupil->pin)
 └─ Auth::guard('pupil')->login($pupil)
```

---

## auth.php Guard Configuration

```php
'guards' => [
    'web'        => ['driver' => 'session', 'provider' => 'users'],   // School Admin + Teacher
    'superadmin' => ['driver' => 'session', 'provider' => 'users'],   // Super Admin
    'pupil'      => ['driver' => 'session', 'provider' => 'pupils'],  // Pupil
],

'providers' => [
    'users'  => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    'pupils' => ['driver' => 'eloquent', 'model' => App\Models\Pupil::class],
],
```

---

## Migration File Order

| Order | File | Creates |
|---|---|---|
| 1 | `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` *(existing)* |
| 2 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` *(existing)* |
| 3 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` *(existing)* |
| 4 | `2026_04_04_000001_add_auth_fields_to_users_table.php` | ALTER `users` |
| 5 | `2026_04_04_000002_create_schools_table.php` | `schools` |
| 6 | `2026_04_04_000003_create_roles_table.php` | `roles` |
| 7 | `2026_04_04_000004_create_user_pins_table.php` | `user_pins` |
| 8 | `2026_04_04_000005_create_user_roles_table.php` | `user_roles` |
| 9 | `2026_04_04_000006_create_user_school_roles_table.php` | `user_school_roles` |
| 10 | `2026_04_04_000007_create_pupils_table.php` | `pupils` |
| 11 | `2026_04_04_000008_create_login_attempts_table.php` | `login_attempts` |

---

## Files to Create / Modify

| File | Action |
|---|---|
| `database/migrations/2026_04_04_000001_*.php` → `_000008_*.php` | Create (8 new files) |
| `app/Models/User.php` | Add `pin()`, `globalRoles()`, `schoolRoles()`, `isSuperAdmin()`, `hasRoleInSchool()` |
| `app/Models/Pupil.php` | Create — implements `Authenticatable` |
| `app/Models/Role.php` | Create |
| `app/Models/School.php` | Create |
| `app/Models/UserSchoolRole.php` | Create (pivot model) |
| `config/auth.php` | Add `superadmin` + `pupil` guards and `pupils` provider |
| `routes/web.php` | Add route groups for `/superadmin`, `/login/pin`, `/pupil` |
| `database/seeders/RoleSeeder.php` | Seed `super_admin`, `school_admin`, `teacher` |

---

## Verification Checklist

- [ ] `php artisan migrate:fresh` — all 11 migrations run without FK errors
- [ ] `php artisan db:seed --class=RoleSeeder` — 3 roles inserted
- [ ] Super Admin login: user + `user_roles` row → `POST /superadmin/login` authenticates
- [ ] Two-step login: user + `user_pins` + `user_school_roles` → step 1 sets session, step 2 clears it
- [ ] Pupil login: `pupils` row → `POST /pupil/login` with `pupil_id` + PIN authenticates
- [ ] `Auth::guard('superadmin')->check()` and `Auth::guard('web')->check()` are independent
- [ ] Delete a school → `user_school_roles` rows cascade delete
- [ ] Delete a role in use → `RESTRICT` blocks the operation
