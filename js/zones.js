/* Wildlife Interactions — content data (Missions + Éducation)
   Source: Figma file "WI (copie)", page Content (6251:3977)
   - list / hero:  Main component variants (6026:3112) → `bg` + `bgOpacity`
   - detail page:  Detail frames (6251:4242, 6255:7835 …) → `detail`
*/
(() => {
  const SECTIONS_PLACEHOLDER = [
    { tag: 'Tag', title: 'Section nane', text: 'Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed.' },
    { tag: 'Tag', title: 'Section nane', text: 'Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed. Lorem ipsum dolor sit amet consectetur. Rhoncus tellus praesent sit in ultrices id facilisi neque sed.' },
  ];
  const STATS_PLACEHOLDER = [
    { value: '70+', label: 'Labos', text: 'Lorem ipsum dolor sit amet consectetu Lorem ipsum dolor lorem ipsum dolor sit amet' },
    { value: '70+', label: 'Labos', text: 'Lorem ipsum dolor sit amet consectetu Lorem ipsum dolor lorem ipsum dolor sit amet' },
    { value: '70+', label: 'Labos', text: 'Lorem ipsum dolor sit amet consectetu Lorem ipsum dolor lorem ipsum dolor sit amet' },
  ];
  const TALENT = { name: 'Joanne Rodriguez', role: 'Lead Brand Planner', photo: 'assets/img/talent-joanne-rodriguez.jpg', linkedin: 'https://www.linkedin.com/', activities: ['Camargue', 'Les pôles'] };

  // Shared lower part of every detail page (Figma: supplimentaire — placeholder content in the mock)
  window.WI_DETAIL_SHARED = {
    sections: SECTIONS_PLACEHOLDER,
    stats: STATS_PLACEHOLDER,
    talentsTitle: 'Talent WI',
    talents: [TALENT, TALENT, TALENT],
  };

  const e = (title, text) => ({ title, text });
  const s = (value, label, text) => ({ value, label, text });

  window.WI_ZONES = [
    /* ------------------------------ MISSIONS ------------------------------ */
    {
      slug: 'mer-mediterranee', category: 'missions', name: 'Mer Méditerranée',
      summary: 'Surveillance des écosystèmes côtiers et protection des espèces marines endémiques',
      bg: 'assets/img/zone-mer-mediterranee.jpg', bgOpacity: 0.5, image: 'assets/img/zone-mer-mediterranee.jpg',
      detail: {
        lead: "Le Sanctuaire Pelagos, aire marine protégée de 87 500 km² entre France, Italie et Monaco, abrite l'une des plus fortes concentrations de cétacés de Méditerranée, dont environ un millier de rorquals communs. Véritables alliés climatiques, ces baleines participent activement à la séquestration du carbone océanique grâce à la « pompe à baleines ». Pourtant, trafic maritime intense, pollutions et collisions menacent chaque année cet équilibre fragile, dans un contexte de déclin mondial des populations de cétacés.",
        enjeuxImage: 'assets/img/enjeux-mer-mediterranee.jpg',
        enjeux: [
          e('Le rorqual, sentinelle du sanctuaire', "Espèce bio-indicatrice de la santé de l'écosystème pélagique, le rorqual commun révèle par sa présence l'état de santé global de la Méditerranée."),
          e('Une pompe à carbone vivante', 'En se nourrissant et en fertilisant le phytoplancton, chaque grande baleine séquestre en moyenne 33 tonnes de CO₂ au cours de sa vie.'),
          e('Des collisions mortelles', 'Le trafic maritime estival tue chaque année entre 20 et 50 rorquals communs en Méditerranée nord-occidentale, première cause de mortalité non naturelle.'),
          e('Une population mondiale en déclin', "De 4 à 5 millions d'individus avant l'essor de la pêche industrielle, la population mondiale de cétacés est tombée à 1,3 million aujourd'hui."),
        ],
        stats: [
          s('87 500', 'km²', 'Superficie du Sanctuaire Pelagos, aire marine protégée entre France, Italie et Monaco'),
          s('33', 'tonnes de CO₂', 'Carbone séquestré en moyenne par une grande baleine tout au long de sa vie'),
          s('20-50', 'rorquals/an', 'Nombre de rorquals communs tués chaque année par les collisions avec les navires'),
        ],
      },
    },
    {
      slug: 'mer-rouge', category: 'missions', name: 'Mer Rouge',
      summary: 'Étude des récifs coralliens et monitoring de la biodiversité aquatique tropicale',
      bg: 'assets/img/zone-mer-rouge-2.jpg', bgOpacity: 0.35, image: 'assets/img/zone-mer-rouge-2.jpg',
      detail: {
        lead: "Bordée de récifs coralliens parmi les plus résistants au monde, la Mer Rouge est un sanctuaire de biodiversité unique, capable de mieux résister au réchauffement que la majorité des récifs de la planète. Elle abrite une faune exceptionnelle, poissons tropicaux, tortues, requins, dugongs, et représente un espoir précieux pour l'avenir des écosystèmes coralliens mondiaux. Mais cette résilience naturelle est mise à l'épreuve par une pression touristique et industrielle croissante. Préserver la Mer Rouge, c'est préserver un refuge pour le corail de demain.",
        enjeuxImage: 'assets/img/enjeux-mer-rouge.jpg',
        enjeux: [
          e('Des récifs sous surveillance', 'Malgré leur résistance naturelle à la chaleur, les récifs de la Mer Rouge restent vulnérables à la pollution et au développement côtier.'),
          e('Une pression touristique croissante', 'Plongée, tourisme balnéaire et aménagements littoraux exercent une pression grandissante sur des écosystèmes coralliens fragiles.'),
          e('Un trafic maritime stratégique', 'Voie de passage majeure du commerce mondial, la Mer Rouge est exposée aux risques de pollution liés au trafic maritime intense.'),
          e('Un espoir pour les coraux du futur', 'Grâce à leur résistance thermique exceptionnelle, les coraux de la Mer Rouge pourraient jouer un rôle clé dans la restauration des récifs mondiaux.'),
        ],
        stats: [
          s('2 250', 'km', 'Longueur de la Mer Rouge, bordée de récifs coralliens parmi les plus résistants au monde'),
          s('+5', '°C', 'Écart de température supporté par certains coraux sans blanchir, un record de résilience thermique'),
          s('+1 200', 'espèces', "Nombre d'espèces de poissons recensées, dont beaucoup sont uniques à cette mer"),
        ],
      },
    },
    {
      slug: 'polynesie-francaise', category: 'missions', name: 'Polynésie française',
      summary: 'Conservation des atolls et recherche sur les impacts du changement climatique',
      bg: 'assets/img/zone-polynesie-francaise-2.jpg', bgOpacity: 0.6, image: 'assets/img/zone-polynesie-francaise-2.jpg',
      detail: {
        lead: 'Archipel de plus de 100 îles disséminées dans le Pacifique, la Polynésie française concentre une biodiversité marine exceptionnelle : lagons turquoise, récifs coralliens, baleines à bosse et requins. Ce territoire insulaire, particulièrement exposé à la montée des eaux et au réchauffement des océans, incarne les défis auxquels font face les populations du Pacifique. Entre traditions ancestrales et enjeux environnementaux contemporains, la Polynésie française est un territoire clé pour comprendre la résilience des îles face au changement climatique.',
        enjeuxImage: 'assets/img/enjeux-polynesie-francaise.jpg',
        enjeux: [
          e('Des espèces emblématiques à protéger', "Baleines à bosse, requins et tortues marines trouvent en Polynésie un habitat précieux, aujourd'hui fragilisé par la pêche et le tourisme."),
          e('Des lagons menacés', 'Le blanchissement corallien, accéléré par la hausse des températures, fragilise des lagons essentiels à la pêche et à la biodiversité locale.'),
          e('Une montée des eaux préoccupante', "De nombreux atolls polynésiens, situés à quelques mètres au-dessus du niveau de la mer, sont directement menacés par l'élévation du niveau marin."),
          e('Un savoir traditionnel à valoriser', 'Les pratiques ancestrales polynésiennes de gestion des ressources marines offrent des pistes précieuses pour une pêche plus durable.'),
        ],
        stats: [
          s('118', 'îles', "Nombre d'îles réparties sur cinq archipels dispersés dans l'immensité du Pacifique"),
          s('5 millions', 'km²', "Superficie de la zone économique exclusive polynésienne, l'une des plus vastes au monde"),
          s('-2', 'mètres', 'Altitude moyenne de nombreux atolls, directement menacés par la moindre élévation marine.'),
        ],
      },
    },
    {
      slug: 'spitzberg', category: 'missions', name: 'Spitzberg',
      summary: 'Observation de la faune arctique et suivi scientifique de la fonte des glaces',
      bg: 'assets/img/zone-spitzberg-2.jpg', bgOpacity: 0.5, image: 'assets/img/zone-spitzberg-2.jpg',
      detail: {
        lead: "Le Spitzberg s'impose comme le baromètre le plus sensible du changement climatique, avec un réchauffement jusqu'à 6 à 7 fois plus rapide que la moyenne mondiale. Ce phénomène s'auto-amplifie : la fonte des glaces expose des surfaces sombres qui absorbent la chaleur au lieu de la réfléchir, tandis que le dégel du permafrost libère du méthane ancien. Véritable régulateur climatique de l'hémisphère Nord, l'archipel influence jusqu'à la stabilité du Jet Stream. Sa préservation est un enjeu mondial : il abrite le Coffre-fort mondial de semences, garant de la biodiversité alimentaire de l'humanité.",
        enjeuxImage: 'assets/img/enjeux-spitzberg.jpg',
        enjeux: [
          e("Le miroir brisé de l'albédo", "La disparition de la banquise expose des surfaces sombres qui absorbent jusqu'à 90 % de l'énergie solaire, accélérant encore la fonte."),
          e('Un réchauffement hors norme', "Le Svalbard se réchauffe 6 à 7 fois plus vite que la moyenne mondiale, un phénomène d'amplification arctique sans équivalent sur Terre."),
          e('Le permafrost, bombe à retardement', "Le dégel des sols gelés et le retrait des 1 600 glaciers de l'archipel libèrent d'énormes quantités de méthane ancien, un gaz 80 fois plus réchauffant que le CO₂."),
          e('Un régulateur climatique mondial', "En perturbant le Jet Stream, le réchauffement du Spitzberg propage canicules, inondations et vagues de froid jusqu'en Europe et en Amérique du Nord."),
        ],
        stats: [
          s('6-7', 'fois', "Vitesse de réchauffement du Svalbard par rapport à la moyenne mondiale, un record d'amplification arctique"),
          s('1 600', 'glaciers', "Nombre de glaciers de l'archipel, aujourd'hui fragilisés par la fonte accélérée"),
          s('2 310', 'tonnes de méthane/an', 'Estimation des rejets de méthane ancien libérés chaque année par le dégel du sol'),
        ],
      },
    },
    {
      slug: 'groenland', category: 'missions', name: 'Groenland',
      summary: 'Analyse des migrations animales et cartographie des habitats polaires',
      bg: 'assets/img/zone-groenland-2.jpg', bgOpacity: 0.5, image: 'assets/img/zone-groenland-2.jpg',
      detail: {
        lead: "Sentinelle du climat mondial, le Groenland abrite la deuxième plus grande calotte glaciaire de la planète. Sa fonte accélérée, directement liée au réchauffement climatique, contribue de façon majeure à l'élévation du niveau des mers. Terre des Inuits et refuge d'une faune arctique emblématique, ours polaires, narvals, phoques, le Groenland est aujourd'hui le théâtre visible d'un dérèglement global aux conséquences planétaires. Comprendre le Groenland, c'est anticiper l'avenir de nos côtes.",
        enjeuxImage: 'assets/img/enjeux-groenland.jpg',
        enjeux: [
          e('Une calotte glaciaire qui fond', "Le Groenland perd chaque année des milliards de tonnes de glace, un phénomène qui accélère la montée des eaux à l'échelle mondiale."),
          e('Une faune arctique fragilisée', 'Ours polaires, phoques et narvals voient leur habitat de glace se réduire, bouleversant leurs cycles de chasse et de reproduction.'),
          e('Des communautés en première ligne', 'Les populations inuites, dont le mode de vie dépend de la glace et de la mer, doivent s\'adapter à un environnement en mutation rapide.'),
          e('Un rôle climatique global', 'Ce qui se joue au Groenland ne reste pas local : la fonte des glaces influence les courants marins et le climat de toute la planète.'),
        ],
        stats: [
          s('1,7 million', 'km²', "Superficie de l'inlandsis groenlandais, deuxième plus grande calotte glaciaire au monde"),
          s('270 milliards', 'tonnes', 'Quantité de glace perdue chaque année, accélérant la montée du niveau des mers'),
          s('+14', 'mm', 'Hausse du niveau marin mondial déjà imputable à la fonte du Groenland depuis 2000'),
        ],
      },
    },
    {
      slug: 'peninsule-antarctique', category: 'missions', name: 'Péninsule Antarctique',
      summary: "Recherche sur les colonies de manchots et l'acidification des eaux australes",
      bg: 'assets/img/zone-peninsule-antarctique-2.jpg', bgOpacity: 0.5, image: 'assets/img/zone-peninsule-antarctique-2.jpg',
      detail: {
        lead: "La Péninsule Antarctique figure parmi les régions qui se réchauffent le plus rapidement sur Terre. Ses plateformes de glace, en recul accéléré, jouent un rôle crucial dans la régulation du climat mondial et le maintien du niveau des mers. Manchots, phoques et baleines dépendent de cet écosystème polaire d'une richesse exceptionnelle, aujourd'hui fragilisé par la fonte des glaces et la raréfaction du krill. Explorer la Péninsule Antarctique, c'est observer en direct les premiers signaux d'un bouleversement climatique global.",
        enjeuxImage: 'assets/img/enjeux-peninsule-antarctique.jpg',
        enjeux: [
          e('Le krill, maillon vital en danger', 'La raréfaction du krill, base de toute la chaîne alimentaire antarctique, menace manchots, phoques et baleines.'),
          e('Une fonte des glaces accélérée', "Le recul des plateformes glaciaires antarctiques contribue directement à la montée du niveau des mers à l'échelle mondiale."),
          e('Des colonies de manchots fragilisées', "Le réchauffement et la modification de la banquise perturbent la reproduction et l'alimentation de plusieurs espèces de manchots."),
          e('Un rôle climatique planétaire', "Ce qui se joue en Antarctique influence directement les courants océaniques et le climat de l'ensemble de la planète."),
        ],
        stats: [
          s('+3 °C', 'en 50 ans', 'Hausse des températures régionales observée depuis le milieu du XXe siècle'),
          s('3 250', 'km²', 'Superficie de la plateforme de glace Larsen B effondrée en quelques semaines en 2002'),
          s('70', '%', 'Déclin observé dans certaines colonies de manchots Adélie au cours des dernières décennies'),
        ],
      },
    },
    {
      slug: 'camargue', category: 'missions', name: 'Camargue',
      summary: "Protection des zones humides et suivi des populations d'oiseaux migrateurs",
      bg: 'assets/img/zone-camargue-2.jpg', bgOpacity: 0.5, image: 'assets/img/zone-camargue-2.jpg',
      detail: {
        lead: "En première ligne du dérèglement climatique en France, la Camargue voit 70 % de ses terres situées à moins d'un mètre au-dessus du niveau de la mer, subissant une montée des eaux accélérée et un recul inexorable de son trait de côte. Ce delta unique abrite une biodiversité exceptionnelle, dont les flamants roses, ainsi qu'une identité culturelle forte portée par la riziculture et l'élevage traditionnel. Face à cette mutation, la région déploie des « Solutions fondées sur la Nature », restaurant d'anciennes salines pour créer des boucliers naturels face à l'érosion marine.",
        enjeuxImage: 'assets/img/enjeux-camargue.jpg',
        enjeux: [
          e('Le refuge fragile des flamants roses', "L'étang du Fangassier, unique site de ponte en France, voit sa mise en eau devenue aléatoire ; le Parc ornithologique du Pont de Gau agit comme pilier de protection sur 60 hectares."),
          e('La bataille du sel', "La remontée du sel par capillarité menace la riziculture, pourtant essentielle au dessalement naturel des terres grâce à ses apports d'eau douce."),
          e('Une montée des eaux accélérée', 'Le niveau marin a déjà grimpé de 7 cm en 30 ans, provoquant un recul inexorable du trait de côte sur un territoire largement sous le niveau de la mer.'),
          e('Un élevage traditionnel en péril', "La perte des pâturages sous l'effet du sel fragilise l'élevage des taureaux et chevaux, mettant en péril un pan entier de l'identité manadière."),
        ],
        stats: [
          s('70 %', 'terres', "Part du delta camarguais situé à moins d'un mètre au-dessus du niveau de la mer"),
          s('7 cm', 'en 30 ans', 'Élévation du niveau marin déjà enregistrée sur le littoral camarguais'),
          s('60', 'hectares', 'Surface du Parc ornithologique du Pont de Gau, refuge protégé pour les flamants roses'),
        ],
      },
    },

    /* ------------------------------ ÉDUCATION ------------------------------ */
    {
      slug: 'primaire', category: 'education', name: 'Primaire',
      summary: 'Éveil à la biodiversité marine dès le plus jeune âge à travers des ateliers ludiques',
      bg: 'assets/img/edu-primaire.jpg', bgOpacity: 0.5, image: 'assets/img/edu-primaire.jpg',
      detail: {
        lead: "Conçu pour éveiller la curiosité des plus jeunes, notre programme Primaire propose une découverte ludique et sensorielle du monde marin. À travers des ateliers adaptés, des jeux et des supports pédagogiques illustrés, les élèves apprennent à observer, comprendre et respecter la nature. L'objectif : semer dès le plus jeune âge les graines d'une conscience écologique durable, tout en s'amusant et en développant leur esprit d'observation.",
        enjeuxImage: 'assets/img/enjeux-primaire.jpg',
        enjeux: [
          e('Éveiller la curiosité naturelle', "Les enfants découvrent le monde marin à travers des activités ludiques adaptées à leur âge et à leur rythme d'apprentissage."),
          e("Favoriser l'apprentissage par le jeu", "Ateliers créatifs, jeux et supports visuels rendent l'apprentissage vivant, participatif et mémorable pour les élèves."),
          e('Construire des repères simples', 'Des messages clairs et accessibles permettent aux plus jeunes de comprendre les grands enjeux environnementaux sans les effrayer.'),
          e('Semer une conscience écologique durable', "Sensibiliser dès le plus jeune âge, c'est poser les bases d'un rapport respectueux et durable à la nature."),
        ],
        stats: [
          s('+3 000', 'élèves/an', "Nombre d'écoliers sensibilisés chaque année à la découverte du milieu marin"),
          s('50', 'classes', 'Nombre de classes de primaire accompagnées annuellement dans nos interventions scolaires'),
          s('90 %', 'satisfaction', 'Part des enseignants estimant le programme bénéfique à la curiosité de leurs élèves'),
        ],
      },
    },
    {
      slug: 'college', category: 'education', name: 'Collège',
      summary: 'Projets scientifiques participatifs couplant terrain et découverte des écosystèmes marins',
      bg: 'assets/img/edu-college.jpg', bgOpacity: 0.5, image: 'assets/img/edu-college.jpg',
      detail: {
        lead: "Le programme Collège approfondit les connaissances scientifiques des élèves à travers des interventions interactives, des études de cas concrets et des échanges avec des experts du terrain. Il encourage l'esprit critique et l'engagement citoyen en reliant les grands enjeux environnementaux, climat, biodiversité, pollution, à des actions concrètes et accessibles. Ce programme accompagne les collégiens dans la construction d'une réflexion personnelle sur leur rôle face aux défis écologiques actuels.",
        enjeuxImage: 'assets/img/enjeux-college.jpg',
        enjeux: [
          e('Approfondir les connaissances scientifiques', 'Les collégiens explorent des sujets concrets — climat, biodiversité, pollution — à travers des données et des exemples réels de terrain.'),
          e("Développer l'esprit critique", 'Les échanges avec des experts encouragent les élèves à questionner, analyser et se forger leur propre opinion sur les enjeux environnementaux.'),
          e("Encourager l'engagement citoyen", "Le programme invite les élèves à identifier des actions concrètes qu'ils peuvent mener à leur échelle, individuelle ou collective."),
          e('Relier science et société', 'Comprendre les liens entre écosystèmes, activités humaines et changement climatique permet aux collégiens de mieux appréhender le monde qui les entoure.'),
        ],
        stats: [
          s('+1 500', 'collégiens/an', "Nombre d'élèves de collège touchés chaque année par nos ateliers thématiques"),
          s('8', 'établissements partenaires', "Nombre de collèges engagés dans un partenariat pédagogique durable avec l'association"),
          s('12', 'talents mobilisés', 'Nombre de scientifiques intervenant directement auprès des classes pour enrichir les échanges'),
        ],
      },
    },
    {
      slug: 'citizen-pelagos', category: 'education', name: 'Citizen Pelagos',
      summary: 'Programme de science citoyenne permettant au grand public de contribuer',
      bg: 'assets/img/edu-citizen-pelagos.jpg', bgOpacity: 0.5, image: 'assets/img/edu-citizen-pelagos.jpg',
      detail: {
        lead: "Citizen Pelagos invite chacun à devenir acteur de la recherche scientifique en Méditerranée. Plaisanciers, pêcheurs, plongeurs ou simples promeneurs peuvent signaler leurs observations de cétacés, de posidonie ou d'espèces protégées, contribuant ainsi directement à la collecte de données utilisées par les scientifiques du Sanctuaire Pelagos. Ce programme de sciences participatives renforce le lien entre citoyens et chercheurs, tout en développant une culture partagée de la protection du milieu marin.",
        enjeuxImage: 'assets/img/enjeux-citizen-pelagos.jpg',
        enjeux: [
          e('Mobiliser les citoyens du littoral', 'Chacun peut contribuer, à son échelle, à la collecte de données scientifiques utiles à la connaissance et à la protection du milieu marin.'),
          e('Enrichir la recherche scientifique', "Les observations partagées par les citoyens complètent le travail des chercheurs et améliorent le suivi des espèces du Sanctuaire Pelagos."),
          e('Créer du lien entre mer et société', 'Le programme rapproche usagers de la mer et scientifiques autour d\'un objectif commun : mieux connaître pour mieux protéger.'),
          e('Développer une culture de la donnée', 'Chaque signalement contribue à construire une base de connaissances partagée, essentielle au suivi à long terme des écosystèmes marins.'),
        ],
        stats: [
          s('+2 000', 'signalements', "Nombre d'observations transmises par les citoyens depuis le lancement du programme"),
          s('500', 'contributeurs actifs', 'Nombre de plaisanciers et plongeurs participant régulièrement à la collecte de données'),
          s('15', 'espèces suivies', "Nombre d'espèces marines documentées grâce aux signalements citoyens"),
        ],
      },
    },
    {
      slug: 'handicap', category: 'education', name: 'Handicap',
      summary: 'Programmes adaptés pour rendre la découverte des océans accessible à tous',
      bg: 'assets/img/qa-bg.jpg', bgOpacity: 0.5, image: 'assets/img/edu-handicap.jpg',
      detail: {
        lead: 'Convaincue que la sensibilisation à la nature doit être accessible à tous, notre association développe des dispositifs adaptés aux personnes en situation de handicap. Supports tactiles, sonores et visuels, ateliers repensés et formats inclusifs permettent à chacun, quel que soit son handicap, de vivre une expérience de découverte du milieu marin riche et immersive. Ce programme traduit notre engagement pour une éducation à l\'environnement réellement inclusive et ouverte à tous les publics.',
        enjeuxImage: 'assets/img/enjeux-handicap.jpg',
        enjeux: [
          e('Rendre la découverte accessible à tous', 'Des outils tactiles, sonores et visuels permettent à chaque public, quel que soit son handicap, de vivre une expérience immersive et adaptée.'),
          e('Adapter les formats pédagogiques', "Nos ateliers sont repensés pour s'adapter aux différents besoins sensoriels, moteurs ou cognitifs des participants."),
          e("Favoriser l'inclusion et le lien social", "La sensibilisation à l'environnement devient un moment de partage et de rencontre, accessible à tous les publics."),
          e('Construire une éducation vraiment inclusive', "Notre association s'engage à faire évoluer en continu ses outils pédagogiques pour toujours mieux répondre aux besoins de chacun."),
        ],
        stats: [
          s('100 %', 'ateliers adaptables', 'Part des ateliers pouvant être adaptés aux besoins sensoriels, moteurs ou cognitifs'),
          s('+300', 'participants/an', 'Nombre de personnes en situation de handicap accueillies chaque année dans nos programmes'),
          s('5', "formats d'outils inclusifs", 'Nombre de supports développés pour rendre la découverte marine accessible à tous'),
        ],
      },
    },
  ];

  window.WI_CATEGORIES = {
    missions: { label: 'Missions', navLabel: 'MISSIONS', page: 'missions.html' },
    education: { label: 'Éducation', navLabel: 'ÉDUCATION', page: 'education.html' },
  };

  /* Home › Q&A (Figma: Main / Property 1=q&a, 6059:3405) */
  window.WI_FAQ = {
    bg: 'assets/img/qa-bg.jpg',
    bgOpacity: 0.1,
    items: [
      { q: "Qu'est-ce que Wildlife Interactions ?", a: "Wildlife Interactions est une organisation dédiée à la protection de la biodiversité marine, réunissant chercheurs, artistes et éducateurs pour documenter et sensibiliser aux interactions entre la faune sauvage et les sociétés humaines." },
      { q: 'Comment participer à nos missions ?', a: 'Nos missions sont ouvertes à des profils variés : scientifiques, artistes, éducateurs et citoyens engagés. Consultez notre page Missions pour découvrir les projets en cours et trouver celui qui vous correspond. Intéressé(e) ? Contactez-nous !' },
      { q: 'Quels sont les programmes éducatifs disponibles ?', a: 'Nous proposons des programmes adaptés à chaque niveau scolaire ainsi que des initiatives citoyennes comme Citizen Pelagos et des programmes inclusifs pour les personnes en situation de handicap.' },
      { q: 'Comment nous soutenir ?', a: 'Vous pouvez nous soutenir via un don, un partenariat institutionnel ou en rejoignant notre réseau de bénévoles. Chaque contribution contribue directement à nos missions de terrain et à nos programmes éducatifs.' },
      { q: "Comment rejoindre l'équipe de talents ?", a: 'Nous collaborons régulièrement avec des photographes, scientifiques, éducateurs et designers. Envoyez-nous votre profil via la page Contact ou suivez-nous sur LinkedIn pour découvrir les opportunités de collaboration en cours.' },
    ],
  };
})();
