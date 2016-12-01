/**
 * Interaction for the dashboard module
 */
jsBackend.users =
{
    // init, something like a constructor
    init: function()
    {
        jsBackend.users.nick();
    },

    // set nickname
    nick: function()
    {
        $nickname = $('#nickname');
        $name = $('#name');
        $surname = $('#surname');

        // are all elements available
        if($nickname.length > 0 && $name.length > 0 && $surname.length > 0)
        {
            var change = true;

            // if the current value is the same as the one that would be generated then we bind the events
            if($nickname.val() != jsBackend.users.calculateNick()) { change = false; }

            // bind events
            $name.on('keyup', function() { if(change) { $nickname.val(jsBackend.users.calculateNick()); } });
            $surname.on('keyup', function() { if(change) { $nickname.val(jsBackend.users.calculateNick()); } });

            // unbind events
            $nickname.on('keyup', function() { change = false; });
        }
    },

    // calculate the nickname
    calculateNick: function()
    {
        $nickname = $('#nickname');
        $name = $('#name');
        $surname = $('#surname');

        var maxLength = parseInt($nickname.attr('maxlength'));
        if(maxLength === 0) maxLength = 255;

        return utils.string.trim(utils.string.trim($name.val()) +' '+ utils.string.trim($surname.val())).substring(0, maxLength);
    }
};

$(jsBackend.users.init);
