[[User Stories]] 
[[Use Case Diagram]]
[[Class Diagram]]

# Doel

Bedrijf X organiseert regelmatig activiteiten voor medewerkers, maar de planning verloopt chaotisch door:
- Slechte communicatie
- Verouderde systemen
- Geen centraal overzicht
- Dubbel geboekte locaties
- Onduidelijke deelnemerslijsten
- Activiteiten die niet aansluiten bij voorkeuren

---
# Kern
Gebruik:
- **PHP** (OOP)
- **JavaScript** (OOP)
- **Weer-API** (JSON van https://weerlive.nl/)
- **Database**
- **Scrumboard in GitHub**
- **Documentatie** (use case diagram, class diagram, narratives)

Belangrijk: Het project draait niet alleen om functionaliteit, maar ook om **architectuur, OOP-principes en documentatie**.

---

# Stakeholders Eisen

## Klant
- Gebruiksvriendelijke applicatie
- Activiteiten kunnen aanmaken, beheren en bekijken
- Onderscheid tussen binnen- en buitenactiviteiten
- Voor buitenactiviteiten: actuele weersinformatie
- Kalenderfunctie

---

## Projectleider

- Robuuste, nette en schaalbare code
- Goede codekwaliteit en OOP-principes
- Scrumboard in GitHub
- Volledige documentatie:
- Use case diagram
- Class diagram
- Narratives
- Toepassing van OOP in **zowel PHP als JavaScript**


---

## Eindgebruikers

- Eenvoudige registratie en login
- Zoekfunctie / filters voor activiteiten
- Externe gasten kunnen uitnodigen (moeten zich registreren)
- Meldingen bij wijzigingen

---

# Activiteitseisen

Elke activiteit moet bevatten:
- Titel + beschrijving
- Datum, tijd, locatie
- Type (binnen/buiten)
- Deelnemerslijst (incl. gasten)
- Status:
- Gepland
- Geannuleerd
- Voltooid
- Optionele notities
- Bij buitenactiviteiten: gekoppelde weersinformatie

Dit vormt de kern van je datamodel.

---

# Weekplanning

## Week 1–2: Voorbereiding

- User stories maken
- Scrumboard opzetten
- Use case diagram maken
- Minimaal 1 user story per persoon realiseren
- Eerste OOP in PHP
- Basis JS (DOM + forms)


---

## Week 3: Narratives

- Narratives schrijven vóór implementatie

- Functionaliteiten bouwen

- Verdere DOM-manipulatie


👉 Narratives zijn verplicht vóór coderen.

---

## Week 4: Abstracte classes + API

- Abstracte classes in PHP

- Weer-API koppelen (JSON ophalen via JS)

- Use case diagram bijwerken


---

## Week 5: Class Diagram + Modules

- Class diagram maken van bestaande code

- JavaScript modules gebruiken

- PHP static methods toepassen


---

## Week 6: Database + Volledige OOP JS

- Database class (Singleton of Dependency Injection)

- JS volledig OOP

- Class diagram verbeteren


---

## Week 7–8: Eindopdracht

- Nieuwe eisen verwerken

- Diagrams aanpassen

- Narratives schrijven

- Applicatie verfijnen


---

# 🧠 Technisch Ontwerp – Verplichte Relaties

Je moet aantonen dat je deze 4 UML-relaties kunt implementeren:

1. **Associatie**

2. **Aggregatie**

3. **Compositie**

4. **Generalisatie (overerving)**


⚠️ Als één relatie ontbreekt in je class diagram, moet je een apart PHP-bestand maken waarin je die relatie alsnog demonstreert.

Voorbeelden moeten logisch zijn (geen onzinnige relaties).

---

# 📝 Beoordeling

Je wordt beoordeeld op:

### 🔹 Technische werking

- PHP

- JavaScript

- Database

- API-integratie


### 🔹 Scrum & GitHub

- Scrumboard

- Planning

- Transparantie


### 🔹 Documentatie

- Use case diagram

- Class diagram

- Narratives

- Correcte UML-relaties


### 🔹 Samenwerking & Presentatie

---

# ⚡ Wat is écht het belangrijkste?

Als je het project strategisch bekijkt, draait alles om:

1. **Correcte toepassing van OOP in PHP én JS**

2. **Een goed doordacht datamodel**

3. **Documentatie vóór implementatie**

4. **Schaalbare architectuur**

5. **De 4 verplichte UML-relaties correct aantonen**

6. **Weer-API correct integreren bij buitenactiviteiten**


Dit is geen simpele CRUD-opdracht — het is een combinatie van:

- Webdevelopment

- Software architectuur

- Scrum

- Documentatie

- OOP-principes


---

Als je wilt, kan ik ook:

- Een architectuurvoorstel maken

- Een mogelijke class-structuur schetsen

- Helpen met een datamodel

- De UML-relaties concreet uitwerken voor dit project

- Of het project analyseren vanuit een software-architectuur perspectief


Wat wil je als volgende stap?