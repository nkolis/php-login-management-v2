<script>
  let swalConfig = '<?= $model['swal'] ?? '' ?>';
  if (swalConfig != '') {
    swalConfig = JSON.parse(swalConfig);
    Swal.fire({
      title: swalConfig['title'] || '',
      text: swalConfig['text'] || '',
      icon: swalConfig['icon'],
      showConfirmButton: swalConfig['showConfirmButton'] || false,
      timer: swalConfig['timer'] || 0,
    }).then(function() {
      if (swalConfig['redirect-url'] != undefined) {
        location = swalConfig['redirect-url']
      }
    });
  }
  const formControlElement = document.querySelectorAll(".form-control");

  formControlElement.forEach((el) => {
    el.addEventListener("input", (e) => {
      const target = e.target;
      const value = target.value;
      if (value != "" && target.classList.contains("is-invalid")) {
        target.classList.remove("is-invalid");
      }
    });
  });

  const togglePasswordElement = document.querySelector('#toggle-password');
  const toggleEyeElement = togglePasswordElement.firstElementChild;
  const inputPasswordElement = document.querySelector("#password");
  togglePasswordElement.addEventListener('click', (e) => {
    e.preventDefault();
    togglePasswordElement.classList.toggle('eye');
    if (togglePasswordElement.classList.contains('eye')) {
      inputPasswordElement.setAttribute('type', 'text');
      toggleEyeElement.classList.remove('bi-eye-slash');
      toggleEyeElement.classList.toggle('bi-eye');
    } else {
      inputPasswordElement.setAttribute('type', 'password');
      toggleEyeElement.classList.remove('bi-eye');
      toggleEyeElement.classList.toggle('bi-eye-slash');
    }
  });
</script>
</body>

</html>