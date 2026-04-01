# Use case 1

**Use Case:** `Planning a party`
**Version No:** 0.1

---

## End Goal

Has a party planned

---

## Description

This use case covers how a user and the system interacts while making a party in the app

---

## Actors

- Actor 1
   - User

---

## Preconditions

- Condition 1
   - Need to be logged in

---

## Basic Flow (Happy Path)

| Step | User Actions                                                                                  | System Actions                                                                                                          |
| ---- | --------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| 1    | clicks on the "+" to create an activity                                                       | creates a space where the user is able to select an activity and tell with how many persons the activity is going to be |
| 2    | selects an activity and the amount of persons who will be attending the activity and the date | The system saves the user actions in the local storage                                                                  |
| 3    | The user click on the "confirm" button to make the activity                                   | The system sends the activity to the database                                                                           |
| 4    |                                                                                               | The system will assign the right staff for the date to the activity.                                                    |

---

## Alternate Flow(s)

### 1. outdoor

| Step | User Actions                         | System Actions                                                                                               |
| ---- | ------------------------------------ | ------------------------------------------------------------------------------------------------------------ |
| 1    | The user selects an outdoor activety | The system shows the weather for that date and location and adds a warning "weather can effect the activety" |

### 2. canceling activity

| Step | User Actions                                           | System Acitons                                                                                                     |
| ---- | ------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------ |
| 1    | The user cancelled the progress of making the activity | The system ask for conformation                                                                                    |
| 2    | The user agrees                                        | The system deletes the saves of the previous change made to the activity form the local storage of the user device |

---

## Exception Flow(s)

### 1. Invalid date

| Step | Exception Condition          | System Response                                    |
| ---- | ---------------------------- | -------------------------------------------------- |
| 1    | The user enters a wrong date | The system tells the user that his date is invalid |

---

## Post Conditions

Describe the true state of the system after successful completion.

- Result 1
   - Staff is assigned
- Result 2
   - The event is fully organized

---
# Use case 2

**Use Case:** `Joining an activity`  
**Version No:** 0.1

---

## End Goal

User successfully joins an activity

---

## Description

This use case covers how a user joins an existing activity in the app

---

## Actors

- Actor 1
    
    - User
        

---

## Preconditions

- Condition 1
    
    - Need to be logged in
        
- Condition 2
    
    - Activity must exist
        

---

## Basic Flow (Happy Path)

|Step|User Actions|System Actions|
|---|---|---|
|1|The user opens the activity overview|The system shows a list of available activities|
|2|The user selects an activity|The system shows activity details|
|3|The user clicks on "Join activity"|The system adds the user to the participant list|
|4||The system updates the number of participants|

---

## Alternate Flow(s)

### 1. Activity full

|Step|User Actions|System Actions|
|---|---|---|
|1|The user tries to join a full activity|The system shows a message that the activity is full|

### 2. User cancels joining

|Step|User Actions|System Actions|
|---|---|---|
|1|The user clicks join activity|The system asks for confirmation|
|2|The user cancels|The system stops the joining process|

---

## Exception Flow(s)

### 1. Activity not found

|Step|Exception Condition|System Response|
|---|---|---|
|1|The activity does not exist anymore|The system shows an error message|

---

## Post Conditions

- Result 1
    
    - User is added to the activity
        
- Result 2
    
    - Participant list is updated

--- 

# Use case 3

**Use Case:** `Editing an activity`  
**Version No:** 0.1

---

## End Goal

Activity information is updated

---

## Description

This use case covers how a user edits an existing activity

---

## Actors

- Actor 1
    
    - User
        
- Actor 2
    
    - System
        

---

## Preconditions

- Condition 1
    
    - User must be logged in
        
- Condition 2
    
    - User must be the creator of the activity
        

---

## Basic Flow (Happy Path)

|Step|User Actions|System Actions|
|---|---|---|
|1|The user opens their activity|The system loads the activity information|
|2|The user clicks on edit activity|The system shows editable fields|
|3|The user changes the activity details|The system saves changes in local storage|
|4|The user clicks confirm|The system updates the activity in the database|

---

## Alternate Flow(s)

### 1. Changing date

|Step|User Actions|System Actions|
|---|---|---|
|1|The user changes the date|The system checks staff availability|
|2||The system assigns new staff if needed|

### 2. Cancel editing

|Step|User Actions|System Actions|
|---|---|---|
|1|The user cancels editing|The system asks for confirmation|
|2|The user confirms|The system discards the changes|

---

## Exception Flow(s)

### 1. No permission

|Step|Exception Condition|System Response|
|---|---|---|
|1|User is not the creator|The system denies access to edit|

---

## Post Conditions

- Result 1
    
    - Activity is updated
        
- Result 2
    
    - Database contains new activity information

---

# Use case 4

**Use Case:** `Deleting an activity`  
**Version No:** 0.1

---

## End Goal

Activity is deleted from the system

---

## Description

This use case covers how a user deletes an activity from the app

---

## Actors

- Actor 1
    
    - User
        

---

## Preconditions

- Condition 1
    
    - User must be logged in
        
- Condition 2
    
    - User must be the creator of the activity
        

---

## Basic Flow (Happy Path)

|Step|User Actions|System Actions|
|---|---|---|
|1|The user opens their activity|The system shows activity details|
|2|The user clicks delete activity|The system asks for confirmation|
|3|The user confirms deletion|The system deletes the activity from the database|
|4||The system removes all participants from the activity|

---

## Alternate Flow(s)

### 1. User cancels deletion

|Step|User Actions|System Actions|
|---|---|---|
|1|The user clicks delete|The system asks for confirmation|
|2|The user cancels|The system keeps the activity|

---

## Exception Flow(s)

### 1. Activity already deleted

|Step|Exception Condition|System Response|
|---|---|---|
|1|Activity not found|The system shows an error message|

---

## Post Conditions

- Result 1
    
    - Activity is removed from the system
        
- Result 2
    
    - Participants are notified about cancellation