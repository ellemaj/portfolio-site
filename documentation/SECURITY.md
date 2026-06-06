# Security Design Document

## Project: itdp-ellemaj

This document describes the security design and implementation for three OWASP Top Ten Web Application Security Risks:

- **A01:2025** — Broken Access Control
- **A05:2025** — Injection
- **A07:2025** — Authentication Failures

---

## A01:2025 — Broken Access Control

### What is it?

Broken Access Control occurs when users can act outside of their intended permissions — for example, accessing admin pages without being an admin, or viewing another user's data without authorization.

### Design Motivation

This application has two types of users: regular users and admins. Admins can manage blog posts, view the overview, update grades, and edit the profile page. Regular users should not be able to reach any of these endpoints, not even by manually typing the URL. Without access control, any user could navigate to `/blog/create` or `/blog/{id}/delete` and perform destructive actions.

The chosen approach is **role-based middleware** that intercepts requests before they reach the controller. This keeps authorization logic centralized and consistent — it does not rely on individual controllers remembering to check the role themselves.

### Implementation

#### Middleware: `app/Middleware/AdminMiddleware.php`

A dedicated middleware class checks `$_SESSION['role']` on every request to a protected route. If the role is not `admin`, the request is rejected with a `403 Forbidden` response before any controller logic runs.

```php
// app/Middleware/AdminMiddleware.php
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Forbidden');
}
```

**Why middleware instead of per-controller checks?**
Centralized middleware prevents situations where a developer forgets to add a role check in a new controller method. Every protected route must explicitly declare the middleware — access is denied by default for those routes.

#### Route Protection: `app/RouteProvider.php`

All admin-only routes are registered with the `AdminMiddleware`:

| Route | Method | Protection |
|---|---|---|
| `/blog/manage` | GET | AdminMiddleware |
| `/blog/create` | GET, POST | AdminMiddleware |
| `/blog/{id}/edit` | GET | AdminMiddleware |
| `/blog/{id}/update` | POST | AdminMiddleware |
| `/blog/{id}/delete` | POST | AdminMiddleware |
| `/blog/{id}/restore` | POST | AdminMiddleware |
| `/overview` | GET | AdminMiddleware |
| `/dashboard/grade/update` | POST | AdminMiddleware |
| `/profile/edit` | GET | AdminMiddleware |
| `/profile/update` | POST | AdminMiddleware |

**Why protect both GET and POST for create/edit?**
Protecting only the POST endpoint would still allow an attacker to load the admin form via GET, giving them a visible interface even if submission is blocked. Both the form display and the form submission are protected.

#### Session-Based Role Storage

On successful login, the user's role is stored in the session:

```php
// app/Controllers/UserController.php
$_SESSION['role'] = $user->role;
```

The middleware reads this value on every subsequent request. Roles are set server-side from the database — they cannot be tampered with by the client.

---

## A05:2025 — Injection

### What is it?

Injection attacks occur when untrusted user input is interpreted as code or commands. The most common form in web applications is SQL Injection, where malicious SQL is inserted into a query. Cross-Site Scripting (XSS) is a related risk where malicious JavaScript is injected into HTML output.

### Design Motivation

Every form in this application accepts user input that eventually reaches the database or is displayed back in the browser. Without protection, an attacker could craft an input like `' OR '1'='1` to bypass a login query, or inject `<script>alert(1)</script>` into a blog post title to execute code in a visitor's browser.

The chosen approach uses two layers:

1. **Parameterized queries** for all database interaction — the database driver never treats user input as SQL syntax.
2. **Output escaping** via Twig's auto-escape and PHP's `htmlspecialchars()` — user data is always rendered as plain text, never as HTML markup.

### Implementation

#### Parameterized Queries (SQL Injection Prevention)

All database queries across all repositories use PDO prepared statements with named or positional placeholders. User input is passed as bound parameters — the SQL structure is fixed before any data is inserted.

```php
// app/Repositories/UserRepository.php — findByEmail()
$stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
```

```php
// app/Repositories/PostRepository.php — findBySlug()
$stmt = $this->db->prepare('SELECT * FROM posts WHERE slug = ?');
$stmt->execute([$slug]);
```

```php
// app/Repositories/PostRepository.php — create()
$stmt = $this->db->prepare(
    'INSERT INTO posts (title, slug, content, ...) VALUES (:title, :slug, :content, ...)'
);
$stmt->execute([':title' => $title, ':slug' => $slug, ':content' => $content, ...]);
```

