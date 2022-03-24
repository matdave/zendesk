<?php
namespace Zendesk\API;

class Tickets extends API
{
    public function search($ticket): array
    {
        return $this->curl("tickets/${$ticket}.json", 'GET');
    }
    public function comment($id, $body, $author, $public = true): array
    {
        $ticket = [
            'ticket' => [
                'comment' => [
                    'body' => $body,
                    'author_id' => $author,
                    'public' => $public,
                ]
            ]
        ];
        return $this->curl("tickets/$id.json", 'PUT', $ticket);
    }

    public function create($body, $subject, $requestor): array
    {
        $ticket = [
            'ticket' => [
                'comment' => [
                    'body' => $body,
                ],
                'requester_id' => $requestor,
                'subject' => $subject
            ]
        ];
        return $this->curl("tickets.json", 'POST', $ticket);
    }

    public function get($ticket): array
    {
        return $this->curl("tickets/${$ticket}.json", 'GET');
    }
}
