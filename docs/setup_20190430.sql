--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

-- Classificazioni
create table classificazioni (
  id integer not null,
  tipo integer not null,
  des character varying(100) NOT NULL
);

ALTER TABLE public.classificazioni OWNER TO demo;

COMMENT ON TABLE classificazioni IS 'Contiene i vari enumerati utilizzati nel programma e modificabili dall''amministratore';
COMMENT ON COLUMN classificazioni.id IS 'Chiave primaria';
COMMENT ON COLUMN classificazioni.tipo IS 'Tipo di enumerato:
0 = tipo contatto (per gestione contatti)
1 = tipo richiesta (per gestione contatti)
2 = motivo richiesta (per gestione contatti)
3 = tipo immobile (per gestione contatti)
4 = fonte pubblicità (per gestione contatti)
5 = camere o altro (per gestione contatti)';


CREATE SEQUENCE classificazioni_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE public.classificazioni_id_seq OWNER TO demo;
ALTER SEQUENCE classificazioni_id_seq OWNED BY classificazioni.id;
ALTER TABLE ONLY classificazioni ALTER COLUMN id SET DEFAULT nextval('classificazioni_id_seq'::regclass);
ALTER TABLE ONLY classificazioni ADD CONSTRAINT classificazioni_pk PRIMARY KEY (id);


-- Contatti
CREATE TABLE contatti (
    data timestamp without time zone NOT NULL,
    telefono_chiamante character varying(100) NOT NULL,
    nome_chiamante character varying(100),
    cognome_chiamante character varying(100),
    titolo_chiamante character varying(50),
    email_chiamante character varying,
    id_fonte_pubblicita integer,
    id_tipo_immobile integer,
    id_motivo_richiesta integer,
    id_tipo_richiesta integer,
    id_tipo_contatto integer,
    id_camere integer,
    id_maximizer varchar(100),
    comune varchar(100),
    zona varchar(100),    
    superficie_min integer,
    superficie_max integer,
    prezzo numeric(15, 2),
    prezzo_min numeric(15, 2),
    prezzo_max numeric(15, 2),
    note text,
    id_utente_destinatario integer,
    gruppo_wa_destinatario varchar(100),
    id integer NOT NULL,
    data_email_destinatario timestamp without time zone,
    data_wa_destinatario timestamp without time zone
);


ALTER TABLE public.contatti OWNER TO demo;
COMMENT ON TABLE contatti IS 'Contiene l''elenco delle richieste di contatto ricevute dall''operatore addetto e destinate ai vari utenti.
Memorizzare le informazioni base relative alla chiamata (mittente, destinatario, oggetto della chiamata, ecc...) e la data in cui è stata inviata l''ultima email di notifica al destinatario (se presente).
E'' possibile inoltrare la notifica ad destinatario/gruppo di WhatsApp';

CREATE SEQUENCE contatti_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.contatti_id_seq OWNER TO demo;
ALTER SEQUENCE contatti_id_seq OWNED BY contatti.id;
ALTER TABLE ONLY contatti ALTER COLUMN id SET DEFAULT nextval('contatti_id_seq'::regclass);

ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_pk PRIMARY KEY (id);

ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk01 FOREIGN KEY (id_utente_destinatario) REFERENCES utenti(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk02 FOREIGN KEY (id_fonte_pubblicita) REFERENCES classificazioni(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk03 FOREIGN KEY (id_tipo_immobile) REFERENCES classificazioni(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk04 FOREIGN KEY (id_motivo_richiesta) REFERENCES classificazioni(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk05 FOREIGN KEY (id_tipo_richiesta) REFERENCES classificazioni(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk06 FOREIGN KEY (id_tipo_contatto) REFERENCES classificazioni(id);
ALTER TABLE ONLY contatti ADD CONSTRAINT contatti_fk07 FOREIGN KEY (id_camere) REFERENCES classificazioni(id);

ALTER TABLE UTENTI ADD COLUMN cellulare varchar(30);
