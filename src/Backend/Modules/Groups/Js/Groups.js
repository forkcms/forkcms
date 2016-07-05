/**
 * Interaction for the groups module
 */
jsBackend.Groups =
{
    // init, constructor-alike
    init: function () {
        // variables
        $moduleDataGridBody = $('.jsGroupsPermissionsModule .jsDataGrid tbody');
        $widgetsDataGridBody = $('.jsGroupsWidgets .jsDataGrid tbody');
        $dataGridCheck = $('.jsDataGrid tbody tr td.check input[type="checkbox"]');
        $selectAll = $('.jsSelectAll');

        $moduleDataGridBody.each(jsBackend.Groups.selectionPermissions);
        $widgetsDataGridBody.each(jsBackend.Groups.selectionWidgets);
        $dataGridCheck.click(jsBackend.Groups.selectHandler);
        $selectAll.click(jsBackend.Groups.selectAll);
    },

    // selectHandler
    selectHandler: function () {
        // init vars
        $this = $(this);

        // editing permissions? check permissions
        if ($this.closest('.jsGroupsPermissionsModule').length !== 0) {
            $this.closest('tbody').each(jsBackend.Groups.selectionPermissions);
        }

        // editing widgets? check widgets
        else {
            $this.closest('tbody').each(jsBackend.Groups.selectionWidgets);
        }
    },

    // selection
    selectionPermissions: function () {
        // init vars
        var allChecked = true;
        var noneChecked = true;
        $this = $(this);

        // loop all actions and check if they're checked
        $this.find('td.check input[type="checkbox"]').each(function () {
            // if not checked set false
            if (!$(this).prop('checked')) {
                allChecked = false;
            }

            // is checked?
            else {
                noneChecked = false;
            }
        });

        // some are checked? indeterminate!
        if (!allChecked && !noneChecked) {
            // unset checked and set indeterminate
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = false;
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = true;
        }

        // if all actions are checked, check massaction checkbox
        if (allChecked) {
            // unset indeterminate and set checked
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = false;
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = true;
        }

        // nothing is checked?
        if (noneChecked) {
            // unset indeterminate and checked
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = false;
            $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = false;
        }
    },

    // selection widgets
    selectionWidgets: function () {
        // init vars
        var allChecked = true;
        $this = $(this);

        // loop all actions and check if they're checked
        $this.find('td.check input[type="checkbox"]').each(function () {
            // if not checked set false
            if (!$(this).prop('checked')) {
                allChecked = false;
            }
        });

        // set checked if all is checked
        if (allChecked) {
            $this.closest('.jsDataGrid').find('th.check input[type="checkbox"]').prop('checked', true);
        }

        // uncheck if not all items are checked
        else {
            $this.closest('.jsDataGrid').find('th.check input[type="checkbox"]').prop('checked', false);
        }
    },

    // select all
    selectAll: function (event) {
        event.stopPropagation();
        // init vars
        $this = $(this);

        // toggle data grid checkboxes
        $this.closest('.jsGroupsPermissionsModule').find('td.check input[type="checkbox"]').each(function () {
            $(this).prop('checked', $this.prop('checked') ? true : false);
        });
    }
};

$(jsBackend.Groups.init);
