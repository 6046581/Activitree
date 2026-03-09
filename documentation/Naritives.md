# Use case 1

**Use Case:** `Planning a party`
**Version No:** 0.1

---

## 🎯 End Goal

Has a party planned

---

## 📘 Description

This use case covers how a user and the system interacts while making a party in the app

---

## 👥 Actors

- Actor 1
   - User

---

## ✅ Preconditions

- Condition 1
   - Need to be logged in

---

## 🟢 Basic Flow (Happy Path)

| Step | User Actions                                                                                  | System Actions                                                                                                          |
| ---- | --------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| 1    | clicks on the "+" to create an activity                                                       | creates a space where the user is able to select an activity and tell with how many persons the activity is going to be |
| 2    | selects an activity and the amount of persons who will be attending the activity and the date | The system saves the user actions in the local storage                                                                  |
| 3    | The user click on the "confirm" button to make the activity                                   | The system sends the activity to the database                                                                           |
| 4    |                                                                                               | The system will assign the right staff for the date to the activity.                                                    |

---

## 🟡 Alternate Flow(s)

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

## 🔴 Exception Flow(s)

### 1. Invalid date

| Step | Exception Condition          | System Response                                    |
| ---- | ---------------------------- | -------------------------------------------------- |
| 1    | The user enters a wrong date | The system tells the user that his date is invalid |

---

## 🏁 Post Conditions

Describe the true state of the system after successful completion.

- Result 1
   - Staff is assigned
- Result 2
   - The event is fully organized

# Use Case 2

**Use Case:**  
**Version No:** 0.1

---

## 🎯 End Goal

Describe the final outcome of this use case.

---

## 📘 Description

Brief summary of what this use case covers.

---

## 👥 Actors

- Actor 1
- Actor 2
- Actor 3

---

## ✅ Preconditions

- Condition 1
- Condition 2
- Condition 3

---

## 🟢 Basic Flow (Happy Path)

> The optimal or normal ("good day") flow.  
> No conditional logic.

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |
| 3    |              |                |
| 4    |              |                |

---

## 🟡 Alternate Flow(s)

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |

---

## 🔴 Exception Flow(s)

Identify system and data error conditions.

| Step | Exception Condition | System Response |
| ---- | ------------------- | --------------- |
| 1    |                     |                 |
| 2    |                     |                 |

---

## 🏁 Post Conditions

Describe the true state of the system after successful completion.

- Result 1
- Result 2
- Result 3

# Use Case Specification

**Use Case:**  
**Version No:** 0.1

---

## 🎯 End Goal

Describe the final outcome of this use case.

---

## 📘 Description

Brief summary of what this use case covers.

---

## 👥 Actors

- Actor 1
- Actor 2
- Actor 3

---

## ✅ Preconditions

- Condition 1
- Condition 2
- Condition 3

---

## 🟢 Basic Flow (Happy Path)

> The optimal or normal ("good day") flow.  
> No conditional logic.

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |
| 3    |              |                |
| 4    |              |                |

---

## 🟡 Alternate Flow(s)

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |

---

## 🔴 Exception Flow(s)

Identify system and data error conditions.

| Step | Exception Condition | System Response |
| ---- | ------------------- | --------------- |
| 1    |                     |                 |
| 2    |                     |                 |

---

## 🏁 Post Conditions

Describe the true state of the system after successful completion.

- Result 1
- Result 2
- Result 3

# Use Case Specification

**Use Case:**  
**Version No:** 0.1

---

## 🎯 End Goal

Describe the final outcome of this use case.

---

## 📘 Description

Brief summary of what this use case covers.

---

## 👥 Actors

- Actor 1
- Actor 2
- Actor 3

---

## ✅ Preconditions

- Condition 1
- Condition 2
- Condition 3

---

## 🟢 Basic Flow (Happy Path)

> The optimal or normal ("good day") flow.  
> No conditional logic.

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |
| 3    |              |                |
| 4    |              |                |

---

## 🟡 Alternate Flow(s)

| Step | User Actions | System Actions |
| ---- | ------------ | -------------- |
| 1    |              |                |
| 2    |              |                |

---

## 🔴 Exception Flow(s)

Identify system and data error conditions.

| Step | Exception Condition | System Response |
| ---- | ------------------- | --------------- |
| 1    |                     |                 |
| 2    |                     |                 |

---

## 🏁 Post Conditions

Describe the true state of the system after successful completion.

- Result 1
- Result 2
- Result 3