This pattern is applied consistently in:
- `app/Repositories/UserRepository.php` — `findByEmail()`, `findById()`, `create()`
- `app/Repositories/PostRepository.php` — all methods
- `app/Repositories/ProfileRepository.php` — `update()`
- `app/Repositories/CourseRepository.php` — `findById()`, `updateGrade()`

**Why PDO named placeholders over string concatenation?**
String concatenation (`"SELECT * FROM users WHERE email = '" . $email . "'"`) passes raw user input directly into the SQL string, making injection trivial. With PDO, the database engine receives the query structure and the parameter value separately — the value can never alter the query's logic.

#### Input Sanitization: `app/Controllers/UserController.php`

User-provided strings are sanitized before use:

```php
// app/Controllers/UserController.php — register()
$firstName = htmlspecialchars(trim($_POST['firstName']));
$lastName  = htmlspecialchars(trim($_POST['lastName']));
$email     = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // reject invalid email format
}
```

- `trim()` removes accidental whitespace.
- `htmlspecialchars()` converts `<`, `>`, `"`, `&` to HTML entities, preventing these values from being rendered as markup if echoed back.
- `FILTER_SANITIZE_EMAIL` strips illegal characters from the email field.
- `FILTER_VALIDATE_EMAIL` rejects inputs that are not valid email addresses.

#### Template Auto-Escaping (XSS Prevention via Twig)

Twig enables auto-escaping by default. Every variable printed with `{{ variable }}` is HTML-escaped before output. This means even if a malicious string reached the template, it would be rendered as visible text rather than executable HTML.

```twig
{# app/views/blog/manage.html.twig — auto-escaped, safe #}
{{ post.title }}
{{ post.slug }}

{# app/views/profile.html.twig — auto-escaped, safe #}
{{ profile.bio }}
{{ skill }}
```

**Why rely on Twig's auto-escape rather than escaping in the controller?**
Escaping at the output layer (the template) is the correct place because it is context-aware — Twig knows it is rendering into HTML. Escaping in the controller would encode data that might need to be stored or processed normally before rendering.

---

## A07:2025 — Authentication Failures

### What is it?

Authentication failures occur when an application does not properly verify who a user is. This includes weak password storage, missing brute-force protection, improper session handling, session fixation, and missing CSRF protection on state-changing requests.

### Design Motivation

The login system must ensure that only the real owner of an account can authenticate, that passwords are never recoverable even if the database is stolen, and that an authenticated session cannot be hijacked or forged. CSRF tokens prevent attackers from tricking a logged-in user into submitting a form on their behalf.

The chosen approach combines **bcrypt password hashing**, **session regeneration on login**, **CSRF token validation on all forms**, and **constant-time token comparison** to address these threats.

### Implementation

#### Password Hashing: `app/Controllers/UserController.php`

Passwords are never stored in plain text. On registration, `password_hash()` with `PASSWORD_DEFAULT` is used, which currently applies bcrypt with an automatically generated salt:

```php
// app/Controllers/UserController.php — register()
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

On login, `password_verify()` compares the plain-text input against the stored hash without ever reversing the hash:

```php
// app/Controllers/UserController.php — login()
if (!password_verify($password, $user->password)) {
    // reject login
}
```

**Why bcrypt (`PASSWORD_DEFAULT`) over MD5 or SHA-1?**
MD5 and SHA-1 are fast hashing algorithms — fast enough that an attacker with the database can try billions of guesses per second. bcrypt is deliberately slow (it includes a cost factor) and applies a salt automatically, making precomputed rainbow table attacks and brute-force attacks computationally expensive.

**Why `PASSWORD_DEFAULT` over a hardcoded algorithm?**
`PASSWORD_DEFAULT` will automatically upgrade to a stronger algorithm in future PHP versions. All existing hashes remain valid because `password_verify()` detects the algorithm from the hash prefix.

#### Session Regeneration on Login

Immediately after a successful login, the session ID is regenerated:

```php
// app/Controllers/UserController.php — login()
session_regenerate_id(true);
$_SESSION['user_id']   = $user->id;
$_SESSION['role']      = $user->role;
$_SESSION['firstName'] = $user->firstName;
$_SESSION['lastName']  = $user->lastName;
```

**Why regenerate the session ID?**
Session fixation is an attack where an attacker sets a known session ID on a victim's browser (e.g., via a URL parameter), then waits for the victim to log in. If the server keeps the same session ID after login, the attacker's pre-set ID becomes an authenticated session. Regenerating the session ID at login breaks this attack: the old ID is discarded and the attacker gains nothing.

#### CSRF Token Generation and Validation: `src/Session.php`

A CSRF token is generated using `random_bytes()` (cryptographically secure) and stored server-side in the session:

```php
// src/Session.php
public function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

