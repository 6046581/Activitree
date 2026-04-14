[[User Stories]]
[[Use Case Diagram]]
[[Visual Design]]
[[Narratives]]
[[Class Diagram]]
[[Progression]]

## Goal

Company X regularly organizes activities for employees, but scheduling has become chaotic due to:

- Poor communication
- Outdated systems
- No central overview
- Double-booked locations
- Unclear participant lists
- Activities that don't match attendee preferences

---

## Core

Technologies and approach:

- **PHP** (OOP)
- **JavaScript** (OOP)
- **Weather API** (JSON from https://weerlive.nl/)
- **Database**
- **GitHub Scrumboard**
- **Documentation** (use case diagram, class diagram, narratives)

The project focuses not only on features, but also on **architecture, OOP principles, and documentation**.

---

## Stakeholder Requirements

### Client

- A user-friendly application
- Ability to create, manage, and view activities
- Distinguish between indoor and outdoor activities
- For outdoor activities: display current weather information
- Calendar functionality

---

### Project Lead

- Robust, clean, and scalable code
- Good code quality and adherence to OOP principles
- Scrumboard in GitHub
- Complete documentation:
	- Use case diagram
	- Class diagram
	- Narratives
- Demonstrate OOP in **both PHP and JavaScript**

---

### End Users

- Simple registration and login
- Search and filters for activities
- Ability to invite external guests (they must register)
- Notifications for changes

---

## Activity Requirements

Each activity must include:

- Title and description
- Date, time, and location
- Type (indoor/outdoor)
- Participant list (including guests)
- Status:
	- Planned
	- Cancelled
	- Completed
- Optional notes
- For outdoor activities: linked weather information

This defines the core of the data model.

---

## Week Plan

### Week 1–2: Preparation

- Create user stories
- Set up the GitHub Scrumboard
- Create the use case diagram
- Implement at least one user story per person
- Initial OOP in PHP
- Basic JS (DOM and forms)

---

### Week 3: Narratives

- Write narratives before implementation
- Build features
- Further DOM manipulation

Narratives must be written before coding.

---

### Week 4: Abstract Classes & API

- Abstract classes in PHP
- Integrate Weather API (fetch JSON via JS)
- Update use case diagram

---

### Week 5: Class Diagram & Modules

- Create a class diagram from the existing code
- Use JavaScript modules
- Apply PHP static methods where appropriate

---

### Week 6: Database & Full OOP in JS

- Database class (Singleton or Dependency Injection)
- JavaScript fully object-oriented
- Improve the class diagram

---

### Week 7–8: Final Deliverable

- Incorporate new requirements
- Update diagrams
- Complete narratives
- Refine the application

---

## Technical Design – Required Relationships

You must demonstrate implementations of these four UML relationships:

1. **Association**
2. **Aggregation**
3. **Composition**
4. **Generalization (inheritance)**

If any relationship is missing from your class diagram, provide a separate PHP file that demonstrates it.

Examples should be logical and meaningful.

---

## Evaluation Criteria

Assessment will be based on:

- Technical functionality:
	- PHP
	- JavaScript
	- Database
	- API integration
- Scrum & GitHub:
	- Scrumboard
	- Planning
	- Transparency
- Documentation:
	- Use case diagram
	- Class diagram
	- Narratives
	- Correct UML relationships

## Key Priorities

Strategically, the project emphasizes:

1. **Correct application of OOP in both PHP and JS**
2. **A well-designed data model**
3. **Documentation before implementation**
4. **Scalable architecture**
5. **Proper demonstration of the four UML relationships**
6. **Correct weather-API integration for outdoor activities**
