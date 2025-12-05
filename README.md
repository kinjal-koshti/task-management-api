Team Task Manager – REST API (Laravel + JWT)

This is a clean and modular REST API built with Laravel, designed for managing tasks and categories with JWT authentication.
The API supports CRUD operations, file uploads, search, sorting, pagination, and follows a scalable folder structure suitable for production.

Features

JWT Authentication (Register / Login)

Category CRUD with soft delete

Task CRUD with optional file upload (image/PDF)

Search, sorting, pagination

REST API standards

Centralized validation and error handling

File storage using Laravel Storage

Database relationships (Category → Task)

Tech Stack

Laravel 12.41.1

PHP 8+

MySQL

jwt-auth

Laravel Storage (local)

Postman (API testing)

Installation & Setup

Follow these steps to run the project locally.

1. Clone Repository
git clone https://github.com/kinjal-koshti/task-management-api.git
cd task-management-api

2. Install Dependencies
composer install

3. Create Environment File
cp .env.example .env


Update .env database credentials:

DB_DATABASE=team_task_manager
DB_USERNAME=root
DB_PASSWORD=

4. Generate App Key
php artisan key:generate

5. Generate JWT Secret
php artisan jwt:secret

6. Run Migrations
php artisan migrate

7. Start Server
php artisan serve

Authentication (JWT)
Register

POST /api/register

{
  "name": "test user",
  "email": "test@example.com",
  "password": "123456"
}

Login

POST /api/login

Response:

{
  "access_token": "xxxx",
  "token_type": "bearer"
}


Include token in every request:

Authorization: Bearer <access_token>

Category API Endpoints
| Method | Endpoint              | Description                        |
| ------ | --------------------- | ---------------------------------- |
| POST   | /api/task/store       | Create task + optional file upload |
| GET    | /api/task/list        | List tasks                         |
| PUT    | /api/task/update/{id} | Update task                        |
| DELETE | /api/task/delete/{id} | Soft delete task                   |


Task API Endpoints
| Method | Endpoint              | Description                        |
| ------ | --------------------- | ---------------------------------- |
| POST   | /api/task/store       | Create task + optional file upload |
| GET    | /api/task/list        | List tasks                         |
| PUT    | /api/task/update/{id} | Update task                        |
| DELETE | /api/task/delete/{id} | Soft delete task                   |

Task Filters (Search, Sorting, Pagination)

Example:

/api/task/list?search=api&sort_by=title&order=asc&page=1&limit=10

Supported Filter Params

search= keyword search

sort_by= title / created_at / status

order= asc / desc

status= todo / in_progress / done

page= page number

limit= items per page

File Upload (Task Attachment)

Field Name: attachment
Supported Types: .jpg, .jpeg, .png, .pdf
Stored At: storage/app/public/tasks/

Postman (form-data):

title        : Task One
category_id  : 1
attachment   : <file>

Database Dump

The SQL dump is available at:

database/schema.sql

Author

GitHub: https://github.com/kinjal-koshti