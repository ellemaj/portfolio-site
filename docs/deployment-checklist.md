# Deploy-checklist: Dashboard/FAQ weg + contact-pagina

Stappenplan voor als je deze wijzigingen naar `main` pusht en dus live naar Strato gaan. Los van de code (die gaat automatisch via de FTP-pipeline), zijn er twee dingen die **niet** automatisch gebeuren: de productiedatabase en de mail-configuratie.

## 1. Database op Strato

De automatische deploy (GitHub Actions → FTP) upload alleen bestanden, geen database-wijzigingen. Je productiedatabase heeft dus nog steeds de `courses`-tabel staan, met de echte data van vroeger.

- [ ] Log in op **phpMyAdmin** via het Strato control panel, open je database.
- [ ] Draai: `DROP TABLE IF EXISTS courses;`
- [ ] Controleer dat de overige tabellen (`users`, `posts`, `profile`, `projects`) er nog gewoon staan.

Dit is dezelfde stap die we lokaal in Docker al hebben gedaan, nu voor productie. Zonder deze stap breekt er niets (de code raakt `courses` nergens meer aan), maar de tabel blijft dan als nutteloze restdata staan.

## 2. Mail-configuratie voor het contactformulier

Het contactformulier verstuurt mail via PHP's ingebouwde `mail()`. De `.env` op Strato wordt nooit door de deploy-pipeline aangeraakt (staat expliciet in de README), dus dit moet je zelf toevoegen.

- [ ] Verbind via FTP met de server en open het bestaande `.env` bestand.
- [ ] Voeg een regel toe: `CONTACT_EMAIL=elmarvloenhout@gmail.com` (of een ander adres als je dat liever hebt).
- [ ] Sla op.

**Daarna testen:**
- [ ] Open `https://elmarvanloenhout.nl/contact` en verstuur een test-bericht naar jezelf.
- [ ] Check je inbox **en** je spamfolder. Strato's gedeelde hosting ondersteunt `mail()` meestal wel, maar aflevering bij Gmail/Outlook kan alsnog in spam belanden omdat er geen SPF/DKIM voor het afzenderadres (`noreply@elmarvanloenhout.nl`) is ingesteld.
- [ ] Komt de mail niet aan, ook niet in spam? Dan ondersteunt Strato `mail()` mogelijk niet goed voor dit domein, en is de volgende stap een SMTP-package (bijv. Symfony Mailer) met de SMTP-gegevens van je mailprovider. Dat is een aparte vervolgtaak, geen quick fix.

## 3. Voor de push zelf

- [ ] Draai lokaal de drie CI-checks die ook in de GitHub Actions pipeline zitten, zodat de deploy niet blokkeert:
  ```bash
  vendor/bin/phpstan analyse --no-progress
  vendor/bin/phpcs
  vendor/bin/deptrac analyse --no-progress
  ```
- [ ] Commit en push naar `main` (of eerst naar `development` en dan een PR/merge, afhankelijk van je eigen workflow).
- [ ] Volg de **Actions**-tab op GitHub tot de deploy groen is.

## Niet meegenomen in deze checklist

- Het opschonen van bestaande (school-gerelateerde) blogposts: dat doe je zelf via de "Beheren"-knop op de site, geen deploy-actie.
- Extra contact-links (Instagram, X, etc.) en de bedrijfspagina: aparte taken voor later.
