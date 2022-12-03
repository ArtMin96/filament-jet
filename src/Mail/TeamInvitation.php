<?php

namespace ArtMin96\FilamentJet\Mail;

use ArtMin96\FilamentJet\Models\TeamInvitation as TeamInvitationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The team invitation instance.
     */
    public TeamInvitationModel $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \ArtMin96\FilamentJet\Models\TeamInvitation  $invitation
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this->markdown('filament-jet::mail.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('filament-jet::teams/invitation-mail.subject'));
    }
}
