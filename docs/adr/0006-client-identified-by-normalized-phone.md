# A Client is identified by their normalized phone number

`clients.phone` is unique and stored normalized (E.164: `+` and digits; `8XXXXXXXXXX` and bare ten digits are read as Russian `+7`). An Order request or a manually created Order with a known phone is attached to the existing Client instead of creating a new one, and only fills Client fields that are still empty. The site form has only one mandatory field — the phone — so it is the only reliable key; names and emails typed by visitors vary between submissions.

## Considered options

- A new Client per Order: trivial, but the Clients section fills with duplicates and a Client's history is split.
- Match by phone *or* email, merging automatically: emails are optional and often mistyped; wrong merges are hard to undo.

## Consequences

- Changing a Client's phone to one already used by another Client is rejected; there is no merging of Clients.
- Two people sharing one phone are one Client.
- A visitor cannot overwrite an admin-edited name or email by resubmitting the form.
