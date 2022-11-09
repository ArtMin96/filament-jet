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
     *
     * @var \ArtMin96\FilamentJet\Models\TeamInvitation
     */
    public $invitation;

    /**
     * Create a new message instance.
     *
     * @param  \ArtMin96\FilamentJet\Models\TeamInvitation  $invitation
     * @return void
     */
    public function __construct(TeamInvitationModel $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('filament-jet::mail.team-invitation', ['acceptUrl' => URL::signedRoute('team-invitations.accept', [
            'invitation' => $this->invitation,
        ])])->subject(__('filament-jet::teams.team_settings.invitations.mail.subject'));
    }
}
