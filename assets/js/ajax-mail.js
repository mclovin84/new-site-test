function success() {
  // Success message show
  const successMsg = document.querySelector(".email-success");
  if (successMsg) {
    successMsg.classList.add("show", "alert", "alert-success");
    successMsg.innerText = "Message sent successfully!";
  }

  // Remove error message if visible
  const errorMsg = document.querySelector(".email-error");
  if (errorMsg) {
    errorMsg.classList.remove("show");
  }

  // Clear the form fields
  const form = document.getElementById("contact-form");
  if (form) {
    form.reset();
  }
}
