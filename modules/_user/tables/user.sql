-- Attogram Framework - User Module - user table - v0.1.1

CREATE TABLE IF NOT EXISTS 'user' (
'id' INTEGER PRIMARY KEY,
'username' TEXT UNIQUE NOT NULL,
'password' TEXT NOT NULL,
'email' TEXT NOT NULL,
'level' INTEGER NOT NULL DEFAULT '0'
)
