# OrangeHRM OpenLdap docker image
## this image is based on osixia/openldap image.

Quick Start

Run OpenLDAP docker image:

docker run --name my-openldap-container --detach thulana/openldap:latest
This start a new container with OpenLDAP running inside. Let's make the first search in our LDAP container:

docker exec my-openldap-container ldapsearch -x -H ldap://localhost -b dc=example,dc=org -D "cn=admin,dc=example,dc=org" -w admin
This should output:
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
# extended LDIF
#
# LDAPv3
# base <dc=example,dc=org> with scope subtree
# filter: (objectclass=*)
# requesting: ALL
#

[...]

# numResponses: 3
# numEntries: 2
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
If you have the following error, OpenLDAP is not started yet, maybe you are too fast or maybe your computer is to slow, as you want... but wait some time before retrying.

	ldap_sasl_bind(SIMPLE): Can't contact LDAP server (-1)

#Environment Variables

Environment variables defaults are set in image/environment/default.yaml and image/environment/default.startup.yaml.

See how to set your own environment variables

Default.yaml

Variables defined in this file are available at anytime in the container environment.

General container configuration:

LDAP_LOG_LEVEL: Slap log level. defaults to 256. See table 5.1 in http://www.openldap.org/doc/admin24/slapdconf2.html for the available log levels.
Default.startup.yaml

Variables defined in this file are only available during the container first start in startup files. This file is deleted right after startup files are processed for the first time, then all of these values will not be available in the container environment.

This helps to keep your container configuration secret. If you don't care all environment variables can be defined in default.yaml and everything will work fine.

Required and used for new ldap server only:

LDAP_ORGANISATION: Organisation name. Defaults to Example Inc.

LDAP_DOMAIN: Ldap domain. Defaults to example.org

LDAP_BASE_DN: Ldap base DN. If empty automatically set from LDAP_DOMAIN value. Defaults to (empty)

LDAP_ADMIN_PASSWORD Ldap Admin password. Defaults to admin

LDAP_CONFIG_PASSWORD Ldap Config password. Defaults to config

LDAP_READONLY_USER Add a read only user. Defaults to false

LDAP_READONLY_USER_USERNAME Read only user username. Defaults to readonly

LDAP_READONLY_USER_PASSWORD Read only user password. Defaults to readonly

LDAP_RFC2307BIS_SCHEMA Use rfc2307bis schema instead of nis schema. Defaults to false

Backend:

LDAP_BACKEND: Ldap backend. Defaults to hdb (In comming versions v1.2.x default will be mdb)

Help: http://www.openldap.org/doc/admin24/backends.html

TLS options:

LDAP_TLS: Add openldap TLS capabilities. Can't be removed once set to true. Defaults to true.

LDAP_TLS_CRT_FILENAME: Ldap ssl certificate filename. Defaults to ldap.crt

LDAP_TLS_KEY_FILENAME: Ldap ssl certificate private key filename. Defaults to ldap.key

LDAP_TLS_CA_CRT_FILENAME: Ldap ssl CA certificate filename. Defaults to ca.crt

LDAP_TLS_ENFORCE: Enforce TLS but except ldapi connections. Can't be disabled once set to true. Defaults to false.

LDAP_TLS_CIPHER_SUITE: TLS cipher suite. Defaults to SECURE256:+SECURE128:-VERS-TLS-ALL:+VERS-TLS1.2:-RSA:-DHE-DSS:-CAMELLIA-128-CBC:-CAMELLIA-256-CBC, based on Red Hat's TLS hardening guide

LDAP_TLS_VERIFY_CLIENT: TLS verify client. Defaults to demand

Help: http://www.openldap.org/doc/admin24/tls.html

Replication options:

LDAP_REPLICATION: Add openldap replication capabilities. Defaults to false

LDAP_REPLICATION_CONFIG_SYNCPROV: olcSyncRepl options used for the config database. Without rid and provider which are automatically added based on LDAP_REPLICATION_HOSTS. Defaults to binddn="cn=admin,cn=config" bindmethod=simple credentials=$LDAP_CONFIG_PASSWORD searchbase="cn=config" type=refreshAndPersist retry="60 +" timeout=1 starttls=critical

