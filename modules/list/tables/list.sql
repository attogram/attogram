-- Attogram Framework - List Module - list table - v0.0.1

CREATE TABLE IF NOT EXISTS 'list' (
'id' INTEGER PRIMARY KEY,
'list' TEXT NOT NULL,
'item' TEXT NOT NULL,
'ordering' INTEGER NOT NULL DEFAULT 0
)
