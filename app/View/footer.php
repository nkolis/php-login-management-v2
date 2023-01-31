<script>
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