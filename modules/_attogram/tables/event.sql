-- Attogram Framework - event table - v0.0.1

CREATE TABLE IF NOT EXISTS 'event' (
'id' INTEGER PRIMARY KEY,
'time' INTEGER,
'channel' TEXT,
'level' INTEGER,
'message' TEXT
)
