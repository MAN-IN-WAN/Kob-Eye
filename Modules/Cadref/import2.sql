
set @annee:=2017;
set @cotis:=40;

insert ignore into kbabtel.`kob-Cadref-Annee` (Annee,Cotisation,EnCours) values (@annee,@cotis,0);

truncate kbabtel.`kob-Cadref-Jour`;
insert into kbabtel.`kob-Cadref-Jour` (umod,gmod,omod,Id,Jour) values 
(7,7,7,1,'Lundi'),
(7,7,7,2,'Mardi'),
(7,7,7,3,'Mercredi'),
(7,7,7,4,'Jeudi'),
(7,7,7,5,'Vendredi'),
(7,7,7,6,'Samedi'),
(7,7,7,7,'Dimanche');


truncate kbabtel.`kob-Cadref-Enseignant`;
insert into kbabtel.`kob-Cadref-Enseignant` (umod,gmod,omod,Code,Nom,Prenom,Adresse1,Adresse2,CP,Ville,Telephone1,Telephone2,Notes,Mail)
select 7,7,7,Code,Nom,Prenom,Adr1,Adr2,CP,Ville,Tel1,Tel2,Notes,eMail from cadref17.Enseignants;

truncate kbabtel.`kob-Cadref-Antenne`;
insert into kbabtel.`kob-Cadref-Antenne` (umod,gmod,omod,Antenne,Libelle,Abrege)
select 7,7,7,Antenne,Libelle,Abrege from cadref17.Antennes;

truncate kbabtel.`kob-Cadref-Section`;
insert into kbabtel.`kob-Cadref-Section` (umod,gmod,omod,Section,Libelle)
select 7,7,7,Sect,Libelle from cadref17.Sections;

truncate kbabtel.`kob-Cadref-Discipline`;
insert into kbabtel.`kob-Cadref-Discipline` (umod,gmod,omod,Section,Discipline,CodeDiscipline,Libelle,Visite,Certificat,SectionId)
select 7,7,7,d.Sect,d.Discipline,concat(d.Sect,'.',d.Discipline),d.Libelle,ifnull(d.Visites='O',0),d.Certificat<>0,s.id
from cadref17.Disciplines d
left join kbabtel.`kob-Cadref-Section` s on s.Section=d.Sect;

truncate kbabtel.`kob-Cadref-Niveau`;
insert into kbabtel.`kob-Cadref-Niveau` (umod,gmod,omod,Antenne,Section,Discipline,Niveau,CodeNiveau,Libelle,AntenneId,SectionId,DisciplineId)
select 7,7,7,n.Antenne,n.Sect,n.Discipline,n.Niveau,concat(n.Antenne,'.',n.Sect,'.',n.Discipline,'.',n.Niveau),n.Libelle,a.id,s.id,d.id
from cadref17.Niveaux n
left join kbabtel.`kob-Cadref-Antenne` a on a.Antenne=n.Antenne
left join kbabtel.`kob-Cadref-Section` s on s.Section=n.Sect
left join kbabtel.`kob-Cadref-Discipline` d on d.SectionId=s.Id and d.Discipline=n.Discipline;

truncate kbabtel.`kob-Cadref-Classe`;
insert into kbabtel.`kob-Cadref-Classe` (umod,gmod,omod,CodeClasse,Antenne,Section,Discipline,Niveau,Classe,Annee,
JourId,HeureDebut,HeureFin,Notes,Places,Inscrits,Attentes,Prix,Seances,Lieu,CycleDebut,CycleFin,AntenneId,SectionId,DisciplineId,NiveauId)
select 7,7,7,concat(c.Antenne,'.',c.Sect,'.',c.Discipline,'.',c.Niveau,'.',c.Classe),c.Antenne,c.Sect,c.Discipline,c.Niveau,c.Classe,'2017',
Jour,Debut,Fin,Notes,Places,Inscrits,Attentes,Prix,Seances,Lieu,Date1,Date2,a.id,s.id,d.id,n.id
from cadref17.Classes c
left join kbabtel.`kob-Cadref-Antenne` a on a.Antenne=c.Antenne
left join kbabtel.`kob-Cadref-Section` s on s.Section=c.Sect
left join kbabtel.`kob-Cadref-Discipline` d on d.SectionId=s.Id and d.Discipline=c.Discipline
left join kbabtel.`kob-Cadref-Niveau` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau;

truncate kbabtel.`kob-Cadref-ClasseEnseignants`;
insert into kbabtel.`kob-Cadref-ClasseEnseignants` (umod,gmod,omod,Classe,EnseignantId)
select 7,7,7,n.Id,e.Id
from cadref17.Classes c
left join kbabtel.`kob-Cadref-Classe` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau and n.Classe=c.Classe
left join kbabtel.`kob-Cadref-Enseignant` e on e.Code=c.Ens1
where c.Ens1<>'';
insert into kbabtel.`kob-Cadref-ClasseEnseignants` (umod,gmod,omod,Classe,EnseignantId)
select 7,7,7,n.Id,e.Id
from cadref17.Classes c
left join kbabtel.`kob-Cadref-Classe` n on n.Antenne=c.Antenne and n.Section=c.Sect and n.Discipline=c.Discipline and n.Niveau=c.Niveau and n.Classe=c.Classe
left join kbabtel.`kob-Cadref-Enseignant` e on e.Code=c.Ens2
where c.Ens2<>'';


