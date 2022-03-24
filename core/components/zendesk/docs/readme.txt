# Zendesk

This package includes a FormIt hook for creating and updating tickets in Zendesk.

## System Settings

| Key | Description |
|-----|-------------|
| zendesk.domain | The prefix domain of your Zendesk account, e.g. {{domain}}.zendesk.com |
| zendesk.user | Authenticated user email address for your Zendesk account. Must have agent access. |
| zendesk.token | API Token for your Zendesk account. |

## Hook Options

| Option           | Default | Description                                                  |
|------------------|---------|--------------------------------------------------------------|
| zdAllowNewTicket | true | Allows new tickets to be created                             |
| zdAllowCommentTicket | true | Allows commenting on existing tickets (requires a ticket ID) |
| zdAllowCommentClosed | false | Allow commenting on closed tickets                           |
| zdPublicComment | true | Set comments as public                                       |
| zdTicketField | ticket | The field name for the ticket ID                             |
| zdEmailField | email | The field name for the commentor email                       |
| zdNameField | name | The field name for the commentor name                        |
| zdCommentField | comment | The field name for the ticket message                        |
| zdSubject | `New Ticket from [[+name]]` | The subject of a new ticket |


### Example Usage
```html
[[FormIt?
    &hooks=`zdHook,redirect`
    &zdAllowCommentTicket=`false`
    &redirectTo=`3`
]]
```
