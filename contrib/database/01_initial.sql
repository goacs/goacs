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
	value varchar(255) not null default '',
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
	template_id int not null,
	name varchar(255) not null,
	value varchar(255) not null,
	type varchar(16) not null,
	flags varchar(10) not null default 'R',
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null,
	constraint templates_parameters_pk
		unique (template_id, `name`),
	constraint templates_parameters_template_id_key_index
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
