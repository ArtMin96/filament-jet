<?php

return [
    'create_team' => [
        'title' => 'Team Details',
        'description' => 'Create a new team to collaborate with others on projects.',

        'team_owner_label' => 'Team Owner',

        'fields' => [
            'team_name' => 'Team Name',
        ],

        'actions' => [
            'save' => 'Create',
        ],

        'created' => 'Created',
    ],

    'team_settings' => [
        'current_team_not_exists' => 'You are not involved in any team.',

        'update_name' => [
            'title' => 'Team Name',
            'description' => 'The team\'s name and owner information.',

            'team_owner_label' => 'Team Owner',

            'fields' => [
                'team_name' => 'Team Name',
            ],

            'actions' => [
                'save' => 'Save',
            ],

            'updated' => 'Updated',
        ],

        'add_team_member' => [
            'title' => 'Add Team Member',
            'description' => 'Add a new team member to your team, allowing them to collaborate with you.',

            'note' => 'Please provide the email address of the person you would like to add to this team. The email address must be associated with an existing account.',

            'fields' => [
                'email' => 'Email',
                'role' => 'Role',
            ],

            'actions' => [
                'save' => 'Save',
            ],

            'notify' => [
                'invited' => 'Invited',
                'added' => 'Added',
                'invitation_canceled' => 'Invitation canceled',
            ],
        ],

        'invitations' => [
            'title' => 'Pending Team Invitations',
            'description' => 'These people have been invited to your team and have been sent an invitation email. They may join the team by accepting the email invitation.',

            'mail' => [
                'subject' => 'Team Invitation',
                'title' => 'You have been invited to join the :team team!',

                'registration' => [
                    'title' => 'If you do not have an account, you may create one by clicking the button below. After creating an account, you may click the invitation acceptance button in this email to accept the team invitation:',
                    'description' => 'If you already have an account, you may accept this invitation by clicking the button below:',
                ],

                'accept_description' => 'You may accept this invitation by clicking the button below:',
                'warning' => 'If you did not expect to receive an invitation to this team, you may discard this email.',

                'actions' => [
                    'create_account' => 'Create Account',
                    'accept' => 'Accept Invitation',
                ],
            ],

            'actions' => [
                'cancel' => 'Cancel',
            ],
        ],

        'team_members' => [
            'title' => 'Team Members',
            'description' => 'All of the people that are part of this team.',

            'notify' => [
                'removed' => 'Removed',
                'leave' => 'You leave the team',
            ],

            'manage' => [
                'modal_heading' => 'Manage role',
                'modal_subheading' => '',
                'modal_submit' => 'Save',

                'notify' => [
                    'success' => 'Team member role updated.',
                ],
            ],
        ],

        'delete_team' => [
            'title' => 'Delete Team',
            'description' => 'Permanently delete this team.',
            'note' => 'Once a team is deleted, all of its resources and data will be permanently deleted. Before deleting this team, please download any data or information regarding this team that you wish to retain.',

            'actions' => [
                'delete' => 'Delete Team',
            ],

            'notify' => 'Team deleted',
        ],
    ],

    'validations' => [
        'already_belongs_to_team' => 'This user already belongs to the team.',
        'already_invited_to_team' => 'This user has already been invited to the team.',
        'cannot_leave_own_team' => 'You may not leave a team that you created.',
    ],
];
