document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("form");
  const firstname_input = document.getElementById("firstname-input");
  const email_input = document.getElementById("email-input");
  const password_input = document.getElementById("password-input");
  const repeat_password_input = document.getElementById(
    "repeat-password-input"
  );
  const error_message = document.getElementById("error-message");
  const math_answer_input = document.getElementById("math-answer");
  const math_question = document.getElementById("math-question");
  const loginBtn = document.getElementById("login-btn");
  const signupBtn = document.getElementById("signup-btn");

  function getSignupFormErrors(firstname, email, password, repeatPassword) {
    let errors = [];

    if (firstname === "" || firstname == null) {
      errors.push("Name is required");
      firstname_input.parentElement.classList.add("incorrect");
    }
    if (email === "" || email == null) {
      errors.push("Email is required");
      email_input.parentElement.classList.add("incorrect");
    } else if (!isValidEmail(email)) {
      errors.push("Please enter a valid email address");
      email_input.parentElement.classList.add("incorrect");
    }
    if (password === "" || password == null) {
      errors.push("Password is required");
      password_input.parentElement.classList.add("incorrect");
    }
    if (password.length < 8) {
      errors.push("Password must have at least 8 characters");
      password_input.parentElement.classList.add("incorrect");
    }
    if (password !== repeatPassword) {
      errors.push("Password does not match repeated password");
      password_input.parentElement.classList.add("incorrect");
      repeat_password_input.parentElement.classList.add("incorrect");
    }

    return errors;
  }

  function getLoginFormErrors(email, password) {
    let errors = [];

    if (email === "" || email == null) {
      errors.push("Email is required");
      email_input.parentElement.classList.add("incorrect");
    } else if (!isValidEmail(email)) {
      errors.push("Please enter a valid email address");
      email_input.parentElement.classList.add("incorrect");
    }
    if (password === "" || password == null) {
      errors.push("Password is required");
      password_input.parentElement.classList.add("incorrect");
    }

    return errors;
  }

  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function isMathVerificationCompleted() {
    // Check if math verification elements exist and if the submit button is enabled
    if (math_answer_input && math_question) {
      // Check if the math answer input has the 'correct' class (indicating successful verification)
      if (math_answer_input.classList.contains("correct")) {
        return true;
      }

      // Also check if the submit button is enabled (backup check)
      const submitBtn = loginBtn || signupBtn;
      return submitBtn && !submitBtn.disabled;
    }

    // If math verification elements don't exist, assume verification is not required
    return true;
  }

  if (form) {
    form.addEventListener("submit", (e) => {
      let errors = [];

      if (firstname_input) {
        // If we have a firstname input then we are in the signup
        errors = getSignupFormErrors(
          firstname_input.value,
          email_input.value,
          password_input.value,
          repeat_password_input.value
        );
      } else {
        // If we don't have a firstname input then we are in the login
        errors = getLoginFormErrors(email_input.value, password_input.value);
      }

      // Check if math verification was completed
      if (!isMathVerificationCompleted()) {
        errors.push("Please complete the math verification");
        e.preventDefault();
        error_message.innerText = errors.join(". ");
        error_message.style.display = "block";
        return;
      }

      if (errors.length > 0) {
        // If there are any errors
        e.preventDefault();
        error_message.innerText = errors.join(". ");
        error_message.style.display = "block";
      }
    });
  }

  const allInputs = [
    firstname_input,
    email_input,
    password_input,
    repeat_password_input,
  ].filter((input) => input != null);

  allInputs.forEach((input) => {
    input.addEventListener("input", () => {
      // Only remove the incorrect class from this specific input
      if (input.classList.contains("incorrect")) {
        input.classList.remove("incorrect");
      }
      // Don't clear the error message here - let it persist until form submission
    });
  });

  // Password visibility toggle
  const togglePassword = document.getElementById("toggle-password-visibility");
  if (togglePassword && password_input) {
    togglePassword.addEventListener("click", () => {
      const isPassword = password_input.type === "password";
      password_input.type = isPassword ? "text" : "password";
      togglePassword.src = isPassword
        ? "asset/icons/eye-open.png"
        : "asset/icons/eye-close.png";
      togglePassword.alt = isPassword ? "Hide Password" : "Show Password";
    });
  }

  // Repeat password visibility toggle (for signup)
  const toggleRepeatPassword = document.getElementById(
    "toggle-repeat-password-visibility"
  );
  if (toggleRepeatPassword && repeat_password_input) {
    toggleRepeatPassword.addEventListener("click", () => {
      const isPassword = repeat_password_input.type === "password";
      repeat_password_input.type = isPassword ? "text" : "password";
      toggleRepeatPassword.src = isPassword
        ? "asset/icons/eye-open.png"
        : "asset/icons/eye-close.png";
      toggleRepeatPassword.alt = isPassword
        ? "Hide Repeat Password"
        : "Show Repeat Password";
    });
  }
});
