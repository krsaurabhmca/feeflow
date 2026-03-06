# FeeFlow API Documentation (v1)

**Base URL:** `http://localhost/feeflow/api/v1/` (Replace with your server URL)

## Authentication
All protected endpoints require an `Authorization` header:
`Authorization: Bearer <your_api_key>`

---

## 1. Login
**Endpoint:** `POST login.php`
**Payload:**
```json
{
    "email": "admin@example.com",
    "password": "yourpassword"
}
```
**Response:** Returns institute details and `api_key`.

## 2. Dashboard
**Endpoint:** `GET dashboard.php`
**Response:** Status of students, monthly collection, and recent transactions.

## 3. Students
**Endpoint:** `GET students.php`
**Query Params:** `search` (optional)
**Endpoint:** `POST students.php`
**Payload:** `name`, `class_id`, `roll_no` (opt), `phone` (opt), etc.

## 4. Fees
**Endpoint:** `GET fees.php`
**Query Params:** `student_id` (opt)
**Endpoint:** `POST fees.php`
**Payload:** `student_id`, `amount`, `payment_method`, etc.

## 5. Classes
**Endpoint:** `GET classes.php`
**Response:** List of all classes in the institute.
