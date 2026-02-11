# API & UI Endpoints — Bionic Backend

> NOTE: Authentication is session/JWT based. Where routes are commented in `app/Config/Routes.php`, endpoints are listed but marked **(route commented)**.

| Method | Route          |   Controller::Method | Auth | Description                        |
| ------ | -------------- | -------------------: | ---: | ---------------------------------- |
| GET    | `/auth/login`  |  `Auth::login` (GET) |   no | Show login page                    |
| POST   | `/auth/login`  | `Auth::login` (POST) |   no | Authenticate and issue JWT session |
| GET    | `/auth/logout` |       `Auth::logout` |  yes | Destroy session and logout         |

## Admin

| GET | `/admin` | `Admin\Dashboard::index` | admin | Admin dashboard view |
| GET | `/admin/get_stats` | `Admin\Dashboard::get_stats` | admin | Fetch dashboard stats (AJAX) |
| GET | `/admin/get_room_visits` | `Admin\Dashboard::get_room_visits` | admin | Fetch room visits data (AJAX) |

### Admin — Manage (resource presenters)

Routes registered with `$routes->presenter('user|room|item|action|role|permission|user_role|role_permission')` under `/admin/manage`.
Standard presenter routes (example for `user`):

- GET `/admin/manage/user` -> `User::index` (list view)
- GET `/admin/manage/user/new` -> `User::new` (new form)
- POST `/admin/manage/user` -> `User::create` (create resource)
- GET `/admin/manage/user/{id}` -> `User::show` (show)
- GET `/admin/manage/user/{id}/edit` -> `User::edit` (edit form)
- PUT `/admin/manage/user/{id}` -> `User::update` (update)
- GET `/admin/manage/user/{id}/remove` -> `User::remove` (confirm remove)
- DELETE `/admin/manage/user/{id}` -> `User::delete` (delete)

Custom/ajax endpoints implemented in controllers (common patterns):
| Method | Route | Controller::Method | Auth | Description |
|---|---|---:|---:|---|
| POST | `/admin/manage/user/modal` | `Admin\User::modal` | admin | Returns modal HTML for add/edit/delete |
| POST | `/admin/manage/user/get_datatable` | `Admin\User::get_datatable` | admin | Datatable data for users (AJAX) |
| POST | `/admin/manage/user/add` | `Admin\User::add` | admin | Create user (AJAX) |
| POST | `/admin/manage/user/update` | `Admin\User::update` | admin | Update user (AJAX) |
| POST | `/admin/manage/user/delete` | `Admin\User::delete` | admin | Delete user (AJAX) |

(Repeat patterns exist for `room`, `item`, `action`, `role`, `permission`, `user_role`, `role_permission` controllers.)

### Admin — Task Management (note: routes commented in Routes.php)

These endpoints exist in `app/Controllers/Admin/Task.php` but their route group is commented out.
| Method | Route | Controller::Method | Auth | Description |
|---|---|---:|---:|---|
| GET | `/admin/manage/task` | `Admin\Task::index` | admin | Task management view |
| POST | `/admin/manage/task/get_datatable/location` | `Admin\Task::get_datatable_location` | admin | Datatable for locations |
| POST | `/admin/manage/task/modal/location` | `Admin\Task::modal_location` | admin | Modal content for location |
| POST | `/admin/manage/task/add/location` | `Admin\Task::add_location` | admin | Add location (AJAX) |
| PUT | `/admin/manage/task/edit/location` | `Admin\Task::update_location` | admin | Update location (AJAX) |
| DELETE | `/admin/manage/task/delete/location` | `Admin\Task::delete_location` | admin | Delete location (AJAX) |
| POST | `/admin/manage/task/get_datatable/item` | `Admin\Task::get_datatable_item` | admin | Datatable for items |
| POST | `/admin/manage/task/modal/item` | `Admin\Task::modal_item` | admin | Modal content for item |
| POST | `/admin/manage/task/add/item` | `Admin\Task::add_item` | admin | Add item (AJAX) |
| PUT | `/admin/manage/task/edit/item` | `Admin\Task::update_item` | admin | Update item (AJAX) |
| DELETE | `/admin/manage/task/delete/item` | `Admin\Task::delete_item` | admin | Delete item (AJAX) |
| POST | `/admin/manage/task/get_datatable/action` | `Admin\Task::get_datatable_action` | admin | Datatable for actions |
| POST | `/admin/manage/task/modal/action` | `Admin\Task::modal_action` | admin | Modal content for action |
| POST | `/admin/manage/task/add/action` | `Admin\Task::add_action` | admin | Add action (AJAX) |
| PUT | `/admin/manage/task/edit/action` | `Admin\Task::update_action` | admin | Update action (AJAX) |
| DELETE | `/admin/manage/task/delete/action` | `Admin\Task::delete_action` | admin | Delete action (AJAX) |

## Operator (routes commented)

| GET | `/operator` | `Operator::index` | operator | Operator dashboard view |
| GET | `/operator/scan/{location_id}` | `Operator::scan` | operator | Scan/QR page for a location |
| GET | `/operator/revisi` | `Operator::revisi` | operator | Revision list |
| POST | `/operator/modal` | `Operator::modal` | operator | Modal content (actions list) |
| POST | `/operator/add` | `Operator::add_submission` | operator | Store task submissions (AJAX) |
| POST | `/operator/increment_visit/{location_id}` | `Operator::increment_visit` | operator | Increment visit counter (AJAX) |
| DELETE | `/operator/cancel/{action_id}` | `Operator::cancel_submission` | operator | Cancel a task submission |
| GET | `/operator/get_revision_count` | `Operator::get_revision_count` | operator | Get count of locations with revisions (AJAX) |

## Verifikator (routes commented)

| GET | `/verifikator` | `Verifikator::index` | verifikator | Verifikator dashboard |
| GET | `/verifikator/verified` | `Verifikator::verified` | verifikator | View verified tasks |
| POST | `/verifikator/get_datatable` | `Verifikator::get_datatable` | verifikator | Datatable of pending tasks (AJAX) |
| POST | `/verifikator/get_verified_datatable` | `Verifikator::get_verified_datatable` | verifikator | Datatable of verified tasks (AJAX) |
| POST | `/verifikator/modal` | `Verifikator::modal` | verifikator | Get details modal for a task |
| POST | `/verifikator/update` | `Verifikator::update` | verifikator | Verify or request revision for a task (AJAX) |

---

If you want, I can:

- Generate a Postman/OpenAPI spec from these endpoints ✅
- Add example request/response payloads for the busiest endpoints ✅

Which one next? (reply: `openapi` / `examples` / `tidak perlu`)
