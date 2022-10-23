<?php

namespace ArtMin96\FilamentJet\Contracts;

interface DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $account
     * @return void
     */
    public function delete($account): void;
}