truncate kbabtel.`kob-Cadref-Profession`;
insert into kbabtel.`kob-Cadref-Profession` (umod,gmod,omod,Profession,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref17.Categories n
where n.`Type`='0';

truncate kbabtel.`kob-Cadref-Cursus`;
insert into kbabtel.`kob-Cadref-Cursus` (umod,gmod,omod,Cursus,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref17.Categories n
where n.`Type`='1';

truncate kbabtel.`kob-Cadref-Situation`;
insert into kbabtel.`kob-Cadref-Situation` (umod,gmod,omod,Situation,Libelle)
select 7,7,7,n.Categorie,n.Libelle
from cadref17.Categories n
where n.`Type`='6';

truncate kbabtel.`kob-Cadref-Adherent`;
insert into kbabtel.`kob-Cadref-Adherent` (umod,gmod,omod,Numero,Nom,Prenom,Adresse1,Adresse2,CP,Ville,Telephone1,Telephone2,Notes,Mail,
NotesAnnuelles,Naissance,Inscription,Sexe,ProfessionId,CursusId,SituationId,Adherent,Annee,Etoiles,Origine,Certificat,ClasseId,
Cotisation,Cours,Reglement,Differe,Regularisation)
select 7,7,7,Numero,Nom,Prenom,Adr1,Adr2,CP,Ville,Tel1,Tel2,e.Notes,eMail,
NotesTemp,Naissance,Inscription,Sexe,p.Id,u.Id,s.Id,Adherent,e.Annee,Etoiles,Origine,Certificat,c.Id,
Cotisation,Montant,Reglement,Differe,Regul)
from cadref17.Eleves e
left join kbabtel.`kob-Cadref-Classe` c on c.CodeClasse=concat(substr(e.Delegue,1,1),'.',substr(e.Delegue,2,2),'.',substr(e.Delegue,4,2),'.',substr(e.Delegue,6,1),'.',substr(e.Delegue,7,1))
left join kbabtel.`kob-Cadref-Profession` p on p.Profession=e.Profession
left join kbabtel.`kob-Cadref-Cursus` u on u.Cursus=e.Cursus
left join kbabtel.`kob-Cadref-Situation` s on s.Situation=e.Situation;

truncate kbabtel.`kob-Cadref-Commune`;
insert into kbabtel.`kob-Cadref-Commune` (umod,gmod,omod,Commune,CP,Agglomeration)
select 7,7,7,Ville,CP,Agglo
from cadref17.Communes;

truncate kbabtel.`kob-Cadref-Inscription`;
insert into kbabtel.`kob-Cadref-Inscription` (umod,gmod,omod,Numero,CodeClasse,Antenne,Annee,DateInscription,Attente,DateAttente,Prix,Reduction1,Reduction2,Supprime,DateSupprime,AdherentId,ClasseId)
select 7,7,7,i.Numero,concat(i.Antenne,'.',i.Sect,'.',i.Discipline,'.',i.Niveau,'.',i.Classe),i.Antenne,'2017',if(DateInscr<'2017',null,unix_timestamp(DateInscr)),Attente,if(DateAtte<'2017',null,unix_timestamp(DateAtte)),i.Prix,i.Reduction,i.Reduc2,
Supprime,if(DateSuppr<@annee,null,unix_timestamp(DateSuppr)),a.Id,c.Id
from cadref17.Inscriptions i
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=i.Numero
left join kbabtel.`kob-Cadref-Classe` c on c.CodeClasse=concat(i.Antenne,'.',i.Sect,'.',i.Discipline,'.',i.Niveau,'.',i.Classe)
order by i.Numero,i.Code;

truncate kbabtel.`kob-Cadref-Reglement`;
insert into kbabtel.`kob-Cadref-Reglement` (umod,gmod,omod,Numero,Annee,DateReglement,Montant,ModeReglement,Notes,Differe,Encaisse,Supprime,Utilisateur,AdherentId)
select 7,7,7,r.Numero,@annee,unix_timestamp(r.DateRegl),Somme,`Mode`,r.Notes,r.Differe,r.Encaisse,r.Supprime,Utilisateur,a.Id
from cadref17.Reglements r
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=r.Numero
order by r.Numero,r.DateRegl;



truncate kbabtel.`kob-Cadref-Visite`;
insert into kbabtel.`kob-Cadref-Visite` (umod,gmod,omod,Visite,Libelle,Annee,DateVisite,Places,Inscrits,Attentes,Prix1,Prix2,Prix3)
select 7,7,7,Visite,Libelle,@annee,unix_timestamp(DateVis),Places,Inscrits,Attentes,Prix1,Prix2,Prix3
from cadref17.Visites;

truncate kbabtel.`kob-Cadref-Reservation`;
insert into kbabtel.`kob-Cadref-Reservation` (umod,gmod,omod,Numero,Visite,Annee,Prix,Reduction,Attente,DateAttente,DateInscription,AdherentId,VisiteId)
select 7,7,7,r.Numero,r.Visite,@annee,Prix,Reduction,Attente,if(DateAtte<'2017',null,unix_timestamp(DateAtte)),unix_timestamp(Creation),a.Id,v.Id
from cadref17.Reservations r
left join kbabtel.`kob-Cadref-Adherent` a on a.Numero=r.Numero
left join kbabtel.`kob-Cadref-Visite` v on v.Visite=r.Visite
order by r.Numero,r.Visite;
