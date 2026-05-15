CREATE TABLE posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    preview TEXT NOT NULL,
    content TEXT NOT NULL,
    status TEXT DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO posts (title, slug, preview, content, status, created_at)
VALUES
(
    'Nieuwe update!',
    'update',
    'De website heeft een nieuwe update gehad!',
    '<h2>Nieuwe update!</h2>
    <h3>9 oktober 2025</h3>

    <p>De website heeft een nieuwe update gekregen! <br>
    Door de feedback die ik heb verzameld tijdens de posterpresentaties, ziet de website (en de code) er nu nóg toffer uit! <br>
    Vooral de styling van de website heeft een flinke upgrade gehad. De complete versiegeschiedenis kan je vinden via deze
    <a href="https://github.com/ellemaj/ellemaj.github.io" target="_blank">Github pagina</a>.</p>',
    'published',
    '9 oktober 2025'
),
(
    'Persoonlijke SWOT Analyse',
    'swot-analysis',
    'Mijn persoonlijke SWOT analyse; wat zijn mijn sterke en zwakke punten tijdens de opleiding?',
    '<h2>Persoonlijke SWOT-analyse</h2>
    <h3>11 september 2025</h3>

    <p>Aan het begin van de opleiding heb ik een SWOT analyse van mezelf in moeten vullen: mijn Strenghts,
    Weaknessess, Opportunities en Threats.</p>

    <h4>Strenghts:</h4>
    <ul>
        <li>Hard werken (als het nodig is)</li>
        <li>Andere mensen helpen (soms)</li>
    </ul>

    <h4>Weaknessess:</h4>
    <ul>
        <li>Ik ben slecht in plannen</li>
        <li>Als het te moeilijk wordt geef ik makkelijk op</li>
    </ul>

    <h4>Opportunities:</h4>
    <ul>
        <li>Ik zie kansen om beter te leren samenwerken</li>
        <li>Beter leren plannen</li>
        <li>Meer doorzettingsvermogen</li>
    </ul>

    <h4>Threats</h4>
    <ul>
        <li>Te laat aan iets beginnen, en daardoor op het einde in tijdsnood komen</li>
    </ul>',
    'published',
    '11 september 2025'
),
(
    'ICT-werkveld',
    'ict-werkveld',
    'Hoe ziet het ICT-werkveld er uit? Waar kom ik in terecht als ik dze opleiding afrond?',
    "<h2>Het ICT-werkveld en mijn toekomst na deze studie</h2>
    <h3>10 september 2025</h3>

    <p>Ik heb me voor deze blog verdiept in het ICT werkveld, en wat ik kan verwachten als ik klaar ben met de opleiding.<br>
    Het ICT werkveld is erg breed, en veranderd eigenlijk continu. Bedrijven hebben steeds vaker digitale oplossingen nodig, waardoor er erg veel verschillende functies binnen de ICT-wereld zijn.<br>
    Bijvoorbeeld softwareontwikkelaars die nieuwe apps bouwen, systeembeheerders die ervoor zorgen dat netwerken binnen bedrijven veilig online blijven, security specialisten die zorgen dat bedrijven goed weerbaarzijn tegen cyberaanvallen, enzovoort.<br>
    <br>
    Wanneer ik over 4 jaar mijn HBO-ict studie heb afgerond, denk ik dat er nog steeds genoeg keuze is qua werk. De vraag naar ICT'ers is, en blijft denk ik erg groot.
    Hoewel er in de komende 4 jaar nog veel kan veranderen, ook binnen ICT met de opkomst van bijvoorbeeld AI, denk ik toch dat met de juiste basis ik ver kan komen.</p>",
    'published',
    '10 september 2025'
),
(
    'Feedback',
    'feedback',
    'Welke feedback...?',
    '<h2>Feedback</h2>
    <h3>10 september 2025</h3>
    <p>In de Assignment Specification voor deze opdracht staat dat ik ook een artikel moet schrijven over die
    feedback die ik heb gekregen op de SKC opdracht.<br>
    Maar, ik heb eerlijk gezegd geen idee over welke opdracht dit gaat, en heb ook geen enkele feedback
    daarover gehoord :(</p>',
    'published',
    '10 september 2025'
),
(
    'Mijn programmeerervaring',
    'ervaring',
    'Zoals ik op de home- en profilepage al heb verteld, heb ik al enige codeer en programmeerervaring.',
    '<h2>Programmeerervaring</h2>
    <h3>9 september 2025</h3>

    <p>Zoals ik op de home- en profilepage al heb verteld, heb ik al enige codeer en programmeerervaring.<br>
    Op de middelbare school heb ik 2 jaar informatica gevolgd, en daar heb ik kennisgemaakt met meerdere manieren van coderen, en meerdere programmeertalen.<br>
    2 jaar geleden heb ik dan ook samen met Matthijs al een website gemaakt met HTML en CSS: "Hacken enzo".</p>

    <p>De basiscode van deze website, het "skelet" hebben wij van onze docent gekregen, en dus hebben we niet alles zelf gecodeerd.</p>

    <img src="../images/hackenenzo.png" alt="Website Hacken enzo" width="500" class="blogfoto"><br>

    <p>Hier heb ik niet alleen met HTML & CSS gewerkt, maar ook met SQL en python. Het werken met SQL vond ik erg lastig, en is niet echt voor mij weggelegd, maar python vond ik erg leuk om te doen.
    Toen ik in de eindexamenperiode zat heb ik zelfs een programma met python gemaakt wat uitrekende wat voor cijfer ik moest halen om te slagen.<br>
    Kortom: ik heb de afgelopen jaren al een beetje ervaring opgedaan met coderen en programmeren, en heb het tot nu toe altijd leuk gevonden. Ik ben dus benieuwd wat we hier op de opleiding allemaal gaan doen!</p>',
    'published',
    '9 september 2025'
),
(
    'Studiekeuze & motivatie',
    'studiekeuze',
    'De studie waarvoor ik heb gekozen, en waarom ik hiervoor heb gekozen.',
    "<h2>Studiekeuze</h2>
    <h3>20 november 2024</h3>

    <p>Ik heb gekozen voor de opleiding HBO-ICT aan de HZ (Hogeschool Zeeland).<br>
    De reden dat ik hiervoor heb gekozen, is dat ik altijd al erg geinteresseerd ben geweest in techniek, programmeren, en computers.<br>
    Zoals eerder al genoemd op de <a href='../profile.html'>profile pagina</a> heeft mijn vader een eigen bedrijf in licht en geluid, en ook daar komt programmeren soms bij kijken.<br>
    2 jaar geleden heb ik op de middelbare school het vak informatica gevolgd, en dat vond ik erg leuk om te doen. Hierover meer bij de post <a href='post-programming_experience.html'>programmeerervaring</a>.</p>

    <p>Maar, het feit dat ik informatica zo leuk vond, heeft er uiteindelijk ook tot geleid dat ik voor een ICT studie heb gekozen.
    Eerst twijfelde ik erg wat ik voor opleiding wilde doen: de keuze's lagen tussen de koks/horecaopleiding, een ict opleiding, of een opleiding binnen de evenementensector (bijvoorbeeld audiotechnicus).</p>

    <p>Ik heb meerdere scholen bezocht, maar al vrij snel kwam ik er op uit dat het wel een HBO moest zijn, en geen MBO. Als ik voor een MBO zou kiezen, zou het een beetje voelen alsof ik de laatste 2 jaar havo voor niks had gedaan, en dan zou ik alsnog examens moeten doen in de standaardvakken op die opleiding.</p>

    <p>Ook heb ik meerdere open- en meeloopdagen bezocht, zowel van de HZ als van Avans Breda. Ik twijfelde hierna erg voor welke school ik moest kiezen, maar vanwege de reistijd naar Breda/Middelburg heb ik toch gekozen voor de HZ.</p>

    <p>Na deze 4 jaar hoop ik met een HBO-ICT diploma uit de school te lopen, om dan een baan te kunnen zoeken in het ICT werkveld.</p>",
    'published',
    '20 november 2024'
);
