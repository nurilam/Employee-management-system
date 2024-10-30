<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

    <title>Employee Management System</title>
    <style>
      body, html {
        height: 100%;
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #fafafa;
      }

      .bg {
        background-color: #800000;
        height: 100%;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .login-form-container {
        background-color: white;
        padding: 40px;
        border-radius: 50px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        max-width: 500px;
        text-align: center;
      }

      .card-title {
        font-size: 3rem;
        font-weight: 700;
        color: #800000;
        margin-bottom: 50px;
      }

      .card-subtitle {
        font-size: 1.1rem;
        color: #333;
        margin-bottom: 30px;
      }

      .btn-primary {
        background-color: #ac0101;
        border: none;
        font-size: 1.2rem;
        font-weight: 500;
        padding: 12px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
        margin-bottom: 20px;
      }

      .btn-primary:hover {
        background-color: #940101;
        transform: scale(1.05);
      }

      .emoji {
        font-size: 1.5rem;
        margin-right: 10px;
      }

      .divider {
        height: 1px;
        width: 80%;
        background-color: #ddd;
        margin: 20px auto;
      }

      .footer-text {
        font-size: 0.9rem;
        color: #999;
      }

    </style>
  </head>
  <body>
    <div class="bg">
      <div class="login-form-container">
        <h2 class="card-title">Smart Management System</h2>
        <h6 class="card-subtitle">Please Log In According to Your Role</h6>

        <a href="employee/login.php" class="btn btn-primary">
          <span class="emoji">👤</span>Log-in as Employee
        </a>

        <a href="admin/login.php" class="btn btn-primary">
          <span class="emoji">🔑</span>Log-in as Admin
        </a>

        <div class="divider"></div>
        <p class="footer-text">Effortlessly manage your tasks with our system 💼</p>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
  </body>
</html>
