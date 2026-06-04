# Test Plan

## User Stories

| # | User story |
|---|-----------|
| US-1 | As a visitor, I want to view published blog posts |
| US-2 | As a visitor, I want to open a single blog post by its URL |
| US-3 | As a user, I want to register an account |
| US-4 | As a user, I want to log in with my email and password |
| US-5 | As an admin, I want to create, edit and delete blog posts |
| US-6 | As an admin, I want pages to be protected so regular users can't access them |
| US-7 | As a visitor, I want to see my course grades and results |
| US-8 | As a visitor, I want to view the profile page with personal information |
| US-9 | As a visitor, I want to navigate the home, FAQ and commandmaker pages |

---

## Unit Tests

Unit tests test a single class in isolation. Dependencies (like the database) are replaced with fakes called **mocks**.

| Test | Class | What it verifies | User story |
|------|-------|-----------------|-----------|
| `test_deleted_at_is_null_by_default` | `Post` | A new Post has no deleted_at | US-5 |
| `test_can_set_title_and_slug` | `Post` | Title and slug can be assigned | US-5 |
| `test_status_can_be_published` | `Post` | Status can be set to published | US-1 |
| `test_status_can_be_draft` | `Post` | Status can be set to draft | US-5 |
| `test_can_set_content_and_preview` | `Post` | Content and preview can be assigned | US-5 |
| `test_default_role_is_user` | `User` | A new User gets role "user" by default | US-3 |
| `test_deleted_at_is_null_by_default` | `User` | A new User has no deleted_at | US-3 |
| `test_can_set_name` | `User` | First and last name can be assigned | US-3 |
| `test_can_set_email` | `User` | Email can be assigned | US-3 |
| `test_role_can_be_set_to_admin` | `User` | Role can be changed to admin | US-6 |
| `test_returns_forbidden_when_no_session` | `AdminMiddleware` | Returns 403 when not logged in | US-6 |
| `test_returns_null_when_user_is_admin` | `AdminMiddleware` | Allows access when role is admin | US-6 |
| `test_returns_forbidden_when_user_has_role_user` | `AdminMiddleware` | Returns 403 when role is user | US-6 |
| `test_index_returns_response` | `HomeController` | Home page returns a 200 response | US-9 |
| `test_faq_returns_response` | `HomeController` | FAQ page returns a 200 response | US-9 |
| `test_commandmaker_returns_response` | `HomeController` | Commandmaker page returns a 200 response | US-9 |
| `test_index_returns_response` | `BlogController` | Blog index returns a 200 response | US-1 |
| `test_show_returns_not_found_when_no_slug` | `BlogController` | Returns 404 when no slug is given | US-2 |
| `test_show_returns_view_when_slug_given` | `BlogController` | Returns 200 when a slug is given | US-2 |
| `test_manage_returns_response` | `BlogController` | Manage page returns a response | US-5 |
| `test_show_create_returns_response` | `BlogController` | Create page returns a response | US-5 |
| `test_show_edit_returns_not_found_when_post_missing` | `BlogController` | Returns 404 when post does not exist | US-5 |
| `test_show_edit_returns_view_when_post_found` | `BlogController` | Returns 200 when post exists | US-5 |
| `test_show_login_returns_response` | `UserController` | Login page returns a 200 response | US-4 |
| `test_show_register_returns_response` | `UserController` | Register page returns a 200 response | US-3 |
| `test_overview_returns_response` | `UserController` | Overview page returns a 200 response | US-3 |
| `test_register_redirects_when_email_is_invalid` | `UserController` | Redirects when email is not valid | US-3 |
| `test_register_redirects_when_password_too_short` | `UserController` | Redirects when password is shorter than 8 characters | US-3 |

---

## Integration Tests

Integration tests test the repository classes against a real (temporary) SQLite database. This verifies that database queries actually work correctly end-to-end.

| Test | Class | What it verifies | User story |
|------|-------|-----------------|-----------|
| `test_create_returns_user_with_id` | `UserRepository` | Creating a user assigns an ID | US-3 |
| `test_create_saves_correct_data` | `UserRepository` | User data is saved correctly | US-3 |
| `test_find_by_email_returns_user` | `UserRepository` | User can be found by email | US-4 |
| `test_find_by_email_returns_null_when_not_found` | `UserRepository` | Returns null for unknown email | US-4 |
| `test_find_by_id_returns_user` | `UserRepository` | User can be found by ID | US-4 |
| `test_find_by_id_returns_null_when_not_found` | `UserRepository` | Returns null for unknown ID | US-4 |
| `test_create_admin_user` | `UserRepository` | Admin role is saved correctly | US-6 |
| `test_password_is_stored_hashed` | `UserRepository` | Passwords are hashed, not stored as plain text | US-3 |
| `test_create_returns_post_with_id` | `PostRepository` | Creating a post assigns an ID | US-5 |
| `test_create_saves_correct_data` | `PostRepository` | Post data is saved correctly | US-5 |
| `test_find_by_id_returns_post` | `PostRepository` | Post can be found by ID | US-5 |
| `test_find_by_id_returns_null_when_not_found` | `PostRepository` | Returns null for unknown ID | US-5 |
| `test_find_by_slug_returns_post` | `PostRepository` | Post can be found by slug (URL) | US-2 |
| `test_find_by_slug_returns_null_when_not_found` | `PostRepository` | Returns null for unknown slug | US-2 |
| `test_find_all_published_only_returns_published_posts` | `PostRepository` | Only published posts appear on the blog | US-1 |
| `test_find_all_returns_all_posts` | `PostRepository` | Admin sees all posts including drafts | US-5 |
| `test_update_changes_title` | `PostRepository` | Editing a post saves changes | US-5 |
| `test_delete_sets_deleted_at` | `PostRepository` | Deleting a post sets a timestamp (soft delete) | US-5 |
| `test_undo_delete_clears_deleted_at` | `PostRepository` | Restoring a post clears deleted_at | US-5 |
| `test_find_all_returns_all_courses` | `CourseRepository` | All courses are returned | US-7 |
| `test_find_all_returns_empty_array_when_no_courses` | `CourseRepository` | Returns empty array when no courses exist | US-7 |
| `test_find_by_id_returns_course` | `CourseRepository` | Course can be found by ID | US-7 |
| `test_find_by_id_returns_null_when_not_found` | `CourseRepository` | Returns null for unknown ID | US-7 |
| `test_grade_is_null_by_default` | `CourseRepository` | A new course has no grade yet | US-7 |
| `test_update_grade_saves_new_grade` | `CourseRepository` | A grade can be saved for a course | US-7 |
| `test_update_grade_can_set_grade_to_null` | `CourseRepository` | A grade can be removed from a course | US-7 |
| `test_get_returns_null_when_no_profile` | `ProfileRepository` | Returns null when no profile exists | US-8 |
| `test_get_returns_profile` | `ProfileRepository` | Profile data is returned correctly | US-8 |
| `test_get_returns_null_for_optional_fields_when_empty` | `ProfileRepository` | Optional fields default to null | US-8 |
| `test_update_saves_changes` | `ProfileRepository` | Profile changes are saved to the database | US-8 |
| `test_update_returns_true` | `ProfileRepository` | Update returns true on success | US-8 |

---

## Running the tests

```bash
# Run all tests
docker compose exec app vendor/bin/phpunit

# Run with code coverage report
docker compose exec app vendor/bin/phpunit --coverage-text
```
