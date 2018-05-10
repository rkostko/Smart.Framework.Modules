
-- START :: PostgreSQL Table: web / page_builder r.180509 #####

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

-- Table Structure: Page-Builder #####

CREATE TABLE web.page_builder (
    id character varying(63) NOT NULL,
    ref character varying(63) DEFAULT ''::character varying NOT NULL,
    ctrl character varying(128) DEFAULT ''::character varying NOT NULL,
    active smallint DEFAULT 0 NOT NULL,
    auth smallint DEFAULT 0 NOT NULL,
    special smallint DEFAULT 0 NOT NULL,
    name character varying(255) DEFAULT ''::character varying NOT NULL,
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
    CONSTRAINT page_builder__chk__id CHECK ((char_length((id)::text) >= 2)),
    CONSTRAINT page_builder__chk__active CHECK (((active = 0) OR (active = 1))),
    CONSTRAINT page_builder__chk__auth CHECK (((auth = 0) OR (auth = 1))),
    CONSTRAINT page_builder__chk__special CHECK (((special = 0) OR (special = 1))),
    CONSTRAINT page_builder__chk__mode CHECK ((char_length((mode)::text) >= 3)),
    CONSTRAINT page_builder__chk__published CHECK ((published >= 0))
);

COMMENT ON TABLE web.page_builder IS 'Web - Page Builder v.2018.05.09';
COMMENT ON COLUMN web.page_builder.id IS 'Unique ID for the Record: Page or Segment (segments must begin with: #)';
COMMENT ON COLUMN web.page_builder.ref IS 'Reference Parent ID, Optional';
COMMENT ON COLUMN web.page_builder.ctrl IS 'Reference Controller ID, Optional';
COMMENT ON COLUMN web.page_builder.active IS 'Active Status: 0=inactive ; 1=active';
COMMENT ON COLUMN web.page_builder.auth IS 'Auth Status: 0 = no auth ; 1 = requires auth';
COMMENT ON COLUMN web.page_builder.special IS 'Special Status: 0 = normal ; 1 = special';
COMMENT ON COLUMN web.page_builder.name IS 'Record Name (for management only)';
COMMENT ON COLUMN web.page_builder.mode IS 'Render Mode: html / markdown / text / raw / settings';
COMMENT ON COLUMN web.page_builder.data IS 'Render Active Runtime';
COMMENT ON COLUMN web.page_builder.code IS 'Render Code';
COMMENT ON COLUMN web.page_builder.meta_title IS 'Meta Title, Pages Only';
COMMENT ON COLUMN web.page_builder.meta_description IS 'Meta Description, Pages Only';
COMMENT ON COLUMN web.page_builder.meta_keywords IS 'Meta Keywords, Pages Only';
COMMENT ON COLUMN web.page_builder.layout IS 'Page Design Layout, Pages Only';
COMMENT ON COLUMN web.page_builder.checksum IS 'Checksum (MD5)';
COMMENT ON COLUMN web.page_builder.admin IS 'Author';
COMMENT ON COLUMN web.page_builder.published IS 'Time of Publising: timestamp';
COMMENT ON COLUMN web.page_builder.modified IS 'Last Modification: yyyy-mm-dd';

ALTER TABLE ONLY web.page_builder ADD CONSTRAINT page_builder__id PRIMARY KEY (id);

CREATE INDEX page_builder__idx__ref ON web.page_builder USING btree (ref);
CREATE INDEX page_builder__idx__ctrl ON web.page_builder USING btree (ctrl);
CREATE INDEX page_builder__idx__active ON web.page_builder USING btree (active);
CREATE INDEX page_builder__idx__auth ON web.page_builder USING btree (auth);
CREATE INDEX page_builder__idx__special ON web.page_builder USING btree (special);
CREATE INDEX page_builder__idx__mode ON web.page_builder USING btree (mode);
CREATE INDEX page_builder__idx__layout ON web.page_builder USING btree (layout);
CREATE INDEX page_builder__idx__admin ON web.page_builder USING btree (admin);
CREATE INDEX page_builder__idx__modified ON web.page_builder USING btree (modified);


-- Table Structure: Page-Builder Translations #####

CREATE TABLE web.page_translations (
    id character varying(63) NOT NULL,
    lang character varying(2) NOT NULL,
    code text NOT NULL,
    meta_title character varying(255) DEFAULT ''::character varying NOT NULL,
    meta_description character varying(512) DEFAULT ''::character varying NOT NULL,
    meta_keywords character varying(1024) DEFAULT ''::character varying NOT NULL,
    admin character varying(25) DEFAULT ''::character varying NOT NULL,
    modified character varying(23) DEFAULT ''::character varying NOT NULL,
    CONSTRAINT page_translations__chk__id CHECK ((char_length((id)::text) >= 2)),
    CONSTRAINT page_translations__chk__lang CHECK ((char_length((lang)::text) = 2))
);

ALTER TABLE ONLY web.page_translations ADD CONSTRAINT page_translations_pkey PRIMARY KEY (id, lang);

COMMENT ON TABLE web.page_translations IS 'Web - Page (Builder) Translations v.2018.05.09';
COMMENT ON COLUMN web.page_translations.id IS 'Unique ID for the Record: Page or Segment (segments must begin with: #)';
COMMENT ON COLUMN web.page_translations.lang IS 'Language ID: de, fr, ...';
COMMENT ON COLUMN web.page_translations.code IS 'Render Code';
COMMENT ON COLUMN web.page_translations.admin IS 'Author';
COMMENT ON COLUMN web.page_translations.modified IS 'Last Modification: yyyy-mm-dd';

CREATE INDEX page_translations__idx__admin ON web.page_translations USING btree (admin);
CREATE INDEX page_translations__idx__modified ON web.page_translations USING btree (modified);

--
-- PostgreSQL database dump complete #####
--
