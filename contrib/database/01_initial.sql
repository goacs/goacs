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
	created_at datetime null,
	updated_at datetime null
);

create index cpe_serial_number_index
	on cpe (serial_number);

create unique index cpe_uuid_uindex
	on cpe (uuid);

alter table cpe
	add constraint cpe_pk
		primary key (uuid);