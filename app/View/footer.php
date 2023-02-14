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
        const inputGroupEl = target.parentNode.parentNode;
        if (inputGroupEl.classList.contains('input-group')) {
          inputGroupEl.lastElementChild.classList.remove('d-block');
        }
      }
    });
  });

  const togglePasswordElement = document.querySelectorAll('#toggle-password');

  const inputPasswordElement = document.querySelectorAll("input[type=password]");
  togglePasswordElement.forEach((el, i) => {
    el.addEventListener('click', (e) => {
      const input = inputPasswordElement[i];
      const eyeElement = el.firstElementChild;
      if (input.getAttribute('type') == 'password') {
        input.setAttribute('type', 'text');
        eyeElement.classList.remove('bi-eye-slash');
        eyeElement.classList.add('bi-eye');
      } else {
        input.setAttribute('type', 'password');
        eyeElement.classList.remove('bi-eye');
        eyeElement.classList.add('bi-eye-slash');
      }
    })
  })
</script>
</body>

</html>