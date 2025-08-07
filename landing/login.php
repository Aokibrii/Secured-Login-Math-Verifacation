<?php
session_start();
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_SESSION['email'])) {
  header("Location: page/index.php");
  exit();
}
if (isset($_GET['show']) && $_GET['show'] === 'login') {
  header("Location: login.php");
  exit();
}

// Handle error messages
$error_message = "";
$success_message = "";

if (isset($_GET['error'])) {
  switch ($_GET['error']) {
    case 'empty_fields':
      $error_message = "Please fill in all fields.";
      break;
    case 'invalid_credentials':
      $error_message = "Invalid email or password.";
      break;
    case 'recaptcha_failed':
    case 'math_captcha_failed':
      $error_message = "Incorrect answer to the math question.";
      break;
    default:
      $error_message = "An error occurred. Please try again.";
  }
}

if (isset($_GET['success'])) {
  switch ($_GET['success']) {
    case 'account_created':
      $success_message = "Account created successfully! Please login.";
      break;
  }
}

// Generate math question (addition, subtraction, or division)
$min = 1;
$max = 10;
$operators = ['+', '-', '÷'];
$operator = $operators[array_rand($operators)];

if ($operator === '+') {
  $a = rand($min, $max);
  $b = rand($min, $max);
  $answer = $a + $b;
  $question = "$a + $b = ?";
} elseif ($operator === '-') {
  $a = rand($min, $max);
  $b = rand($min, $max);
  // Ensure non-negative result
  if ($a < $b) {
    [$a, $b] = [$b, $a];
  }
  $answer = $a - $b;
  $question = "$a - $b = ?";
} else { // Division (ensure whole number)
  $b = rand($min, $max);
  $answer = rand($min, $max);
  $a = $b * $answer;
  $question = "$a ÷ $b = ?";
}

$_SESSION['math_captcha_answer'] = $answer;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" />
  <link rel="icon" href="asset/images/lock.png">
  <script type="text/javascript" src="js/validation.js" defer></script>
</head>

<body>
  <div class="container-flex">
    <div class="wrapper">
      <h1>Login</h1>
      <p id="error-message" class="<?php echo $error_message ? 'error' : ''; ?>"><?php echo htmlspecialchars($error_message); ?></p>
      <?php if ($success_message): ?>
        <p id="success-message" class="success"><?php echo htmlspecialchars($success_message); ?></p>
      <?php endif; ?>
      <form id="form" action="../config/login_signup.php" method="POST">
        <div>
          <label for="email-input">
            <span>@</span>
          </label>
          <input
            type="email"
            name="email"
            id="email-input"
            placeholder="Email" />
        </div>
        <div>
          <label for="password-input">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              height="24"
              viewBox="0 -960 960 960"
              width="24">
              <path
                d="M240-80q-33 0-56.5-23.5T160-160v-400q0-33 23.5-56.5T240-640h40v-80q0-83 58.5-141.5T480-920q83 0 141.5 58.5T680-720v80h40q33 0 56.5 23.5T800-560v400q0 33-23.5 56.5T720-80H240Zm240-200q33 0 56.5-23.5T560-360q0-33-23.5-56.5T480-440q-33 0-56.5 23.5T400-360q0 33 23.5 56.5T480-280ZM360-640h240v-80q0-50-35-85t-85-35q-50 0-85 35t-35 85v80Z" />
            </svg>
          </label>
          <div class="password-container">
            <input
              type="password"
              name="password"
              id="password-input"
              placeholder="Password" />
            <img src="asset/icons/eye-close.png" id="toggle-password-visibility" alt="Toggle Password Visibility">
          </div>
        </div>
        <div class="checkbox-container">
          <input type="checkbox" id="show-math-modal" name="show-math-modal">
          <label for="show-math-modal">Click the Checkbox to Login & Verified your account</label>
        </div>
        <input type="hidden" name="math-answer" id="math-answer-hidden" value="">
        <button type="submit" name="login" id="login-btn" disabled>Login</button>
      </form>
      <p>New here? <a href="signup.php">Create an Account</a></p>
    </div>
  </div>

  <!-- Math Modal -->
  <div id="math-modal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Math Verification</h2>
        <span class="close">&times;</span>
      </div>
      <div class="modal-body">
        <p>Please solve this math problem to verify your Account</p>
        <div class="math-question">
          <span id="math-question" data-question="<?php echo $question; ?>" data-answer="<?php echo $answer; ?>"><?php echo $question; ?></span>
        </div>
        <div class="math-input">
          <input
            type="text"
            name="math-answer"
            id="math-answer"
            placeholder="Enter your answer"
            autocomplete="off" />
        </div>
        <div class="modal-buttons">
          <button type="button" id="verify-math">Verify</button>
          <button type="button" id="cancel-math">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Modal functionality
    const modal = document.getElementById('math-modal');
    const checkbox = document.getElementById('show-math-modal');
    const closeBtn = document.querySelector('.close');
    const verifyBtn = document.getElementById('verify-math');
    const cancelBtn = document.getElementById('cancel-math');
    const loginBtn = document.getElementById('login-btn');
    const mathAnswer = document.getElementById('math-answer');
    const mathAnswerHidden = document.getElementById('math-answer-hidden');
    const mathQuestion = document.getElementById('math-question');

    // Show modal when checkbox is checked
    checkbox.addEventListener('change', function() {
      if (this.checked) {
        modal.style.display = 'flex';
        mathAnswer.focus();
      } else {
        modal.style.display = 'none';
        loginBtn.disabled = true;
        if (mathAnswerHidden) mathAnswerHidden.value = '';
      }
    });

    // Close modal when clicking X
    closeBtn.addEventListener('click', function() {
      modal.style.display = 'none';
      checkbox.checked = false;
      loginBtn.disabled = true;
      if (mathAnswerHidden) mathAnswerHidden.value = '';
    });

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
      if (event.target === modal) {
        modal.style.display = 'none';
        checkbox.checked = false;
        loginBtn.disabled = true;
        if (mathAnswerHidden) mathAnswerHidden.value = '';
      }
    });

    // Verify math answer
    verifyBtn.addEventListener('click', function() {
      const userAnswer = mathAnswer.value.trim();
      const expectedAnswer = mathQuestion.dataset.answer;

      if (userAnswer === expectedAnswer) {
        modal.style.display = 'none';
        loginBtn.disabled = false;
        mathAnswer.classList.remove('incorrect');
        mathAnswer.classList.add('correct');
        if (mathAnswerHidden) mathAnswerHidden.value = userAnswer;
      } else {
        mathAnswer.classList.add('incorrect');
        mathAnswer.classList.remove('correct');
        mathAnswer.value = '';
        mathAnswer.focus();
        if (mathAnswerHidden) mathAnswerHidden.value = '';
      }
    });

    // Cancel button
    cancelBtn.addEventListener('click', function() {
      modal.style.display = 'none';
      checkbox.checked = false;
      loginBtn.disabled = true;
      mathAnswer.value = '';
      mathAnswer.classList.remove('incorrect', 'correct');
      if (mathAnswerHidden) mathAnswerHidden.value = '';
    });

    // Enter key in math input
    mathAnswer.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        verifyBtn.click();
      }
    });
  </script>
</body>

</html>