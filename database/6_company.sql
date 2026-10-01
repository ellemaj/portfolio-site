CREATE TABLE company (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    service1_title VARCHAR(255) NOT NULL,
    service1_description TEXT NOT NULL,
    service2_title VARCHAR(255) NOT NULL,
    service2_description TEXT NOT NULL,
    service3_title VARCHAR(255) NOT NULL,
    service3_description TEXT NOT NULL,
    logo VARCHAR(255) NULL,
    photo1 VARCHAR(255) NULL,
    photo2 VARCHAR(255) NULL
);

INSERT INTO company (
    name,
    description,
    service1_title,
    service1_description,
    service2_title,
    service2_description,
    service3_title,
    service3_description,
    logo,
    photo1,
    photo2
)
VALUES (
    'EvL Tech',

    '<p>EvL Tech is sinds zomer 2026 mijn eigen bedrijf. Onder EvL Tech vallen EvL Lighting, EvL Audio en EvL IT Services.</p><p>Al sinds klein af aan loop ik mee in het audiovisuele bedrijf van mijn vader, en hier heb ik veel ervaring opgedaan om vervolgens nu zelf EvL Tech te starten.</p><p>Ik heb voor verschillende opdrachtgevers al heel veel toffe dingen mogen doen; van versterking bij zakelijke presentaties, tot installeren van vergadersystemen, tot lichtoperator zijn bij optredens.</p>',

    'EvL Lighting',
    'Lichtontwerp, programmering en bediening.',

    'EvL Audio',
    'Versterken van (zakelijke) bijeenkomsten en andere evenementen.',

    'EvL IT Services',
    'Applicatiebeheer, ontwerp en updates.',

    '/assets/images/evl-tech-ondark.svg',
    '/assets/images/musical.jpg',
    '/assets/images/lacanzoniitaliane.jpg'
);
