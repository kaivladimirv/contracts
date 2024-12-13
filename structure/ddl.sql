create table if not exists insurance_companies
(
    id varchar(36) not null constraint insurance_companies_pk primary key,
    name varchar(255) not null unique,
    email varchar(255) not null unique,
    password_hash varchar(255) not null,
    email_confirm_token varchar(255),
    is_email_confirmed boolean not null default false,
    access_token varchar(255),
    access_token_expires timestamp,
    is_deleted boolean not null default false
);

create table if not exists persons
(
    id varchar(36) not null constraint persons_pk primary key,
    last_name varchar(255) not null,
    first_name varchar(255) not null,
    middle_name varchar(255) not null,
    email varchar(255),
    phone_number varchar(15),
    insurance_company_id varchar(36) not null  references insurance_companies (id),
    telegram_user_id varchar(50),
    notifier_type smallint not null,
    unique (email, insurance_company_id),
    unique (phone_number, insurance_company_id)
);

create table if not exists services
(
    id varchar(36) not null constraint services_pk primary key,
    name varchar(255) not null,
    insurance_company_id varchar(36) not null  references insurance_companies (id),
    unique (name, insurance_company_id)
);

create table if not exists contracts
(
    id varchar(36) not null constraint contracts_pk primary key,
    number varchar(50) not null,
    start_date timestamp not null,
    end_date timestamp not null,
    max_amount double precision not null,
    insurance_company_id varchar(36) not null  references insurance_companies (id),
    unique (number, insurance_company_id)
);

create table if not exists insured_persons
(
    id varchar(36) not null constraint insured_persons_pk primary key,
    contract_id varchar(36) not null  references contracts (id),
    person_id varchar(36) not null  references persons (id),
    policy_number varchar(30) not null,
    is_allowed_to_exceed_limit boolean not null default false,
    unique (contract_id, person_id),
    unique (contract_id, policy_number)
);

create table if not exists contract_services
(
    id varchar(36) not null constraint contract_services_pk primary key,
    contract_id varchar(36) not null  references contracts (id),
    service_id varchar(36) not null  references services (id),
    limit_type smallint not null,
    limit_value double precision not null,
    unique (contract_id, service_id)
);

create table if not exists provided_services
(
    id varchar(36) not null constraint provided_services_pk primary key,
    contract_id varchar(36) not null  references contracts (id),
    insured_person_id varchar(36) not null  references insured_persons (id),
    date_of_service timestamp not null,
    service_id varchar(36) not null,
    service_name varchar(255) not null,
    limit_type smallint not null,
    quantity double precision not null,
    price double precision not null,
    amount double precision not null,
    is_deleted boolean not null default false,
    deletion_date timestamp
);

create table if not exists balances
(
    id serial not null constraint balances_pk primary key,
    contract_id varchar(36) not null  references contracts (id),
    insured_person_id varchar(36) not null  references insured_persons (id) on delete cascade,
    service_id varchar(36) not null,
    limit_type smallint not null,
    balance double precision not null,
    unique (insured_person_id, service_id, limit_type)
);

create table if not exists log_activity
(
    id       bigint generated always as identity primary key,
    datetime timestamp   not null,
    log_type integer     not null,
    actor_id varchar(36) not null,
    data     jsonb       not null
);
