\c interview_calendar;

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE account (
    uuid UUID DEFAULT uuid_generate_v4() PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname  VARCHAR(100) NOT NULL,
    email     VARCHAR(255) UNIQUE NOT NULL,
    type      INT NOT NULL DEFAULT 0,
    created   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated   TIMESTAMP
);

CREATE TABLE availability (
    id serial PRIMARY KEY,
    account_uuid UUID NOT NULL REFERENCES account(uuid),
    date DATE NOT NULL,
    interval INT4RANGE NOT NULL,
    created   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated   TIMESTAMP,
    UNIQUE(account_uuid, date, interval)
);

INSERT INTO account (firstname, lastname, email, type)
VALUES
('Ines', 'Doe', 'idoe@gmail.com', 1),
('Ingrid', 'Smith', 'ismith@gmail.com', 1),
('Carl', 'White', 'cwhite@gmail.com', 0);



