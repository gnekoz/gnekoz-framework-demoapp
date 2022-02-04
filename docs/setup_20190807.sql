
alter table consumi add column flg_addebitato integer;

update consumi set flg_addebitato = 1;