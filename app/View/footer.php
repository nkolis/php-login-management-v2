<script>
  let swalConfig = '<?= $model['swal'] ?? '' ?>';
  if (swalConfig != '') {
    swalConfig = JSON.parse(swalConfig);
    Swal.fire({
      title: swalConfig['title'],
      icon: swalConfig['icon'],
      showConfirmButton: false,
      timer: 1000,
    }).then(function() {
      location = swalConfig['redirect-url']
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