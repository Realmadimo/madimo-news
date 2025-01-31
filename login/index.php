<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs .nav-link.active {
            background-color: #007bff;
            color: #ffffff;
        }

        body {
            background-color: #525e79;

        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <!-- Display error or success messages -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
            <?php endif; ?>

            <!-- Tabs for Sign In and Sign Up -->
            <ul class="nav nav-tabs mb-4" id="loginSignUpTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login"
                        type="button" role="tab" aria-controls="login" aria-selected="true">Sign In</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="signup-tab" data-bs-toggle="tab" data-bs-target="#signup" type="button"
                        role="tab" aria-controls="signup" aria-selected="false">Sign Up</button>
                </li>
            </ul>
            <div class="tab-content" id="loginSignUpTabContent">
                <!-- Sign In Form -->
                <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                    <form action="./process_login/" method="POST">
                        <h3 class="text-center">Sign In</h3>
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label">Username</label>
                            <input type="text" name="username" id="loginUsername" class="form-control"
                                placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" name="password" id="loginPassword" class="form-control"
                                placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    </form>
                </div>

                <!-- Sign Up Form -->
                <div class="tab-pane fade" id="signup" role="tabpanel" aria-labelledby="signup-tab">
                    <form action="./process_signup/" method="POST">
                        <h3 class="text-center">Sign Up</h3>
                        <div class="mb-3">
                            <label for="signupFName" class="form-label">First Name</label>
                            <input type="text" name="f_name" id="signupFName" class="form-control"
                                placeholder="Enter your first name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupLName" class="form-label">Last Name</label>
                            <input type="text" name="l_name" id="signupLName" class="form-control"
                                placeholder="Enter your last name" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupUsername" class="form-label">Username</label>
                            <input type="text" name="username" id="signupUsername" class="form-control"
                                placeholder="Choose a username" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupPassword" class="form-label">Password</label>
                            <input type="password" name="password" id="signupPassword" class="form-control"
                                placeholder="Create a password" required>
                        </div>
                        <div class="mb-3">
                            <label for="signupConfirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" id="signupConfirmPassword"
                                class="form-control" placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Sign Up</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>