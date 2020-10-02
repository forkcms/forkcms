export class MassAddToGroup {
  constructor () {
    // update the hidden input for the new group's ID with the remembered value
    const $txtNewGroup = $('input[name="newGroup"]')

    // clone the groups SELECT into the "add to group" mass action dialog
    $('.jsMassActionAddToGroupSelectGroup').replaceWith(
      $('select[name="group"]')
        .clone(true)
        .removeAttr('id')
        .attr('name', 'newGroup')
        .on('change', (event) => {
          // update the hidden input for the new group's ID with the current value
          $txtNewGroup.val(event.currentTarget.value)
        })
    )
  }
}
