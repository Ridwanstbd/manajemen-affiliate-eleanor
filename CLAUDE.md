# CLAUDE.md — PT Eleanor Project Global Indonesia Affiliate Management System

## 1. Project Overview

- Name : PT Eleanor Project Global Indonesia Affiliate Management System
- Description : Web-based application for affiliate management that processes performance data, distinguishes between regular affiliates and key opinion leaders (KOL), and manages reward and product sample allocations.
- Goal : Centralize performance data processing, incentive calculation, content monitoring, and reward distribution in a single integrated digital ecosystem to minimize manual errors.
- Target Users: Guests (prospective registrants/Pre-Authentication), Administrators, and Affiliators (Regular and KOL).
- Version : v1.0.0
- Status : Active development

## 2. Tech Stack

- Language : PHP, Javascript
- Framework : Laravel 12
- Server : Laragon Web Server on Windows 11 operating system
- Hardware : Intel Core i3 Processor, 8GB RAM
- Editor : Microsoft Visual Studio Code
- Database : Relational Database (consisting of tables like `users`, `creator_metrics`, `products`, `system_access_request`, `kol_contracts`, etc.)

## 3. Commands

composer install
php artisan serve
php artisan migrate
npm install
npm run dev

## 4. Project Structure

app/Http/Controllers/Admin/
app/Http/Controllers/Affiliator/
app/Http/Requests/Admin/
app/Http/Requests/Affiliator/
app/Http/Requests/
app/Models/
database/migrations/
resources/views/
resources/views/auth/
resources/views/components/
resources/views/components/atoms/
resources/views/components/molecules/
resources/views/components/organisms/
resources/views/layouts/
resources/views/pages/
routes/
app/Console/
public/css/

## 5. Naming Conventions

- Controller : PascalCase
- Model : PascalCase (Singular)
- View : Atomic Design
- Database : snake_case (like `system_access_request`, `creator_metrics`, `kol_contracts`)

## 6. Code Conventions

- Apply MVC architecture concepts using the Laravel 12 framework.
- User authentication is done in multiple steps (tiered), starting from checking/validating the TikTok username and continuing to the password matching form.
- Always store specific tokens and expiration times for the password reset link delivery functionality.
- User passwords must be matched using a hash before dashboard access is granted.

## 7. Component Rules

- Blade templates are separated between Guest, Administrator, and Affiliator interfaces based on their access rights.
- Visual components for month-over-month performance data comparison and Return on Investment (ROI) metrics are created specifically for the Analytics Center screen on the Administrator Dashboard.

## 8. Styling Rules

- Dashboard pages must integrate clean interface elements to display list table components, alert widgets, ranking widgets, and announcement boards.

## 9. API & Data Fetching Rules

- The system uses data import functionality to receive uploaded TikTok Analytics XLSX files to be parsed and automatically mapped into the analytics table structure.
- The system must integrate an external courier API to check receipt numbers and track packages so that the sample delivery status automatically changes to "delivered".

## 10. State Management Rules

- User account status management has stages: PENDING, ACTIVE, and BANNED (Blacklist).
- Task status management (task_reports) is organized into stages: PROCESSING, COMPLETED, and OVERDUE.
- Sample request status management (sample_requests) includes: PENDING, APPROVED, SHIPPED, DELIVERED, and REJECTED.

## 11. Performance Rules

- The TikTok Analytics XLSX file upload processing must validate the extension format and the number of files first to prevent server errors.
- Use a boolean toggle (filter) `is_kol` feature to streamline the query for separating analytics and ROI summaries between Regular Affiliators and KOL Affiliators specifically without overloading the page payload.

## 12. Git Rules

feat :
fix :
refactor :
style :
docs :
test :
chore :

## 13. Features

- [x] Guest Access Rights: Checking TikTok username availability in the database, Requesting registration access, Claiming account activation, and Resetting password via email.
- [x] Administrator Access Rights: Tiered login, Importing analytics XLSX files, User management (approval, blacklist, account recovery).
- [x] Advanced Administrator: Product ROI analysis and comparison features, Sample catalog settings, KOL exclusive work contracts, Challenge program management, Courier receipt synchronization via API.
- [x] Affiliator Access Rights: Registration/Claim, Requesting access, Requesting free product samples with terms agreement, Reporting TikTok video links for tasks.
- [x] Advanced Affiliator: Monthly and challenge leaderboards, Task obligation status monitoring indicators (account warnings), Claiming KOL contract quotas, List of winner announcements.

## 14. Testing

- Testing the TikTok username validation check for redirection to the correct form (Access Request / Claim / Login).
- Testing the data validation, parsing, and mapping process on the XLSX file import menu.
- Testing the sample request status update cycle, starting from pending, approval, shipping receipt input, automatic API tracking, to task completion status delivery.

## 15. Do Not

- Do not grant Administrator authority rights to entities with Affiliator or Guest roles.
- Do not allow password recovery access if the token from the email link is invalid or has expired.
- Do not process task reporting completion if the TikTok video URL link input does not have a valid format.
- Do not let an affiliator user log in if their account status is blacklisted.

## 16. Environment Variables

DB_DATABASE
DB_USERNAME
DB_PASSWORD
MAIL_MAILER (must be configured for sending password reset links using the SMTP protocol)
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
RAJAONGKIR_API_KEY (Used to track package movement / sample delivery receipt numbers)
