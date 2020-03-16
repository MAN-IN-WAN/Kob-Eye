
update
`kob-Show-Country`
set uid=1,gid=1,umod=7,gmod=7,omod=7,tmsCreate=0,userCreate=1,tmsEdit=0,userEdit=1;


insert into `kob-Show-Country` (id,code,country,phonecode) select id,sortname,name,phonecode from countries;
insert into `kob-Show-State` (id,state,countryid) select id,name,country_id from states;
insert into `kob-Show-City` (id,city,stateid) select id,name,state_id from cities;

update `kob-Show-Translation` t
inner join `kob-Show-Country` c on c.Code=t.Code
set t.Original=c.Country;

