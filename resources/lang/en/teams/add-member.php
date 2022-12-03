<?php

return [

    'title' => 'Add Team Member',

    'description' => 'Add a new team member to your team, allowing them to collaborate with you.',

    'note' => 'Please provide the email address of the person you would like to add to this team. The email address must be associated with an existing account.',

    'buttons' => [
        'save' => 'Save',
    ],

    'fields' => [
        'email' => 'Email',
        'role' => 'Role',
    ],

    'messages' => [
        'invited' => 'New member invited to team.',
        'added' => 'New member added to team.',
        'already_belongs_to_team' => 'This user already belongs to the team.',
        'already_invited_to_team' => 'This user has already been invited to the team.',
        'email_not_registered' => 'We were unable to find a registered user with this email address.',
    ],

];
