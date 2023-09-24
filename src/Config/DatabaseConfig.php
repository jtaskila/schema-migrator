<?php

declare(strict_types=1);

namespace Jtaskila\SchemaMigrator\Config;

class DatabaseConfig
{
    private string $host = '';
    private string $username = '';
    private string $password = '';
    private string $database = '';
    
    public function setHost(string $host): DatabaseConfig
    {
        $this->host = $host;
        
        return $this;
    }
    
    public function getHost(): string
    {
        return $this->host;    
    }
    
    public function setUsername(string $username): DatabaseConfig
    {
        $this->username = $username;
        
        return $this;
    }
    
    public function getUsername(): string
    {
        return $this->username;    
    }
    
    public function setPassword(string $password): DatabaseConfig
    {
        $this->password = $password;
        
        return $this;
    }
    
    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setDatabase(string $database): DatabaseConfig
    {
        $this->database = $database;
        
        return $this;
    }
    
    public function getDatabase(): string
    {
        return $this->database;
    }
    
    public function validate(): bool
    {
        if (empty($this->host)) {
            return false;
        }
        if (empty($this->username)) {
            return false;
        }       
        if (empty($this->database)) {
            return false;
        }        

        return true;
    }
}