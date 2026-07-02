-- ============================================================
-- Migration 001: Create contacts and contact_phones tables
-- ============================================================

CREATE TABLE IF NOT EXISTS contacts (
    id         TEXT NOT NULL PRIMARY KEY,
    name       TEXT NOT NULL,
    last_name  TEXT NOT NULL,
    email      TEXT NOT NULL UNIQUE,
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS contact_phones (
    id         INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    contact_id TEXT    NOT NULL,
    phone      TEXT    NOT NULL,

    UNIQUE (contact_id, phone),

    FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE
);