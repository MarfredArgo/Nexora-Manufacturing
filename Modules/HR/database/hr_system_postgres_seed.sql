-- PostgreSQL seed data converted from the MySQL/phpMyAdmin dump.
-- Tables are created by Laravel migrations; this file only loads data.

BEGIN;

-- Employees (upsert so re-running is safe)
INSERT INTO employees (
  id, employee_id, first_name, middle_name, last_name, suffix, nationality,
  email, company_email, temporary_password, phone, position, department,
  hire_date, work_schedule, created_at, updated_at, profile_picture,
  gender, marital_status, address, birth_certificate, curriculum_vitae,
  valid_id, medical_certificate, signature
) VALUES
(1, '20260001', 'Pinnah Rosh', 'Rivera', 'Bocalan', NULL, 'Filipino', 'roshbocalan@gmail.com', 'pinnah roshrivera@nexora.com', 'NEX-MQO1ZD', '09765979137', 'BI MANAGER', 'Business Intelligence', '2026-07-12', '23:16:00', '2026-07-12 07:16:25', '2026-07-13 04:01:41', NULL, 'Female', NULL, 'Austin TX', NULL, NULL, NULL, NULL, NULL),
(2, '20260002', 'Rosh', 'Rivera', 'Bocalan', NULL, 'Filipino', 'pinnaroshbocalan@gmail.com', 'roshrivera@nexora.com', 'NEX-WN6RUJ', '097653233211', 'E-COMMERCE MANAGER', 'Electronic Commerce', '2026-07-12', '23:24:00', '2026-07-12 07:24:24', '2026-07-13 04:02:04', NULL, 'Female', NULL, 'Balimbing St.', NULL, NULL, NULL, NULL, NULL),
(3, '20260003', 'Arnold Angelgabriel', 'Ricomano', 'Solis', NULL, 'Filipino', 'gabrielsolis@gmail.com', 'arnoldangelgabrielsolis@nexora.com', 'NEX-JW3JHX', '09765323321', 'ACCOUNTANT', 'Finance', '2026-07-13', '15:10:00', '2026-07-12 08:09:19', '2026-07-13 04:02:17', NULL, 'Male', NULL, 'Balimbing St.', NULL, NULL, NULL, NULL, NULL),
(4, '20260004', 'Esander John', 'Torralba', 'Layosa', 'Jr', 'Filipino', 'esanderlayosa@gmail.com', 'esanderjohnlayosa@nexora.com', 'NEX-JDN4VM', '09765979137', 'HR MANAGER', 'Human Resources', '2026-07-13', '08:49:00', '2026-07-12 16:49:32', '2026-07-13 04:02:32', NULL, 'Male', NULL, 'Austin TX', NULL, NULL, NULL, NULL, NULL),
(5, '20260005', 'Isaiah Gab', 'Torralba', 'Alvarez', NULL, 'Filipino', 'gabalvarez@gmail.com', 'isaiahgabalvarez@nexora.com', 'NEX-XDXYF7', '097653233211', 'E-COMMERCE MANAGER', 'Electronic Commerce', '2026-07-13', '14:01:00', '2026-07-12 22:04:24', '2026-07-13 03:40:38', NULL, 'Male', NULL, 'Balimbing St.', NULL, NULL, NULL, NULL, NULL)
ON CONFLICT (email) DO UPDATE SET
  employee_id = EXCLUDED.employee_id,
  first_name = EXCLUDED.first_name,
  middle_name = EXCLUDED.middle_name,
  last_name = EXCLUDED.last_name,
  suffix = EXCLUDED.suffix,
  nationality = EXCLUDED.nationality,
  company_email = EXCLUDED.company_email,
  temporary_password = EXCLUDED.temporary_password,
  phone = EXCLUDED.phone,
  position = EXCLUDED.position,
  department = EXCLUDED.department,
  hire_date = EXCLUDED.hire_date,
  work_schedule = EXCLUDED.work_schedule,
  updated_at = EXCLUDED.updated_at,
  gender = EXCLUDED.gender,
  address = EXCLUDED.address;

SELECT setval(pg_get_serial_sequence('employees', 'id'), COALESCE((SELECT MAX(id) FROM employees), 1));

-- Departments (derived from employee data; not in original dump inserts)
INSERT INTO departments (id, department_name, department_code, slug, created_at, updated_at) VALUES
(1, 'Business Intelligence', 'BI', 'business-intelligence', NOW(), NOW()),
(2, 'Electronic Commerce', 'EC', 'ecommerce', NOW(), NOW()),
(3, 'Finance', 'FIN', 'finance', NOW(), NOW()),
(4, 'Human Resources', 'HR', 'human-resources', NOW(), NOW())
ON CONFLICT DO NOTHING;

SELECT setval(pg_get_serial_sequence('departments', 'id'), COALESCE((SELECT MAX(id) FROM departments), 1));

-- Session from dump (optional; users will get new sessions on login)
INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity) VALUES
('3hj7iBDJQncdvUfbz7jvluWbXLiqwwo0Q7OKIgiD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJEUHhpSzZHcnVaYkk2QWlBQ0thQ2hEcFhwRkM3VjFQMWpNSDFoY1BHIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9kYXNoYm9hcmQiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX0sImVtcGxveWVlX2xvZ2dlZF9pbiI6dHJ1ZSwiZW1wbG95ZWVfaWQiOjQsImVtcGxveWVlX25hbWUiOiJFc2FuZGVyIEpvaG4iLCJlbXBsb3llZV9lbWFpbCI6ImVzYW5kZXJqb2hubGF5b3NhQG5leG9yYS5jb20ifQ==', 1783946100)
ON CONFLICT (id) DO NOTHING;

COMMIT;
