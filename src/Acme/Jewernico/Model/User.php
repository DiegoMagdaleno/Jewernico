<?php

namespace Acme\Jewernico\Model;

class User
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private int $permissionLevel;

    public function __construct(int $id, string $name, string $email, string $password, int $permissionLevel)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->permissionLevel = $permissionLevel;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getPermissionLevel(): int
    {
        return $this->permissionLevel;
    }
}
?>