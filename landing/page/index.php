<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User page</title>
    <style>
        .profile-details , h1{
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 16px;
            font-size: 20px;
        }

        .logout-form {
            display: flex;
            justify-content: center;
            margin-top: 16px;
        }

        .logout-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .logout-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1>User page</h1>
    <div class="profile-details">
        <h3><?php echo htmlspecialchars($_SESSION['firstname'] ?? ''); ?></h3>
        <p>Email: <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
    </div>
    <form class="logout-form" action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>
</body>

</html>