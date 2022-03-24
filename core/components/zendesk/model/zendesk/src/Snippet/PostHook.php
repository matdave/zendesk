<?php
namespace Zendesk\Snippet;

use Zendesk\API\Users;
use Zendesk\API\Tickets;

class PostHook extends Snippet
{
    public function run(): bool
    {
        $defaultConfig = [
            'zdAllowNewTicket' => true,
            'zdAllowCommentTicket' => true,
            'zdAllowCommentClosed' => false,
            'zdPublicComment' => true,
            'zdTicketField' => 'ticket',
            'zdEmailField' => 'email',
            'zdNameField' => 'name',
            'zdCommentField' => 'comment',
            'zdSubject' => 'New Ticket from [[+name]]',
        ];

        $hook = $this->scriptProperties['hook'];
        $config = array_merge($defaultConfig, $hook->formit->config);
        $values = $hook->getValues();

        $requireTicket = !$this->bool($config['zdAllowNewTicket']);

        if (empty($values[$config['zdEmailField']])) {
            $hook->addError($config['zdEmailField'], $this->modx->lexicon('zendesk.err.field_email'));
            return $hook->hasErrors();
        }

        if (empty($values[$config['zdNameField']])) {
            $hook->addError($config['zdNameField'], $this->modx->lexicon('zendesk.err.field_name'));
            return $hook->hasErrors();
        }

        if (empty($values[$config['zdCommentField']])) {
            $hook->addError($config['zdCommentField'], $this->modx->lexicon('zendesk.err.field_comment'));
            return $hook->hasErrors();
        }

        $users = new Users($this->zendesk);
        //search users
        $userResults = $users->search($values[$config['zdEmailField']]);
        if (empty($userResults['result'])) {
            // create user
            $user = $users->create($values[$config['zdEmailField']], $values[$config['zdNameField']]);
            if (empty($user['result'])) {
                $hook->addError($config['zdEmailField'], $this->modx->lexicon('zendesk.err.user_create'));
                return $hook->hasErrors();
            }
            $id = $user['result']->user->id;
        } else {
            // grab first results
            $id = $userResults['result']->users[0]->id;
        }

        if (empty($id)) {
            $hook->addError($config['zdEmailField'], $this->modx->lexicon('zendesk.err.user_create'));
            return $hook->hasErrors();
        }

        $tickets = new Tickets($this->zendesk);
        if (empty($values[$config['zdTicketField']]) && $this->bool($config['zdAllowNewTicket'])) {
            if ($requireTicket) {
                $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_not_found'));
                return $hook->hasErrors();
            }
            //allow dynamic subject
            $subject = $hook->_process($config['zdSubject'], $values);
            $ticket = $tickets->create($values[$config['zdCommentField']], $subject, $id);
            if ($ticket['code'] < 300) {
                return true;
            } else {
                $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_no_create'));
                return $hook->hasErrors();
            }
        } elseif ($this->bool($config['zdAllowCommentTicket'])) {
            $ticket = $tickets->get($values[$config['zdTicketField']]);
            if (empty($ticket['result'])) {
                $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_not_found'));
                return $hook->hasErrors();
            }
            if ($ticket['result']->ticket->status === 'solved' && !$config['zdAllowCommentClosed']) {
                $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_not_active'));
                return $hook->hasErrors();
            }
            $comment = $tickets->comment($values[$config['zdTicketField']], $values[$config['zdCommentField']], $id, $this->bool($config['zdPublicComment']));
            if ($comment['code'] < 300) {
                return true;
            } else {
                $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_no_create'));
                return $hook->hasErrors();
            }
        } else {
            $hook->addError($config['zdTicketField'], $this->modx->lexicon('zendesk.err.ticket_no_create'));
        }
        return $hook->hasErrors();
    }
}
