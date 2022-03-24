# Zendesk

This package includes a FormIt hook for creating and updating tickets in Zendesk. 

Signup for a [Zendesk account here](https://www.zendesk.com/).

## System Settings

| Key | Description |
|-----|-------------|
| zendesk.domain | The prefix domain of your Zendesk account, e.g. {{domain}}.zendesk.com |
| zendesk.user | Authenticated user email address for your Zendesk account. Must have agent access. |
| zendesk.token | [API Token](https://support.zendesk.com/hc/en-us/articles/4408889192858#:~:text=Generating%20an%20API%20token%201%20In%20Admin%20Center%2C,Save%20to%20return%20to%20the%20API%20page.%20) for your Zendesk account. |

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
