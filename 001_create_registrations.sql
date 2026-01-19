PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS schema_migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL UNIQUE,
    applied_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS registrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    father_name TEXT NOT NULL,
    grandfather_name TEXT NOT NULL,
    family_name TEXT NOT NULL,
    full_name TEXT NOT NULL,
    nationality TEXT NOT NULL,
    address TEXT NOT NULL,
    street_number TEXT NOT NULL,
    house_number TEXT NOT NULL,
    student_phone TEXT NOT NULL,
    work_phone TEXT,
    email TEXT NOT NULL,
    program_type TEXT NOT NULL,
    selected_program TEXT NOT NULL,
    notes TEXT,
    submission_date TEXT,
    timestamp TEXT,
    files_json TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);
