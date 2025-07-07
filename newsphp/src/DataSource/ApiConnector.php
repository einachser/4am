<?php

namespace App\DataSource;

interface ApiConnector
{
    public function fetchData(string $query): ?array;
}


