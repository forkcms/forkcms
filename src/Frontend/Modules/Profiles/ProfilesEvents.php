<?php

namespace Frontend\Modules\Profiles;

/**
 * A helper class that contains all possible profiles events
 */
final class ProfilesEvents
{
    /**
     * The profiles.session.id.changed event is thrown each time a profiles session Id changes (during login/logout)
     *
     * The event listener receives an
     * Frontend\Modules\Profiles\Event\ProfilesSessionIdChangedEvent instance.
     *
     * @var string
     */
    const PROFILES_SESSION_ID_CHANGED = 'profiles.session.id.changed';
}
