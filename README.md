# Mindmap Tool

## Overzicht

Deze tool is ontwikkeld met behulp van het PHP framework Laravel. Laravel is een krachtig en elegant webapplicatieframework dat bekend staat om zijn eenvoudige en expressieve syntaxis. Het biedt een uitgebreide set tools die taken zoals routing, authenticatie, sessiebeheer en caching vereenvoudigen. Laravel volgt het Model-View-Controller (MVC) design pattern, wat helpt om een duidelijke scheiding te behouden tussen de logica van de applicatie, de gebruikersinterface en de data.

## Mindmap Module

De mindmap module maakt gebruik van het MVC design pattern binnen Laravel, waardoor je eenvoudig mindmaps kunt aanmaken, bewerken en bekijken. De achterliggende techniek is als volgt:

### Functionaliteit:

1. **Aanmaken en Bewerken van Mindmaps**:
   - Gebruikers kunnen handmatig ideeën toevoegen of de AI-ideeën generator gebruiken om nieuwe concepten te bedenken. Voor een indruk bekijk deze video https://www.dropbox.com/scl/fi/xm26lv7lsfar6lk5th3ki/PixelPartner_Brainstormtool.mp4?rlkey=vl43x2zdjojxgpfmwz4pif0sh&dl=1 .

2. **Genereren van Ideeën**:
   - Bij het klikken op de knop om ideeën te genereren, wordt de `getIdeas()` functie in `Idea.php` aangeroepen.
   - Deze functie doet een beroep op OpenAI, gebaseerd op de reeds bestaande ideeën in de mindmap, om een set van 5 nieuwe ideeën te genereren via de `generateIdeaV2` methode.
   - Nadat deze ideeën zijn gegenereerd, wordt de `generatePrompts` methode aangeroepen. Deze methode genereert per idee een prompt via de OpenAI koppeling met behulp van PHP multicurl requests.
   - Vervolgens wordt de `getImages` methode aangeroepen om via OpenAI afbeeldingen te genereren die overeenkomen met elk idee.
   - De gegenereerde afbeeldingen worden eerst in de database opgeslagen en vervolgens in de storage map. Dit zorgt ervoor dat je de afbeeldingen altijd vanuit de database kunt ophalen, mocht het opslaan in de storage map niet succesvol zijn.

3. **Weergave en Presentatie**:
   - Nadat alle data is gegenereerd, wordt deze gerenderd via een Laravel view en teruggestuurd via een AJAX call om in de frontend te worden weergegeven.

### Integratie van de Mindmap

De mindmap functionaliteit maakt gebruik van een JavaScript bibliotheek die gratis te gebruiken en te installeren is. Dit kan worden geïnstalleerd door `composer install` uit te voeren in het project.

---

Dit overzicht geeft een volledig beeld van hoe de mindmap module werkt binnen het Laravel framework en hoe de integratie met AI en JavaScript de functionaliteit uitbreidt.

### Opzetten van systeem
Stap 1: .env.example kopiëren naar *.env* en aanpassen/aanvullen.
Stap 2: Database connectie opzetten in .env.
Stap 3: *composer install* runnen in de terminal
Stap 4: *npm install* runnen in de terminal
Stap 5: database migreren d.m.v. commando: *php artisan migrate*
Stap 6: *npm run watch* commando uitvoeren in de terminal (voor styling live te editten). *npm run production* voor vaste styling.

### Overig
Bij vragen over het opzetten kun je contact opnemen met Remco Thijssen via remco@wux.nl. Gebruik als onderwerp 'PixelPartner' met eventueel een eigen aanvulling maar in ieder geval zodat ik direct weet waar het over gaat.

# PixelPartner made by Remco Thijssen @ Wux
