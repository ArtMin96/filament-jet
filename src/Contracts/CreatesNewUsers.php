<?php

namespace ArtMin96\FilamentJet\Contracts;

interface CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return mixed
     */
    public function create(array $input);
}
