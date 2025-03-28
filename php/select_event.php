<?php
session_start();
include '../inc/config.php';

// 检查是否已登录
if (!isset($_SESSION['organiser_id']))
{
    header("Location: organiser_Login.php");
    exit();
}

$organiser_id = $_SESSION['organiser_id'];

// 查询 organizer 创建的 event
$query = "SELECT event_id, event_name FROM event WHERE organizer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $organiser_id);
$stmt->execute();
$result = $stmt->get_result();

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (!empty($_POST['event_id']) && isset($_POST['vip_price'], $_POST['regular_price'], $_POST['economy_price']))
    {
        $_SESSION['selected_event'] = $_POST['event_id'];
        $_SESSION['vip_price'] = $_POST['vip_price'];
        $_SESSION['regular_price'] = $_POST['regular_price'];
        $_SESSION['economy_price'] = $_POST['economy_price'];
        $_SESSION['promotion'] = !empty($_POST['promotion']) ? $_POST['promotion'] : null;

        header("Location: ticket_setup.php"); // 选择完成后跳转
        exit();
    }
    else
    {
        $error = "Plese select an event and enter ticket prices.";
    }
}
?>


<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <title>Choose Event</title>
    <style>
        h2{
            color: gold;
        }
        body {
            display: flex;
            background-color: #001f3f;

            font-family: Arial, sans-serif;
            height: 100vh;
            margin: 0;
            padding: 0;
        }
        body .container{
            color: black;
            font-size: 12px;
        }

        /* 固定 sidebar 在左侧 */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #222;
            color: white;
        }

        /* 让 container 居中 */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-left: 250px;
            /* 避开 sidebar */
            width: calc(100% - 250px);
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        select,
        input,
        button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
        }   
    </style>



</head>

<body>

    <div class="sidebar">
        <?php include "../inc/sidebar.php"; ?>
    </div>
    <div class="content">

        <div class="container">
            <h2>Choose Event</h2>

            <?php if (isset($error))
                echo "<p class='error'>$error</p>"; ?>

            <form method="post">
                <label for="event">Select Event:</label>
                <select name="event_id" id="event" required>
                    <option value="">Please Select...</option>
                    <?php while ($row = $result->fetch_assoc())
                    { ?>
                        <option value="<?= $row['event_id']; ?>"><?= $row['event_name']; ?></option>
                    <?php } ?>
                </select>

                <label for="vip_price">VIP Ticket Price:</label>
                <input type="number" name="vip_price" id="vip_price" placeholder="Submit VIP Ticket Price" required>

                <label for="regular_price">Regular Ticket Price:</label>
                <input type="number" name="regular_price" id="regular_price" placeholder="Submit Regular Ticket Price"
                    required>

                <label for="economy_price">Economy Ticket Price:</label>
                <input type="number" name="economy_price" id="economy_price" placeholder="Submit Economy Ticket Price"
                    required>

                <label for="promotion">Promotion (Code or Discount):</label>
                <input type="text" name="promotion" id="promotion" placeholder="Enter promotion code or discount (optional)">


                <button type="submit">Move to Seat Management</button>
            </form>
        </div>
    </div>
</body>

</html>