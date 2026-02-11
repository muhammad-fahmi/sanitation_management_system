# Entity Relationship Diagram (Mermaid) — Bionic Backend

```mermaid
erDiagram
    USERS {
        int id PK
        varchar username
        varchar email
        varchar password
        int is_active
        datetime last_login_at
        datetime created_at
        datetime updated_at
    }

    ROLES {
        int id PK
        varchar name
        varchar slug
        text description
        datetime created_at
        datetime updated_at
    }

    PERMISSIONS {
        int id PK
        varchar name
        varchar slug
        text description
        datetime created_at
        datetime updated_at
    }

    USER_ROLES {
        int user_id FK
        int role_id FK
        datetime created_at
        datetime updated_at
    }

    ROLE_PERMISSIONS {
        int role_id FK
        int permission_id FK
        datetime created_at
        datetime updated_at
    }

    ROOMS {
        int id PK
        varchar name
        datetime created_at
        datetime updated_at
    }

    ITEMS {
        int id PK
        int room_id FK
        varchar name
        datetime created_at
        datetime updated_at
    }

    ACTIONS {
        int id PK
        int item_id FK
        varchar name
        datetime created_at
        datetime updated_at
    }

    TASK_SUBMISSIONS {
        int id PK
        varchar submission_code
        datetime date
        int room_id FK
        int visit_frequency
        text revision_message
        varchar status
        int submitted_by FK
        int verified_by FK
        datetime created_at
        datetime updated_at
        datetime deleted_at
    }

    TASK_SUBMISSION_ITEMS {
        int id PK
        int task_submission_id FK
        int item_id FK
        int cleaning_frequency
        datetime created_at
        datetime updated_at
        datetime deleted_at
    }

    TASK_SUBMISSION_ACTIONS {
        int id PK
        int task_submission_item_id FK
        int action_id FK
        int repetitions
        datetime created_at
        datetime updated_at
        datetime deleted_at
    }

    TASK_SUBMISSION_ATTACHMENTS {
        int id PK
        int task_submission_action_id FK
        text file_path
        datetime created_at
        datetime updated_at
        datetime deleted_at
    }

    TASK_SUBMISSION_HISTORIES {
        int id PK
        int task_submission_id FK
        int user_id
        varchar previous_status
        varchar new_status
        text remarks
        datetime created_at
    }

    USERS ||--o{ USER_ROLES : "has"
    ROLES ||--o{ USER_ROLES : "has"
    ROLES ||--o{ ROLE_PERMISSIONS : "has"
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : "has"

    ROOMS ||--o{ ITEMS : "contains"
    ITEMS ||--o{ ACTIONS : "has"

    ROOMS ||--o{ TASK_SUBMISSIONS : "location"
    TASK_SUBMISSIONS ||--o{ TASK_SUBMISSION_ITEMS : "includes"
    TASK_SUBMISSION_ITEMS ||--o{ TASK_SUBMISSION_ACTIONS : "includes"
    TASK_SUBMISSION_ACTIONS ||--o{ TASK_SUBMISSION_ATTACHMENTS : "has"
    TASK_SUBMISSIONS ||--o{ TASK_SUBMISSION_HISTORIES : "history"

    USERS ||--o{ TASK_SUBMISSIONS : "submitted_by"
    USERS ||--o{ TASK_SUBMISSIONS : "verified_by"
```

> Notes:
>
> - `status` on `task_submissions` stores values like `pending_review | revision_requested | approved | rejected` (see migration).
> - Many tables include `created_at`, `updated_at`, and some `deleted_at` for soft-deletes or tracking.
> - This diagram is based on migrations in `app/Database/Migrations`.
