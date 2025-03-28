<?php
session_start();
require_once('../inc/config.php');

if (!isset($_SESSION['organiser_id']))
{
    die(json_encode(["error" => "Access denied."]));
}

$organiser_id = $_SESSION['organiser_id'];
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';

function getAnalyticsData($conn, $organiser_id, $filter)
{
    if ($filter === "weekly")
    {
        $dateGrouping = "YEARWEEK(t.sale_date)";
        $dateFormat = "CONCAT(YEAR(t.sale_date), ' Week ', WEEK(t.sale_date))";
    }
    elseif ($filter === "monthly")
    {
        $dateGrouping = "DATE_FORMAT(t.sale_date, '%Y-%m')";
        $dateFormat = "DATE_FORMAT(t.sale_date, '%Y-%m')";
    }
    else
    {
        $dateGrouping = "DATE(t.sale_date)";
        $dateFormat = "DATE(t.sale_date)";
    }

    $query = "
    SELECT 
        $dateFormat AS date, 
        COUNT(t.transaction_id) AS ticket_sales, 
        COALESCE(SUM(es.price), 0) AS revenue, 
        COUNT(CASE WHEN t.seat_id IS NOT NULL THEN 1 END) AS seat_occupancy
    FROM event e
    LEFT JOIN event_transaction t ON e.event_id = t.event_id
    LEFT JOIN event_seats es ON t.seat_id = es.id  -- Ensure ticket price is stored in event_seats
    WHERE e.organizer_id = ?
    GROUP BY $dateGrouping
    ORDER BY date ASC;
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $organiser_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc())
    {
        $row['ticket_sales'] = $row['ticket_sales'] ?? 0;
        $row['revenue'] = $row['revenue'] ?? 0;
        $row['seat_occupancy'] = $row['seat_occupancy'] ?? 0;
        $data[] = $row;
    }
    return $data;
}


$data = getAnalyticsData($conn, $organiser_id, $filter);
echo json_encode($data);
?>