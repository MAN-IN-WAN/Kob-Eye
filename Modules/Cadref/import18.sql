set @annee:='2018';
set @cotis:=40;

truncate kbabtel.`kob-Cadref-Annee`;
insert into kbabtel.`kob-Cadref-Annee` (umod,gmod,omod,Annee,Cotisation,EnCours) values (7,7,7,@annee,@cotis,1);

truncate kbabtel.`kob-Cadref-Jour`;
insert into kbabtel.`kob-Cadref-Jour` (umod,gmod,omod,Id,Jour) values 
(7,7,7,1,'Lundi'),
(7,7,7,2,'Mardi'),
(7,7,7,3,'Mercredi'),
(7,7,7,4,'Jeudi'),
(7,7,7,5,'Vendredi'),
(7,7,7,6,'Samedi'),
(7,7,7,7,'Dimanche');

truncate kbabtel.`kob-Cadref-Vacance`;
insert into kbabtel.`kob-Cadref-Vacance` (umod,gmod,omod,Type,Libelle,DateDebut,DateFin,JourId) values 
(7,7,7,'D','Lundi',unix_timestamp('2018-09-10'),0,1),
(7,7,7,'D','Mardi',unix_timestamp('2018-09-10'),0,2),
(7,7,7,'D','Mercredi',unix_timestamp('2018-09-10'),0,3),
(7,7,7,'D','Jeudi',unix_timestamp('2018-09-10'),0,4),
(7,7,7,'D','Vendredi',unix_timestamp('2018-09-10'),0,5),
(7,7,7,'D','Samedi',unix_timestamp('2019-09-10'),0,6),
(7,7,7,'F','Lundi',unix_timestamp('2019-05-27'),0,1),
(7,7,7,'F','Mardi',unix_timestamp('2019-05-28'),0,2),
(7,7,7,'F','Mercredi',unix_timestamp('2019-06-05'),0,3),
(7,7,7,'F','Jeudi',unix_timestamp('2019-06-06'),0,4),
(7,7,7,'F','Vendredi',unix_timestamp('2019-06-07'),0,5),
(7,7,7,'F','Samedi',unix_timestamp('2019-06-08'),0,6),
(7,7,7,'V','TOUSSAINT',unix_timestamp('2018-10-20'),unix_timestamp('2018-11-04'),0),
(7,7,7,'V','11 Novembre',unix_timestamp('2018-11-11'),0,0),
(7,7,7,'V','NOEL',unix_timestamp('2018-12-22'),unix_timestamp('2019-01-06'),0),
(7,7,7,'V','HIVERT',unix_timestamp('2019-02-23'),unix_timestamp('2019-03-10'),0),
(7,7,7,'V','PRINTEMPS',unix_timestamp('2019-04-20'),unix_timestamp('2019-05-05'),0),
(7,7,7,'V','8 Mai',unix_timestamp('2019-05-08'),0,0),
(7,7,7,'V','ASCENSION',unix_timestamp('2019-05-29'),unix_timestamp('2019-06-03'),0);

truncate kbabtel.`kob-Cadref-Lieu`;
insert into kbabtel.`kob-Cadref-Lieu` (umod,gmod,omod,Type,Lieu,Libelle,GPS) values 
(7,7,7,'L','NIM','Nimes, 249 rue de Bouillagues','43.8335608,4.3692999'),
(7,7,7,'L','AAC','Alès, Espace André Chamson, 2 place Henri Barbusse','44.1248475,4.0773789'),
(7,7,7,'L','AEM','Alès, Ecole des Mines, 6 rue de Clavières','44.1248475,4.0773789'),
(7,7,7,'L','BSG','Bagnols, St Gervais, Salle de la Coquillone',''),
(7,7,7,'L','BMA','Bagnols, Maison des Associations',''),
(7,7,7,'L','GRA','Le Grau du roi, 120 Rue des Médards',''),
(7,7,7,'L','VIG','Le Vigan, Lycée Boulevard Pasteur',''),
(7,7,7,'L','SOM','Sommières, Salle Alexandrie, Espace Lawrence Durrell',''),
(7,7,7,'L','VIL','Villeneuve - Les Angles, Salle Frédéric Mistral, Bd des Frères Carpanédo',''),
(7,7,7,'R','AGD','Alès, Garage Durand','44.0900755,4.0530286'),
(7,7,7,'R','AGR','Alès, Gare Routière','44.1274921,4.081083'),
(7,7,7,'R','CALM','La Calmette, Casino',''),
(7,7,7,'R','NSAUV','Nimes, route de Sauve',''),
(7,7,7,'R','NSEV','Nimes, place Séverine',''),
(7,7,7,'R','NCOST','Nimes, Stade des Costières','');




