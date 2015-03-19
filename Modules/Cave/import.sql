delete from a_client where substr(trim(nom),1,1) in ('*','-','.') or substr(trim(rs),1,1) in ('*','-','.');
delete from a_prod where substr(trim(nom),1,1) in ('*','-','.') or substr(trim(rs),1,1) in ('*','-','.');
truncate `loc-Cave-Tiers`;

insert into `loc-Cave-Tiers` (id,Nom,Societe,TypeTiersId,umod,gmod,omod) values (1,'-FERMENTATION','-FERMENTATION',1,7,7,7);

insert into `loc-Cave-Tiers` (Nom,CodPostal,Ville,Telephone1,Telephone2,fax,Societe,Email,TypeTiersId,umod,gmod,omod)
select nom,cp,ville,tel1,tel2,fax,rs,mail,2,7,7,7 from a_client;

insert into `loc-Cave-Tiers` (Nom,CodPostal,Ville,Telephone1,fax,Societe,Email,TypeTiersId,umod,gmod,omod)
select nom,cp,ville,tel1,fax,rs,mail,1,7,7,7 from a_prod;




truncate `loc-Cave-Categorie`;

update app set app=trim(app);

insert into `loc-Cave-Categorie` (id,Categorie,umod,gmod,omod) values (1,'-APTE',7,7,7);

insert into `loc-Cave-Categorie` (Categorie,umod,gmod,omod)
select distinct trim(trim(both 'Rosé' from trim(both 'Blanc' from trim(both 'Rouge' from app)))) as cat,7,7,7 
from app o where app<>''order by cat;



truncate `loc-Cave-Lot`;

insert into `loc-Cave-Lot` (Lot,Date,CategorieId,CouleurId,Degre,Notes,umod,gmod,omod)
select distinct lot,UNIX_TIMESTAMP(min(date)) as dt,g.id,
if(locate('Rouge',app)>0,1,if(locate('Blanc',app)>0,2,if(locate('Rosé',app)>0,3,0))) as coul,deg,'',7,7,7 
from a_cuve o
left join `loc-Cave-Categorie` g on g.Categorie=trim(trim(both 'Rosé' from trim(both 'Blanc' from trim(both 'Rouge' from app))))
where lot<>'' and lot<>'0' and (app<>'' or oper<>'Nettoyage') 
group by lot;

update `loc-Cave-Lot` set CouleurId=4 where CouleurId=0;


truncate `loc-Cave-Cuve`;

insert into `loc-Cave-Cuve` (cuve,capacite,volume,Cuvelotid,EtatCuveid,notes,umod,gmod,omod,oper,volop,desprov)
select  trim(cuve),vol,vc,l.id,11,trim(obs),7,7,7,oper,voper,despro from a_cuve c
left join `loc-Cave-Lot` l on l.Lot=c.lot
where trim(cuve)<>'';

update `loc-Cave-Cuve` set cuve=concat(substr('00',length(cuve)), cuve) where cuve regexp '^[1-9]';
update `loc-Cave-Cuve` set cuve=concat(substr(cuve,1,1),substr('0',length(cuve)-1), substr(cuve,2)) where cuve regexp '^[A-B]';
update `loc-Cave-Cuve` set cuve=concat(substr(cuve,1,2),substr('0',length(cuve)-2), substr(cuve,3)) where cuve regexp '^CV';

update `loc-Cave-Cuve` set occupation=round(volume/capacite*100);
update `loc-Cave-Cuve` set CuveLotId=0 where CuvelotId is null;
update `loc-Cave-Cuve` set EtatCuveid=10,Vide=1 where cuvelotid=0 and Volume=0;
update `loc-Cave-Cuve` set EtatCuveid=20 where cuvelotid>0 and Volume=0;

update `loc-Cave-Lot` l
join (select lot,sum(vc) as vc from a_cuve group by lot) c on c.lot=l.lot
set l.VolumeReel=c.vc,l.VolumeRestant=c.vc;

