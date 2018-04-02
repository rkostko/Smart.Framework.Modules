
-- START :: PostgreSQL Table: web / page_builder r.180329 #####

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;
SET default_tablespace = '';
SET default_with_oids = false;

-- Schema #####

CREATE SCHEMA web;
COMMENT ON SCHEMA web IS 'Web Area';

SET search_path = web, pg_catalog;

-- Table Structure #####

CREATE TABLE page_builder (
    id character varying(63) NOT NULL,
    ref character varying(63) DEFAULT ''::character varying NOT NULL,
    ctrl character varying(128) DEFAULT ''::character varying NOT NULL,
    active smallint DEFAULT 0 NOT NULL,
    auth smallint DEFAULT 0 NOT NULL,
    special smallint DEFAULT 0 NOT NULL,
    title character varying(255) DEFAULT ''::character varying NOT NULL,
    mode character varying(8) NOT NULL,
    data text DEFAULT ''::text NOT NULL,
    code text DEFAULT ''::text NOT NULL,
    meta_title character varying(255) DEFAULT ''::character varying NOT NULL,
    meta_description character varying(512) DEFAULT ''::character varying NOT NULL,
    meta_keywords character varying(1024) DEFAULT ''::character varying NOT NULL,
    layout character varying(75) DEFAULT ''::character varying NOT NULL,
    checksum character varying(40) DEFAULT ''::character varying NOT NULL,
    admin character varying(25) DEFAULT ''::character varying NOT NULL,
    published bigint DEFAULT 0 NOT NULL,
    modified character varying(23) DEFAULT ''::character varying NOT NULL,
    CONSTRAINT page_builder__check__id CHECK ((char_length((id)::text) >= 2)),
    CONSTRAINT page_builder__check__active CHECK (((active = 0) OR (active = 1))),
    CONSTRAINT page_builder__check__auth CHECK (((auth = 0) OR (auth = 1))),
    CONSTRAINT page_builder__check__special CHECK (((special = 0) OR (special = 1))),
    CONSTRAINT page_builder__check__mode CHECK ((char_length((mode)::text) >= 3)),
    CONSTRAINT page_builder__check__published CHECK ((published >= 0))
);

COMMENT ON TABLE page_builder IS 'Web - Page Builder v.2018.03.29';
COMMENT ON COLUMN page_builder.id IS 'Unique ID for the Record: Page or Segment (segments must begin with: #)';
COMMENT ON COLUMN page_builder.ref IS 'Reference Parent ID, Optional';
COMMENT ON COLUMN page_builder.ctrl IS 'Reference Controller ID, Optional';
COMMENT ON COLUMN page_builder.active IS 'Active Status: 0=inactive ; 1=active';
COMMENT ON COLUMN page_builder.auth IS 'Auth Status: 0 = no auth ; 1 = requires auth';
COMMENT ON COLUMN page_builder.special IS 'Special Status: 0 = normal ; 1 = special';
COMMENT ON COLUMN page_builder.title IS 'Record Title (for management only)';
COMMENT ON COLUMN page_builder.mode IS 'Render Mode: html / markdown / text / raw / settings';
COMMENT ON COLUMN page_builder.data IS 'Render Active Runtime';
COMMENT ON COLUMN page_builder.code IS 'Render Code';
COMMENT ON COLUMN page_builder.meta_title IS 'Meta Title, Pages Only';
COMMENT ON COLUMN page_builder.meta_description IS 'Meta Description, Pages Only';
COMMENT ON COLUMN page_builder.meta_keywords IS 'Meta Keywords, Pages Only';
COMMENT ON COLUMN page_builder.layout IS 'Page Design Layout, Pages Only';
COMMENT ON COLUMN page_builder.checksum IS 'Checksum (MD5)';
COMMENT ON COLUMN page_builder.admin IS 'Author';
COMMENT ON COLUMN page_builder.published IS 'Time of Publising: timestamp';
COMMENT ON COLUMN page_builder.modified IS 'Last Modification: yyyy-mm-dd';

ALTER TABLE ONLY page_builder ADD CONSTRAINT page_builder__id PRIMARY KEY (id);

CREATE INDEX page_builder__ref ON page_builder USING btree (ref);
CREATE INDEX page_builder__ctrl ON page_builder USING btree (ctrl);
CREATE INDEX page_builder__active ON page_builder USING btree (active);
CREATE INDEX page_builder__auth ON page_builder USING btree (auth);
CREATE INDEX page_builder__special ON page_builder USING btree (special);
CREATE INDEX page_builder__mode ON page_builder USING btree (mode);
CREATE INDEX page_builder__layout ON page_builder USING btree (layout);
CREATE INDEX page_builder__admin ON page_builder USING btree (admin);

--
-- PostgreSQL database dump complete #####
--
