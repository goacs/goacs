create table cpe
(
	uuid varchar(36) not null,
	serial_number varchar(50) not null default '',
	oui varchar(6) not null default '',
	software_version varchar(20) not null default '',
	hardware_version varchar(20) not null default '',
	ip_address varchar(15) not null default '',
	connection_request_url varchar(255) not null default '',
	connection_request_user varchar(50) not null default '',
	connection_request_password varchar(64) not null default '',
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null
);

create index cpe_serial_number_index
	on cpe (serial_number);

alter table cpe
	add constraint cpe_pk
		primary key (uuid);

create table cpe_parameters
(
	cpe_uuid varchar(36) not null,
    name varchar(255) not null,
	value text not null default '',
	type varchar(16) not null,
	flags varchar(10) not null default 'R',
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null,
	constraint cpe_parameters_pk
		unique (cpe_uuid, `name`),
	constraint cpe_parameters_cpe_uuid_key_index
		unique (cpe_uuid, `name`)
);

create table templates
(
	id int auto_increment
		primary key,
	name varchar(50) not null,
	created_at datetime default CURRENT_TIMESTAMP null,
	updated_at datetime default CURRENT_TIMESTAMP null
);

create table templates_parameters
(
    uuid varchar(36) not null,
    template_id int not null,
	name varchar(255) not null,
	value varchar(255) not null,
	type varchar(16) not null,
	flags varchar(10) not null default 'R',
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null,
    constraint templates_parameters_pk
        primary key (uuid),
	constraint templates_parameters_unique
		unique (template_id, `name`)

);

create table cpe_to_templates
(
	cpe_uuid varchar(36) not null,
	template_id int not null,
	priority int not null default 100,
	constraint cpe_to_templates_pk
		primary key (cpe_uuid, template_id)
);

create table faults
(
    uuid varchar(36) not null,
    cpe_uuid varchar(36) not null,
    code varchar(50) not null,
    message varchar(2000) not null,
    created_at datetime default current_timestamp not null,
    constraint fault_pk
		primary key (uuid)
);

create table users
(
    uuid varchar(36) not null
        primary key,
    username varchar(30) null,
    password varchar(128) null,
    email varchar(100) null,
    status int default 1 null,
    constraint users_username_uindex
        unique (username)
);

INSERT INTO users (uuid, username, password, email, status)
VALUES ('84770763-4c6f-47b2-b966-97798c1b7e18', 'admin', '$2a$10$hpoB9cOropWQ6DTZuBku/e8hUkl7jNZ7F231/uJhEqE8Cipp4SdOC', 'admin@goacs.net', 1);


create table tasks
(
    id int auto_increment
        primary key,
    for_name varchar(20),
    for_id varchar(36),
    event varchar(50),
    not_before datetime,
    task varchar(80),
    script text default '',
    infinite bool default false,
    created_at datetime,
    done_at datetime null
);

create index tasks_for_index
    on tasks (for_name, for_id);


create table config
(
    config_key varchar(50) not null
        primary key,
    config_value varchar(100) null
);

