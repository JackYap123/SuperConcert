<?php
session_start();
require '../inc/config.php';

// 获取用户数据
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM Organisers WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $organiser = $result->fetch_assoc();
    $stmt->close();
}

// 处理更新请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_organiser'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $organization_name = $_POST['organization_name'];

    $updateQuery = "UPDATE Organisers SET name = ?, email = ?, phone_number = ?, organization_name = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $name, $email, $phone_number, $organization_name, $id);
    if ($stmt->execute()) {
        $success_message = "Organiser updated successfully.";
    } else {
        $error_message = "Error updating organiser: " . $stmt->error;
    }
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Organiser</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #0d1b2a;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #0d1b2a;
            box-shadow: 0 0 8px rgba(13, 27, 42, 0.3);
        }

        .btn {
            background-color: #0d1b2a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn:hover {
            background-color: #f4d160;
            transform: translateY(-3px);
        }

        .btn:active {
            transform: translateY(1px);
        }

        .error-message {
            color: #f44336;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .success-message {
            color: #4CAF50;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>
    </head>

    <body>
        <div class="container">
            <h2>Edit Organiser</h2>
            <?php if (isset($success_message)) { ?>
                <p class="success-message"> <?php echo htmlspecialchars($success_message); ?> </p>
            <?php } ?>

            <?php if (isset($error_message)) { ?>
                <p class="error-message"> <?php echo htmlspecialchars($error_message); ?> </p>
            <?php } ?>

            <form action="edit_organiser.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($organiser['id']); ?>">

                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($organiser['name']); ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($organiser['email']); ?>" required>

                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($organiser['phone_number']); ?>" required>

                <label for="organization_name">Organization Name</label>
                <input type="text" id="organization_name" name="organization_name" value="<?php echo htmlspecialchars($organiser['organization_name']); ?>">

                <button type="submit" class="btn">Save Changes</button>
            </form>
        </div>
    </body>

    </html>
