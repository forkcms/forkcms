export class Groups {
  constructor () {
    // variables
    const $moduleDataGridBody = $('.jsGroupsPermissionsModule .jsDataGrid tbody')
    const $widgetsDataGridBody = $('.jsGroupsWidgets .jsDataGrid tbody')
    const $dataGridCheck = $('.jsDataGrid tbody tr td.check input[type="checkbox"]')
    const $selectAll = $('.jsSelectAll')

    $moduleDataGridBody.each((index, element) => {
      this.selectionPermissions(element)
    })
    $widgetsDataGridBody.each((index, element) => {
      this.selectionWidgets(element)
    })
    $dataGridCheck.on('click', $.proxy(this.selectHandler, this))
    $selectAll.on('click', $.proxy(this.selectAll, this))
  }

  // selectHandler
  selectHandler (event) {
    // init vars
    const $this = $(event.currentTarget)

    // editing permissions? check permissions
    if ($this.closest('.jsGroupsPermissionsModule').length !== 0) {
      $this.closest('tbody').each((index, element) => {
        this.selectionPermissions(element)
      })
    } else {
      // editing widgets? check widgets
      $this.closest('tbody').each((index, element) => {
        this.selectionWidgets(element)
      })
    }
  }

  // selection
  selectionPermissions (element) {
    // init vars
    let allChecked = true
    let noneChecked = true
    const $this = $(element)

    // loop all actions and check if they're checked
    $this.find('td.check input[type="checkbox"]').each((index, checkbox) => {
      // if not checked set false
      if (!$(checkbox).prop('checked')) {
        allChecked = false
      } else {
        // is checked?
        noneChecked = false
      }
    })

    // some are checked? indeterminate!
    if (!allChecked && !noneChecked) {
      // unset checked and set indeterminate
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = false
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = true
    }

    // if all actions are checked, check massaction checkbox
    if (allChecked) {
      // unset indeterminate and set checked
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = false
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = true
    }

    // nothing is checked?
    if (noneChecked) {
      // unset indeterminate and checked
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).indeterminate = false
      $this.closest('.jsGroupsPermissionsModule').find('input.jsSelectAll').get(0).checked = false
    }
  }

  // selection widgets
  selectionWidgets (element) {
    // init vars
    let allChecked = true
    const $this = $(element)

    // loop all actions and check if they're checked
    $this.find('td.check input[type="checkbox"]').each((index, checkbox) => {
      // if not checked set false
      if (!$(checkbox).prop('checked')) {
        allChecked = false
      }
    })

    // set checked if all is checked
    if (allChecked) {
      $this.closest('.jsDataGrid').find('th.check input[type="checkbox"]').prop('checked', true)
    } else {
      // uncheck if not all items are checked
      $this.closest('.jsDataGrid').find('th.check input[type="checkbox"]').prop('checked', false)
    }
  }

  // select all
  selectAll (event) {
    event.stopPropagation()
    // init vars
    const $selectAll = $(event.currentTarget)

    // toggle data grid checkboxes
    $selectAll.closest('.jsGroupsPermissionsModule').find('td.check input[type="checkbox"]').each((index, checkbox) => {
      $(checkbox).prop('checked', $selectAll.prop('checked'))
    })
  }
}
