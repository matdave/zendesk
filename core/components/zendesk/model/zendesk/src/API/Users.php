<?php
namespace Zendesk\API;

class Users extends API
{
    public function search($email): array
    {
        return $this->curl("users/search.json?query=$email", 'GET');
    }
    public function create($email, $name): array
    {
        $user = [
            'user' => [
                'email' => $email,
                'name' => $name,
            ]
        ];
        return $this->curl("tickets/users.json", 'POST', $user);
    }
}