public function validateCsrfToken(string $token): bool
{
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
```

**Why `hash_equals()` instead of `===`?**
Standard string comparison with `===` short-circuits as soon as it finds a mismatching character. A timing attack can measure how long the comparison takes to infer how many characters of a guessed token are correct. `hash_equals()` always takes the same amount of time regardless of where strings differ, eliminating the timing side-channel.

**Why `random_bytes()` instead of `rand()` or `uniqid()`?**
`rand()` and `uniqid()` are not cryptographically secure — their output can be predicted if the attacker knows the seed or system time. `random_bytes()` reads from the OS entropy pool, producing output that cannot be predicted.

#### CSRF Tokens in Forms

Every state-changing form includes a hidden CSRF token field that is validated on the server before any action is taken:

```twig
{# app/views/user/login.html.twig #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">

{# app/views/user/register.html.twig #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">

{# app/views/blog/create.html.twig #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">

{# app/views/blog/edit.html.twig #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">

{# app/views/blog/manage.html.twig — delete and restore forms #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">

{# app/views/user/profile-edit.html.twig #}
<input type="hidden" name="csrf_token" value="{{ csrf_token }}">
```

**Why validate CSRF on login too?**
Although CSRF on a login form is less obviously dangerous, it can be used in login CSRF attacks — an attacker tricks a user into logging in as the attacker's account, so the victim unknowingly uses the attacker's session (e.g., entering personal data that the attacker can later read). Protecting the login form closes this vector.

#### Session Destruction on Logout

On logout, the session is fully destroyed:

```php
// app/Controllers/UserController.php — logout()
session_destroy();
session_start();
```

**Why call `session_destroy()` rather than just unsetting session variables?**
`$_SESSION = []` clears the server-side data but leaves the session cookie and session ID intact. `session_destroy()` invalidates the session ID entirely — even if an attacker captured the session cookie, it becomes worthless after logout.

#### Security Headers: `public/index.php`

Additional HTTP headers harden the application against browser-level attacks:

```php
// public/index.php
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

| Header | Protection |
|---|---|
| `X-Frame-Options: DENY` | Prevents clickjacking — the page cannot be loaded in a `<iframe>` on another domain |
| `X-Content-Type-Options: nosniff` | Prevents MIME-type sniffing — the browser must use the declared content type |
| `Referrer-Policy: strict-origin-when-cross-origin` | Limits what URL information is sent in the `Referer` header to external sites |

---

## Summary Table

| Risk | Control | Location |
|---|---|---|
| A01 Broken Access Control | Role-based AdminMiddleware | `app/Middleware/AdminMiddleware.php` |
| A01 Broken Access Control | All admin routes protected | `app/RouteProvider.php` |
| A01 Broken Access Control | Server-side role stored in session | `app/Controllers/UserController.php` |
| A05 Injection | PDO parameterized queries | All `app/Repositories/*.php` |
| A05 Injection | Input sanitization (`htmlspecialchars`, `filter_var`) | `app/Controllers/UserController.php` |
| A05 Injection | Twig auto-escaping on all output | `app/views/**/*.html.twig` |
| A07 Auth Failures | bcrypt password hashing (`PASSWORD_DEFAULT`) | `app/Controllers/UserController.php` |
| A07 Auth Failures | Session regeneration on login | `app/Controllers/UserController.php` |
| A07 Auth Failures | CSRF tokens with `hash_equals()` timing-safe comparison | `src/Session.php` |
| A07 Auth Failures | CSRF hidden fields on all state-changing forms | `app/views/**/*.html.twig` |
| A07 Auth Failures | Full session destruction on logout | `app/Controllers/UserController.php` |
| A07 Auth Failures | Security headers (X-Frame-Options, etc.) | `public/index.php` |
