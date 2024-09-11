<?php

namespace Impiger\ACL\Events;

use Impiger\ACL\Models\Role;
use Impiger\ACL\Models\User;
use Impiger\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RoleAssignmentEvent extends Event
{
    use SerializesModels;

    /**
     * @var Role
     */
    public $role;

    /**
     * @var User
     */
    public $user;

    /**
     * RoleAssignmentEvent constructor.
     *
     * @param Role $role
     * @param User $user
     */
    public function __construct(Role $role, User $user)
    {
        $this->role = $role;
        $this->user = $user;
    }
}
