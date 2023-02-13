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
</script>
</body>

</html>