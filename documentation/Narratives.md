# Use Case 1 — Planning an Activity

**Use Case:** `Planning an activity`
**Version:** 0.1

---

## End Goal

An activity is planned successfully.

---

## Description

This use case describes how a user interacts with the system to plan an activity.

---

## Actors

- User

---

## Preconditions

- The user must be logged in.

---

## Basic Flow (Happy Path)

| Step | User Actions                                                                                  | System Actions                                                                                                          |
| ---- | --------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| 1    | Clicks the "+" button to create an activity                                                 | Shows a form where the user selects the activity type, date, time, and number of attendees                              |
| 2    | Selects activity type, attendee count, and date                                               | Saves the input in local storage                                                                                        |
| 3    | Clicks the "Confirm" button                                                                  | Sends the activity to the backend (database)                                                                            |
| 4    |                                                                                               | The system assigns appropriate staff/resources for the date if applicable                                               |

---

## Alternate Flow(s)

### 1. Outdoor activity

| Step | User Actions                         | System Actions                                                                                               |
| ---- | ------------------------------------ | ------------------------------------------------------------------------------------------------------------ |
| 1    | Selects an outdoor activity          | Shows weather information for the selected date and location and displays a warning about weather impact    |

### 2. Cancelling activity creation

| Step | User Actions                                           | System Actions                                                                                                     |
| ---- | ------------------------------------------------------ | ------------------------------------------------------------------------------------------------------------------ |
| 1    | Cancels the activity creation process                  | Prompts for confirmation                                                                                           |
| 2    | Confirms cancellation                                   | Clears any saved draft data from local storage                                                                     |

---

## Exception Flow(s)

### 1. Invalid date

| Step | Exception Condition          | System Response                                    |
| ---- | ---------------------------- | -------------------------------------------------- |
| 1    | User enters an invalid date  | Shows an error indicating the date is invalid       |

---

## Post Conditions

- Staff/resources are assigned if applicable.
- The event is stored in the system.

---

# Use Case 2 — Joining an Activity

**Use Case:** `Joining an activity`
**Version:** 0.1

---

## End Goal

The user successfully joins an activity.

---

## Description

This use case describes how a user joins an existing activity.

---

## Actors

- User

---

## Preconditions

- The user must be logged in.
- The activity must exist.

---

## Basic Flow (Happy Path)

| Step | User Actions                         | System Actions                                   |
| ---- | ------------------------------------ | ------------------------------------------------ |
| 1    | Opens the activity overview          | Shows a list of available activities             |
| 2    | Selects an activity                  | Displays activity details                        |
| 3    | Clicks "Join activity"              | Adds the user to the participant list            |
| 4    |                                      | Updates the participant count                    |

---

## Alternate Flow(s)

### 1. Activity is full

| Step | User Actions                           | System Actions                                       |
| ---- | -------------------------------------- | ---------------------------------------------------- |
| 1    | Attempts to join a full activity       | Shows a message that the activity is full           |

### 2. User cancels joining

| Step | User Actions                  | System Actions                       |
| ---- | ----------------------------- | ------------------------------------ |
| 1    | Clicks "Join activity"       | Prompts for confirmation               |
| 2    | Cancels                       | Aborts the joining process             |

---

## Exception Flow(s)

### 1. Activity not found

| Step | Exception Condition                 | System Response                   |
| ---- | ----------------------------------- | --------------------------------- |
| 1    | Activity no longer exists           | Shows an error message            |

---

## Post Conditions

- The user is added to the activity.
- The participant list is updated.

---

# Use Case 3 — Editing an Activity

**Use Case:** `Editing an activity`
**Version:** 0.1

---

## End Goal

Activity details are updated.

---

## Description

This use case describes how a user edits an existing activity.

---

## Actors

- User
- System

---

## Preconditions

- The user must be logged in.
- The user must be the creator of the activity.

---

## Basic Flow (Happy Path)

| Step | User Actions                          | System Actions                                  |
| ---- | ------------------------------------- | ----------------------------------------------- |
| 1    | Opens their activity                  | Loads the activity information                  |
| 2    | Clicks "Edit activity"               | Shows editable fields                           |
| 3    | Modifies activity details             | Saves changes to local storage                  |
| 4    | Clicks "Confirm"                     | Updates the activity in the database           |

---

## Alternate Flow(s)

### 1. Changing the date

| Step | User Actions              | System Actions                         |
| ---- | ------------------------- | -------------------------------------- |
| 1    | Changes the date          | Checks staff/resource availability     |
| 2    |                           | Reassigns staff if needed              |

### 2. Cancel editing

| Step | User Actions             | System Actions                         |
| ---- | ------------------------ | -------------------------------------- |
| 1    | Cancels editing          | Prompts for confirmation               |
| 2    | Confirms                 | Discards changes                       |

---

## Exception Flow(s)

### 1. No permission

| Step | Exception Condition     | System Response                  |
| ---- | ----------------------- | -------------------------------- |
| 1    | User is not the creator | Denies access to edit            |

---

## Post Conditions

- Activity is updated.
- The database contains the new activity information.

---

# Use Case 4 — Deleting an Activity

**Use Case:** `Deleting an activity`
**Version:** 0.1

---

## End Goal

The activity is removed from the system.

---

## Description

This use case describes how a user deletes an activity.

---

## Actors

- User

---

## Preconditions

- The user must be logged in.
- The user must be the creator of the activity.

---

## Basic Flow (Happy Path)

| Step | User Actions                    | System Actions                                        |
| ---- | ------------------------------- | ----------------------------------------------------- |
| 1    | Opens their activity            | Shows activity details                                |
| 2    | Clicks "Delete activity"       | Prompts for confirmation                              |
| 3    | Confirms deletion               | Deletes the activity from the database                |
| 4    |                                 | Removes all participants associated with the activity |

---

## Alternate Flow(s)

### 1. User cancels deletion

| Step | User Actions           | System Actions                   |
| ---- | ---------------------- | -------------------------------- |
| 1    | Clicks "Delete"       | Prompts for confirmation         |
| 2    | Cancels                | Keeps the activity               |

---

## Exception Flow(s)

### 1. Activity already deleted

| Step | Exception Condition | System Response                   |
| ---- | ------------------- | --------------------------------- |
| 1    | Activity not found  | Shows an error message            |

---

## Post Conditions

- The activity is removed from the system.
- Participants are notified about the cancellation.