truncate kbabtel.`kob-Cadref-Profession`;
insert into kbabtel.`kob-Cadref-Profession` (umod,gmod,omod,Profession,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref18.Categories n
where n.`Type`='0';

truncate kbabtel.`kob-Cadref-Cursus`;
insert into kbabtel.`kob-Cadref-Cursus` (umod,gmod,omod,Cursus,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref18.Categories n
where n.`Type`='1';

truncate kbabtel.`kob-Cadref-Situation`;
insert into kbabtel.`kob-Cadref-Situation` (umod,gmod,omod,Situation,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref18.Categories n
where n.`Type`='6';

truncate kbabtel.`kob-Cadref-Commune`;
insert into kbabtel.`kob-Cadref-Commune` (umod,gmod,omod,Commune,CP,Agglomeration)
select 7,7,7,Ville,CP,Agglo
from cadref18.Communes;



truncate kbabtel.`kob-Cadref-Enseignant`;
insert into kbabtel.`kob-Cadref-Enseignant` (umod,gmod,omod,Code,Nom,Prenom,Adresse1,Adresse2,CP,Ville,Telephone1,Telephone2,Notes,Mail)
select 7,7,7,Code,Nom,Prenom,Adr1,Adr2,CP,Ville,Tel1,Tel2,Notes,eMail from cadref18.Enseignants;

truncate kbabtel.`kob-Cadref-Antenne`;
insert into kbabtel.`kob-Cadref-Antenne` (umod,gmod,omod,Antenne,Libelle,Abrege)
select 7,7,7,Antenne,Libelle,Abrege from cadref18.Antennes;

truncate kbabtel.`kob-Cadref-Section`;
insert into kbabtel.`kob-Cadref-Section` (umod,gmod,omod,Section,Libelle)
select 7,7,7,Sect,Libelle from cadref18.Sections;

truncate kbabtel.`kob-Cadref-Discipline`;
insert into kbabtel.`kob-Cadref-Discipline` (umod,gmod,omod,Section,Discipline,CodeDiscipline,Libelle,Visite,Certificat,SectionId)
select 7,7,7,d.Sect,d.Discipline,concat(d.Sect,'.',d.Discipline),d.Libelle,ifnull(d.Visites='O',0),d.Certificat<>0,s.id
from cadref18.Disciplines d
left join kbabtel.`kob-Cadref-Section` s on s.Section=d.Sect;

truncate kbabtel.`kob-Cadref-Niveau`;
insert into kbabtel.`kob-Cadref-Niveau` (umod,gmod,omod,Antenne,Section,Discipline,Niveau,CodeNiveau,Libelle,AntenneId,SectionId,DisciplineId)
select 7,7,7,n.Antenne,n.Sect,n.Discipline,n.Niveau,concat(n.Antenne,'.',n.Sect,'.',n.Discipline,'.',n.Niveau),n.Libelle,a.id,s.id,d.id
from cadref18.Niveaux n
left join kbabtel.`kob-Cadref-Antenne` a on a.Antenne=n.Antenne
left join kbabtel.`kob-Cadref-Section` s on s.Section=n.Sect
left join kbabtel.`kob-Cadref-Discipline` d on d.SectionId=s.Id and d.Discipline=n.Discipline;

truncate kbabtel.`kob-Cadref-Classe`;
insert into kbabtel.`kob-Cadref-Classe` (umod,gmod,omod,CodeClasse,Antenne,Section,Discipline,Niveau,Classe,Annee,
JourId,HeureDebut,HeureFin,Notes,Places,Inscrits,Attentes,Prix,Seances,Lieu,CycleDebut,CycleFin,AntenneId,SectionId,DisciplineId,NiveauId)
select 7,7,7,concat(c.Antenne,'.',c.Sect,'.',c.Discipline,'.',c.Niveau,'.',c.Classe),c.Antenne,c.Sect,c.Discipline,c.Niveau,c.Classe,@annee,
Jour,Debut,Fin,Notes,Places,Inscrits,Attentes,Prix,Seances,Lieu,Date1,Date2,a.id,s.id,d.id,n.id
from cadref18.Classes c
left join kbabtel.`kob-Cadref-Antenne` a on a.Antenne=c.Antenne
left join kbabtel.`kob-Cadref-Section` s on s.Section=c.Sect
left join kbabtel.`kob-Cadref-Discipline` d on d.SectionId=s.Id and d.Discipline=c.Discipline
left join kbabtel.`kob-Cadref-Niveau` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau;

truncate kbabtel.`kob-Cadref-ClasseEnseignants`;
insert into kbabtel.`kob-Cadref-ClasseEnseignants` (umod,gmod,omod,Classe,EnseignantId)
select 7,7,7,n.Id,e.Id
from cadref18.Classes c
left join kbabtel.`kob-Cadref-Classe` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau and n.Classe=c.Classe
left join kbabtel.`kob-Cadref-Enseignant` e on e.Code=c.Ens1
where c.Ens1<>'';
insert into kbabtel.`kob-Cadref-ClasseEnseignants` (umod,gmod,omod,Classe,EnseignantId)
select 7,7,7,n.Id,e.Id
from cadref18.Classes c
left join kbabtel.`kob-Cadref-Classe` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau and n.Classe=c.Classe
left join kbabtel.`kob-Cadref-Enseignant` e on e.Code=c.Ens2
where c.Ens2<>'';



truncate kbabtel.`kob-Cadref-Adherent`;
insert into kbabtel.`kob-Cadref-Adherent` (umod,gmod,omod,Numero,Nom,Prenom,Adresse1,Adresse2,CP,Ville,Telephone1,Telephone2,Notes,Mail,
NotesAnnuelles,Naissance,Inscription,Sexe,ProfessionId,CursusId,SituationId,Adherent,Annee,Etoiles,Origine,Certificat,ClasseId,
Cotisation,Cours,Reglement,Differe,Regularisation)
select 7,7,7,Numero,Nom,Prenom,Adr1,Adr2,CP,Ville,Tel1,Tel2,e.Notes,eMail,
NotesTemp,Naissance,Inscription,Sexe,p.Id,u.Id,s.Id,Adherent,e.Annee,Etoiles,Origine,if(Certificat<@annee,null,unix_timestamp(Certificat)),c.Id,
Cotisation,Montant,Reglement,Differe,Regul
from cadref18.Eleves e
left join kbabtel.`kob-Cadref-Classe` c on c.CodeClasse=concat(substr(e.Delegue,1,1),'.',substr(e.Delegue,2,2),'.',substr(e.Delegue,4,2),'.',substr(e.Delegue,6,1),'.',substr(e.Delegue,7,1))
left join kbabtel.`kob-Cadref-Profession` p on p.Profession=e.Profession
left join kbabtel.`kob-Cadref-Cursus` u on u.Cursus=e.Cursus
left join kbabtel.`kob-Cadref-Situation` s on s.Situation=e.Situation;


truncate kbabtel.`kob-Cadref-AdherentAnnee`;
insert into kbabtel.`kob-Cadref-AdherentAnnee` (umod,gmod,omod,AdherentId,Numero,Annee,NotesAnnuelles,Adherent,ClasseId,
Cotisation,Cours,Reglement,Differe,Regularisation)
select 7,7,7,a.Id,e.Numero,e.Annee,NotesTemp,e.Adherent,c.Id,
e.Cotisation,e.Montant,e.Reglement,e.Differe,e.Regul
from cadref18.Eleves e
inner join kbabtel.`kob-Cadref-Adherent` a on a.Numero=e.Numero
left join kbabtel.`kob-Cadref-Classe` c on c.CodeClasse=concat(substr(e.Delegue,1,1),'.',substr(e.Delegue,2,2),'.',substr(e.Delegue,4,2),'.',substr(e.Delegue,6,1),'.',substr(e.Delegue,7,1))
where e.Annee=@annee;

update `kob-Cadref-AdherentAnnee` a
set a.Cours=(select ifnull(sum(ifnull(Prix-Reduction1-Reduction2,0)),0) from `kob-Cadref-Inscription` i where i.AdherentId=a.AdherentId and i.Annee=@annee and i.Supprime=0),
a.Reglement=(select ifnull(sum(ifnull(Montant,0)),0) from `kob-Cadref-Reglement` r where r.AdherentId=a.AdherentId and r.Annee=@annee and r.Supprime=0 and (r.Differe=0 or r.Encaisse=1)),
a.Differe=(select ifnull(sum(ifnull(Montant,0)),0) from `kob-Cadref-Reglement` r where r.AdherentId=a.AdherentId and r.Annee=@annee and r.Supprime=0 and (r.Differe=1 and r.Encaisse=0))
where a.Annee=@annee;

update kbabtel.`kob-Cadref-AdherentAnnee` a
left join (
select AdherentId,min(DateReglement) as dt
from kbabtel.`kob-Cadref-Reglement`
where Annee=@annee
group by AdherentId
) t on t.AdherentId=a.AdherentId
set a.DateCotisation=t.dt
where a.Annee=@annee;



truncate kbabtel.`kob-Cadref-Inscription`;
insert into kbabtel.`kob-Cadref-Inscription` (umod,gmod,omod,Numero,CodeClasse,Antenne,Annee,DateInscription,Attente,DateAttente,Prix,Reduction1,Reduction2,
Supprime,DateSupprime,AdherentId,ClasseId,Utilisateur)
select 7,7,7,i.Numero,concat(i.Antenne,'.',i.Sect,'.',i.Discipline,'.',i.Niveau,'.',i.Classe),i.Antenne,@annee,if(Creation<@annee,null,unix_timestamp(Creation)),Attente,if(DateAtte<@annee,null,unix_timestamp(DateAtte)),i.Prix,i.Reduction,i.Reduc2,
Supprime,if(DateSuppr<@annee,null,unix_timestamp(DateSuppr)),a.Id,c.Id,i.Utilisateur
from cadref18.Inscriptions i
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=i.Numero
left join kbabtel.`kob-Cadref-Classe` c on c.CodeClasse=concat(i.Antenne,'.',i.Sect,'.',i.Discipline,'.',i.Niveau,'.',i.Classe)
order by i.Numero,i.Code;

truncate kbabtel.`kob-Cadref-Reglement`;
insert into kbabtel.`kob-Cadref-Reglement` (umod,gmod,omod,Numero,Annee,DateReglement,Montant,ModeReglement,Notes,Differe,Encaisse,Supprime,Utilisateur,AdherentId)
select 7,7,7,r.Numero,@annee,unix_timestamp(r.DateRegl),Somme,`Mode`,r.Notes,r.Differe,if(r.Differe,r.Encaisse,1),r.Supprime,r.Utilisateur,a.Id
from cadref18.Reglements r
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=r.Numero
order by r.Numero,r.DateRegl;



truncate kbabtel.`kob-Cadref-Visite`;
insert into kbabtel.`kob-Cadref-Visite` (umod,gmod,omod,Visite,Libelle,Annee,DateVisite,Places,Inscrits,Attentes,Prix,Utilisateur)
select 7,7,7,Visite,Libelle,@annee,unix_timestamp(DateVis),Places,Inscrits,Attentes,Prix1,Utilisateur
from cadref18.Visites;

truncate kbabtel.`kob-Cadref-Reservation`;
insert into kbabtel.`kob-Cadref-Reservation` (umod,gmod,omod,Numero,Visite,Annee,Prix,Reduction,Attente,DateAttente,DateInscription,AdherentId,VisiteId,Utilisateur)
select 7,7,7,r.Numero,r.Visite,@annee,r.Prix,r.Reduction,r.Attente,if(r.DateAtte<@annee,null,unix_timestamp(r.DateAtte)),unix_timestamp(r.Creation),a.Id,v.Id,r.Utilisateur
from cadref18.Reservations r
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=r.Numero
left join kbabtel.`kob-Cadref-Visite` v on v.Visite=r.Visite
order by r.Numero,r.Visite;
