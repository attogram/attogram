-- Attogram Framework - user table - v0.0.1

CREATE TABLE IF NOT EXISTS 'user' (
'id' INTEGER PRIMARY KEY,
'username' TEXT UNIQUE NOT NULL,
'password' TEXT NOT NULL,
'email' TEXT NOT NULL,
'level' INTEGER NOT NULL DEFAULT '0',
'last_login' DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
'last_host' TEXT NOT NULL
)