create table cpe
(
	uuid varchar(36) not null,
	serial_number varchar(50) null,
	oui varchar(6) null,
	software_version varchar(20) null,
	hardware_version varchar(20) null,
	ip_address varchar(15) null,
	connection_request_url varchar(255) null,
	connection_request_user varchar(50) null,
	connection_request_password varchar(64) null,
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null
);

create index cpe_serial_number_index
	on cpe (serial_number);

create unique index cpe_uuid_uindex
	on cpe (uuid);

alter table cpe
	add constraint cpe_pk
		primary key (uuid);

create table cpe_parameters
(
	cpe_uuid varchar(36) null,
    name varchar(255) null,
	value varchar(255) null,
	type varchar(16) null,
	flags varchar(10) null,
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null,
	constraint cpe_parameters_pk
		unique (cpe_uuid, `key`),
	constraint cpe_parameters_cpe_uuid_key_uindex
		unique (cpe_uuid, `key`)
);

create table templates
(
	id int auto_increment
		primary key,
	name varchar(50) null,
	created_at datetime default CURRENT_TIMESTAMP null,
	updated_at datetime default CURRENT_TIMESTAMP null
);

create table templates_parameters
(
	template_id int null,
	name varchar(255) null,
	value varchar(255) null,
	type varchar(16) null,
	flags varchar(10) null,
	created_at datetime default CURRENT_TIMESTAMP not null,
	updated_at datetime default CURRENT_TIMESTAMP not null,
	constraint templates_parameters_pk
		unique (template_id, `key`),
	constraint templates_parameters_template_id_key_uindex
		unique (template_id, `key`)
);

create table cpe_to_templates
(
	cpe_uuid varchar(36) not null,
	template_id int not null,
	priority int null,
	constraint cpe_to_templates_pk
		primary key (cpe_uuid, template_id)
);
