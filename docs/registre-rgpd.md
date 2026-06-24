# Registre RGPD EventFlow

DPO fictif : Camille Martin, dpo@eventflow.local.

Regle de retention MVP : les comptes inactifs depuis plus de 2 ans sont anonymises par la commande `php bin/console app:anonymize-old-users`, a planifier via un cron en production. Les logs RGPD sont conserves pour garder une preuve des actions de consentement et d'acces.

## 1. Creation de compte

- Finalite : permettre a un utilisateur de creer un compte EventFlow.
- Base legale : consentement et execution de mesures precontractuelles.
- Donnees concernees : email, mot de passe hash, prenom, nom, telephone optionnel, roles, date et version de consentement.
- Destinataires : utilisateur, equipe technique EventFlow.
- Duree de conservation : compte actif, puis anonymisation apres 2 ans d'inactivite ou sur demande.
- Mesures de securite : hash du mot de passe, API HTTPS en production, JWT signe, validation serveur.

## 2. Authentification

- Finalite : securiser l'acces aux fonctionnalites connectees.
- Base legale : execution du contrat de service.
- Donnees concernees : email, mot de passe hash, roles, jeton JWT temporaire.
- Destinataires : utilisateur, API EventFlow.
- Duree de conservation : jeton JWT limite a 1 heure, compte conserve selon la regle de retention.
- Mesures de securite : PasswordHasher Symfony, LexikJWTAuthenticationBundle, routes protegees.

## 3. Gestion des evenements

- Finalite : permettre aux organisateurs de creer, modifier et supprimer des evenements professionnels.
- Base legale : execution du contrat de service.
- Donnees concernees : identifiant organisateur, titre, description, date, lieu, capacite, statut de publication.
- Destinataires : organisateurs, participants, visiteurs pour les evenements publies.
- Duree de conservation : duree de vie de l'evenement, suppression possible par l'organisateur proprietaire.
- Mesures de securite : role `ROLE_ORGANIZER`, voter Symfony proprietaire, API REST authentifiee.

## 4. Inscription aux evenements

- Finalite : permettre aux utilisateurs de s'inscrire a un evenement.
- Base legale : execution du contrat de service.
- Donnees concernees : utilisateur, evenement, date d'inscription, statut pending/confirmed/cancelled.
- Destinataires : utilisateur, organisateur de l'evenement, equipe technique.
- Duree de conservation : pendant la duree du compte et de l'evenement, anonymisation du compte sur demande.
- Mesures de securite : authentification JWT, prevention de double inscription, controle capacite.

## 5. Gestion du consentement

- Finalite : tracer l'acceptation ou le retrait du consentement RGPD.
- Base legale : obligation legale et consentement.
- Donnees concernees : utilisateur, action, date, IP hashee, details, version de consentement.
- Destinataires : utilisateur, DPO fictif, equipe technique autorisee.
- Duree de conservation : selon obligation de preuve, avec compte utilisateur anonymisable.
- Mesures de securite : IP hashee en SHA-256 avec `APP_SECRET`, acces authentifie.

## 5 bis. Preferences cookies

- Finalite : enregistrer les preferences cookies de l'utilisateur connecte.
- Base legale : consentement pour les categories statistiques et marketing.
- Donnees concernees : preference necessaire imposee, statistiques, marketing, date de mise a jour.
- Destinataires : utilisateur, API EventFlow.
- Duree de conservation : pendant la duree du compte, avec choix local cote navigateur pour les visiteurs non connectes.
- Mesures de securite : route `/api/me/cookie-preferences` protegee, preference rattachee uniquement au compte connecte.

## 6. Rectification des donnees

- Finalite : permettre a l'utilisateur de corriger ses informations personnelles.
- Base legale : obligation legale RGPD.
- Donnees concernees : prenom, nom, telephone.
- Destinataires : utilisateur, API EventFlow.
- Duree de conservation : jusqu'a rectification suivante, anonymisation ou suppression.
- Mesures de securite : route `/api/me` protegee, modification limitee aux donnees du compte connecte.

## 7. Anonymisation

- Finalite : supprimer le caractere personnel du compte a la demande de l'utilisateur ou selon la retention.
- Base legale : obligation legale RGPD, droit a l'effacement.
- Donnees concernees : email, prenom, nom, telephone, roles, mot de passe.
- Destinataires : utilisateur, equipe technique.
- Duree de conservation : donnees anonymisees conservees pour integrite statistique et relationnelle.
- Mesures de securite : email remplace par son hash SHA-256, nom/prenom remplaces par `Utilisateur supprimé`, telephone supprime.

## 8. Logs d'acces aux donnees

- Finalite : conserver une trace des consultations et exports de donnees personnelles.
- Base legale : interet legitime et obligation de preuve.
- Donnees concernees : action, timestamp, IP hashee, details, lien utilisateur nullable.
- Destinataires : utilisateur, DPO fictif, equipe technique autorisee.
- Duree de conservation : selon obligation de preuve, avec compte utilisateur anonymisable.
- Mesures de securite : IP hashee, acces utilisateur limite a ses propres logs, relation utilisateur nullable apres anonymisation.
