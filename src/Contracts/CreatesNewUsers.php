<?php

namespace ArtMin96\FilamentJet\Contracts;

interface CreatesNewUsers
{
    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \Illuminate\Foundation\Auth\User
     */
    public function create(array $input);
}
