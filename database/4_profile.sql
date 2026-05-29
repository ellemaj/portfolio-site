CREATE TABLE profile (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    intro TEXT NOT NULL,
    bio TEXT NOT NULL,
    birthdate TEXT,
    education TEXT,
    experience TEXT,
    skills TEXT,
    traits TEXT NOT NULL,
    github TEXT,
    linkedin TEXT,
    spotify TEXT,
    discord TEXT,
    image TEXT
);

INSERT INTO profile (
    intro,
    bio,
    birthdate,
    education,
    experience,
    skills,
    traits,
    github,
    linkedin,
    spotify,
    discord,
    image
)
VALUES (
    'Ik ben Elmar van Loenhout, HBO-ICT student aan de HZ University of Applied Sciences met interesse in webontwikkeling en evenemententechnologie.',

    'Naast mijn studie werk ik bij de Aldi en help ik regelmatig mee in het audiovisuele bedrijf van mijn vader.',

    '2009-03-09',

    'Huidig: HBO-ICT aan de HZ University of Applied Sciences.
     Voorheen: In 2025 geslaagd voor Havo aan het Ostrea Lyceum.',

    'Werkzaam bij de Aldi als medewerker verkoop, en regelmatig aan het werk als Audiovisuele technicus bij FvL Audio.',

    'PHP|JavaScript|HTML|CSS|Twig|Docker|Git|SQL|Python',

    'Behulpzaam|Probleemoplossend|Leergierig|Teamspeler',

    'https://github.com/ellemaj',

    'https://www.linkedin.com/in/elmar-van-loenhout-48b34039a/',

    'https://open.spotify.com/user/myp7uhtvaupdg3je0cq6et6q0',

    'https://discord.com/users/689099253926068241',

    '/assets/images/20251025_203849.jpg'
);