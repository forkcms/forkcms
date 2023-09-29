export class TogglePasswordInputType {
  constructor () {
    const togglePasswordInputTypes = document.querySelectorAll('[data-role="toggle-password-input-type"]')

    for (const togglePasswordInputType of togglePasswordInputTypes) {
      togglePasswordInputType.addEventListener('click', this.togglePasswordInputType)
    }
  }

  togglePasswordInputType (event) {
    event.preventDefault()
    const togglePasswordInputType = event.currentTarget
    const targetId = togglePasswordInputType.getAttribute('data-target')

    const inputField = document.getElementById(targetId)
    if (inputField.getAttribute('type') === 'password') {
      inputField.setAttribute('type', 'text')
      togglePasswordInputType.innerHTML = '<i class="fas fa-eye-slash"></i>'
    } else {
      inputField.setAttribute('type', 'password')
      togglePasswordInputType.innerHTML = '<i class="fas fa-eye"></i>'
    }
  }
}