LDAP_REPLICATION_DB_SYNCPROV: olcSyncRepl options used for the database. Without rid and provider which are automatically added based on LDAP_REPLICATION_HOSTS. Defaults to binddn="cn=admin,$LDAP_BASE_DN" bindmethod=simple credentials=$LDAP_ADMIN_PASSWORD searchbase="$LDAP_BASE_DN" type=refreshAndPersist interval=00:00:00:10 retry="60 +" timeout=1 starttls=critical

LDAP_REPLICATION_HOSTS: list of replication hosts, must contain the current container hostname set by --hostname on docker run command. Defaults to :

 - ldap://ldap.example.org
- ldap://ldap2.example.org
If you want to set this variable at docker run command add the tag #PYTHON2BASH: and convert the yaml in python:

  docker run --env LDAP_REPLICATION_HOSTS="#PYTHON2BASH:['ldap://ldap.example.org','ldap://ldap2.example.org']" --detach osixia/openldap:1.1.9
To convert yaml to python online: http://yaml-online-parser.appspot.com/

Other environment variables:

KEEP_EXISTING_CONFIG: Do not change the ldap config. Defaults to false

if set to true with an existing database, config will remain unchanged. Image tls and replication config will not be run. The container can be started with LDAP_ADMIN_PASSWORD and LDAP_CONFIG_PASSWORD empty or filled with fake data.
if set to true when bootstrapping a new database, bootstap ldif and schema will not be added and tls and replication config will not be run.
LDAP_REMOVE_CONFIG_AFTER_SETUP: delete config folder after setup. Defaults to true

LDAP_SSL_HELPER_PREFIX: ssl-helper environment variables prefix. Defaults to ldap, ssl-helper first search config from LDAP_SSL_HELPER_* variables, before SSL_HELPER_* variables.

#Beginner Guide

##Create new ldap server

This is the default behavior when you run this image. It will create an empty ldap for the company Example Inc. and the domain example.org.

By default the admin has the password admin. All those default settings can be changed at the docker command line, for example:

docker run --env LDAP_ORGANISATION="My Company" --env LDAP_DOMAIN="my-company.com" \
--env LDAP_ADMIN_PASSWORD="JonSn0w" --detach osixia/openldap:1.1.9
Data persistence

The directories /var/lib/ldap (LDAP database files) and /etc/ldap/slapd.d (LDAP config files) are used to persist the schema and data information, and should be mapped as volumes, so your ldap files are saved outside the container (see Use an existing ldap database). However it can be useful to not use volumes, in case the image should be delivered complete with test data - this is especially useful when deriving other images from this one.

For more information about docker data volume, please refer to:

https://docs.docker.com/engine/tutorials/dockervolumes/
Edit your server configuration

Do not edit slapd.conf it's not used. To modify your server configuration use ldap utils: ldapmodify / ldapadd / ldapdelete

##Seed ldap database with ldif

This image can load ldif files at startup with either ldapadd or ldapmodify. Mount .ldif in /container/service/slapd/assets/config/bootstrap/ldif directory if you want to overwrite image default boostrap ldif files or in /container/service/slapd/assets/config/bootstrap/ldif/custom (recommended) to extend image config.

Files containing changeType: attributes will be loaded with ldapmodify.

The startup script provide some substitution in bootstrap ldif files: {{LDAP_BASE_DN }} and {{ LDAP_BACKEND }} values are supported. Other {{ * }} substitution are left as is.

Since startup script modifies ldif files, you must add --copy-service argument to entrypoint if you don't want to overwrite them.

	# single file example:
	docker run \
  --volume ./bootstrap.ldif:/container/service/slapd/assets/config/bootstrap/ldif/50-bootstrap.ldif \
  osixia/openldap:1.1.9 --copy-service

	#directory example:
	docker run \
     --volume ./lidf:/container/service/slapd/assets/config/bootstrap/ldif/custom \
     osixia/openldap:1.1.9 --copy-service
Use an existing ldap database

This can be achieved by mounting host directories as volume. Assuming you have a LDAP database on your docker host in the directory /data/slapd/database and the corresponding LDAP config files on your docker host in the directory /data/slapd/config simply mount this directories as a volume to /var/lib/ldap and /etc/ldap/slapd.d:

docker run --volume /data/slapd/database:/var/lib/ldap \
--volume /data/slapd/config:/etc/ldap/slapd.d
--detach osixia/openldap:1.1.9
You can also use data volume containers. Please refer to:

https://docs.docker.com/engine/tutorials/dockervolumes/
Note: By default this image is waiting an hdb database backend, if you want to use any other database backend set backend type via the LDAP_BACKEND environement variable.