update `loc-Cave-Lot` set EtatLotid=2 where volumereel>0;
update `loc-Cave-Lot` set EtatLotid=3 where volumereel=0;
update `loc-Cave-Lot` set Lot=replace(lot,'/','-');

truncate `loc-Cave-Operation`;
truncate `loc-Cave-Analyse`;

insert into `loc-Cave-Operation` (OperationCuveId,OperationLotId,VolumeReel,Degre,Date,TypeId,SousTypeId,umod,gmod,omod)
select c.id,cuvelotid,c.volume,l.Degre,floor(UNIX_TIMESTAMP(now())/86400)*86400,5,51,7,7,7
from `loc-Cave-Cuve` c
left join `loc-Cave-Lot` l on l.id=CuveLotId;

update `loc-Cave-Cuve` c join `loc-Cave-Operation` o on o.OperationCuveId=c.Id
set c.InventaireId=o.Id;

truncate `loc-Cave-Tache`;
truncate `loc-Systeme-Alert`;
truncate `loc-Systeme-AlertUser`;









/////////////////////////////////////////////////////////////










delete from a_oper where cuve='';

truncate `loc-Cave-Categorie`;
truncate `loc-Cave-Lot`;
truncate `loc-Cave-Operation`;
truncate `loc-Cave-Analyse`;
truncate `loc-Cave-Cuve`;

insert into `loc-Cave-Cuve` (cuve,capacite,EtatCuveid,umod,gmod,omod)
select distinct trim(cuve),vol,11,7,7,7 from a_oper;


insert into `loc-Cave-Categorie` (Categorie,umod,gmod,omod)
select distinct trim(trim(both 'Rosé' from trim(both 'Blanc' from trim(both 'Rouge' from app)))) as cat,7,7,7 
from a_oper o
where app<>''
order by cat;


insert into `loc-Cave-Lot` (Lot,Date,CategorieId,CouleurId,Notes,umod,gmod,omod)
select distinct lot,UNIX_TIMESTAMP(min(date)) as dt,g.id,
if(locate('Rouge',app)>0,1,if(locate('Blanc',app)>0,2,if(locate('Rosé',app)>0,3,0))) as coul,'',7,7,7 
from a_oper o
left join `loc-Cave-Categorie` g on g.Categorie=trim(trim(both 'Rosé' from trim(both 'Blanc' from trim(both 'Rouge' from app))))
where lot<>'' and lot<>'0' and app<>''
group by lot;

update `loc-Cave-Lot` l
join (SELECT distinct o.lot,o.date,o.dg,o.op
FROM a_oper o
JOIN (SELECT lot, min(date) as date 
FROM a_oper GROUP BY lot) 
g ON o.lot=g.lot AND o.date=g.date) 
t on l.lot=t.lot
set l.date=UNIX_TIMESTAMP(t.date);

update `loc-Cave-Lot` l
join (SELECT distinct o.lot,o.date,o.dg,o.op
FROM a_oper o
JOIN (SELECT lot, MAX(date) as date 
FROM a_oper where op='Analyse' GROUP BY lot) 
g ON o.lot=g.lot AND o.date=g.date) 
t on l.lot=t.lot
set l.degre=t.dg;



truncate `loc-Cave-Operation`;

insert into `loc-Cave-Operation` (OperationCuveId,OperationLotId,Date,VolumeReel,TypeId,umod,gmod,omod)
select c.id,l.id,UNIX_TIMESTAMP(o.date),o.vo,
find_in_set(o.op,'Entrée,Retiraison,Nettoyage') as typ,7,7,7
from a_oper o
left join `loc-Cave-Cuve` c on c.Cuve=o.cuve
left join `loc-Cave-Lot` l on l.Lot=o.lot
where o.op in ('Entrée','Retiraison','Nettoyage');

update `loc-Cave-Operation` set TypeId=4,SousTypeId=41 where TypeId=3;
update `loc-Cave-Operation` set TypeId=2,SousTypeId=22 where TypeId=2;
update `loc-Cave-Operation` set TypeId=2,SousTypeId=21 where TypeId=1;



