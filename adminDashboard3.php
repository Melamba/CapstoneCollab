<?php
require 'CapstoneSample_db.php';

$active = $pdo->query(
    "SELECT *
     FROM visitor_log vl
     JOIN visitor v  ON v.visitor_id = vl.visitor_id
     LEFT JOIN location l ON l.log_id = vl.log_id
     ORDER BY vl.date_time_in ASC"
)->fetchAll();

$activeCount = count($active);
$todayCount  = $pdo->query("SELECT COUNT(*) FROM visitor_log WHERE DATE(date_time_in) = CURDATE()")->fetchColumn();
$last        = $pdo->query(
    "SELECT CONCAT(v.firstname, ' ', v.lastname) FROM visitor_log vl
     JOIN visitor v ON v.visitor_id = vl.visitor_id
     ORDER BY vl.date_time_in DESC LIMIT 1"
)->fetchColumn();
$lastVisitor = $last ?: 'None yet';
$staffCount  = $pdo->query("SELECT COUNT(*) FROM `user` WHERE status = 'active' AND role <> 'admin'")->fetchColumn();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
    .absTask a
    {
        color: white;
        text-decoration: none;
        margin-right: 2vw;
    }
     .absTask
    {
        
        color: white;
        position: absolute;
        top: 0vh;
        width:100%;
        height: 5%;
        background-color: #4a265e;
        transition: height 2s ease;
        align-content: center;
        padding-left: 1vw;
    }
      .absTask:hover
    {
        position: absolute;
        top: 0vh;
        width:100%;
        height: 10%;
        background-color: #4a265e;
    }
    body
    {
        overflow: hidden;
        margin: 3% 0 0 0;
    }
    .pagePar
    {
        display:flex;
        flex-direction: row;
        margin: 0;
        width: 100%;
        height: 100%;
        flex: 1 0 0;
        min-height: 100vh;
        gap:1vw;
    }
    .sectionA
    {
       gap: 1vh;
        flex: 1 1;
        min-height: 100%;
        overflow: scroll;
        max-height: 100vh;
        min-width: 80vw;
        
    }
    .sectionB
    {
        flex: 1 1  ;
        min-height: 100%;
        display: flex;
        padding: 1vh;
        flex-direction: column;
        gap: 1vh;
    }
    .bChild
    {
        flex: 1 1;
        min-width: 100%;
        max-height: 5vh;
        gap: 5vh;
        align-content: center;
    }
    .bChildB
    {
        flex: 1 1;
        gap: 1vh;
        display: flex;
        flex-direction: column;
        
    }
    .tableActive
    {
        background-color: aliceblue;
        width: 100%;
        border-collapse: collapse;
        max-height: 80vh;
        overflow: scroll;
        text-align: center;
    }
    .tableActive th
    {
        background-color: blueviolet;
        color:white;
        padding: 1vh;
        border: black solid 0.1px;
        
    }
    .tableActive td
    {
        border: black solid 0.1px;
        padding: 1vh;
    }
    .infoCardPar
    {
        flex: 1 1;
        max-height: 45%;
        align-content: center;
        
    }
    .infoCardChi
    {
    background-color: white;
    border: #4a265e solid 2px;
    border-radius: 12px; 
    color: black;
    width:100%;
    min-height: 90%;
    padding: 3vh;
    box-sizing: border-box;
    margin: auto;
    box-shadow: 10px 10px 30px rgba(0, 0, 0, 0.3);
    align-content: baseline;
    }
</style>
</head>
<body>
    <div class="absTask">
        <a href="adminDashboard.php">Dashboard</a>
        <a href="logbookForm.html">New Visitor</a>
    </div>
    <div class="pagePar">
        <div class="sectionA">
           <table class="tableActive">
                <tr>
                    <th>Visitor ID</th>
                    <th>Visitor Name</th>
                    <th>Contact Number</th>
                    <th>Nearest Location</th>
                    <th>Visitor Type</th>
                    <th>Time in</th>
                    <th>Time out</th>
                    <th>Status</th>
                </tr>
                    <?php if (!$active): ?>
                    <tr><td colspan="3">No active visitors</td></tr>
                <?php endif; ?>
                <?php foreach ($active as $row): ?>
                <tr>
                    <td><?= e($row['visitor_id'] ?? '') ?></td>
                    <td><?= e($row['firstname'] . ' ' . $row['lastname']) ?></td>
                    <td><?= e($row['contact_number']) ?></td>
                    <td><?= e($row['building'] ?? '') ?></td>
                    <td><?= e($row['visitor_type'] ?? '') ?></td>
                    <td><?= e($row['date_time_in'] ?? '') ?></td>
                    <td><?= e($row['date_time_out'] ?? '') ?></td>
                    <td><?= e($row['status'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="sectionB">
            <div class="bChild">
                <input type="button" onclick="location.href='adminDashboard2.Php';" value = "See all current Visitor"></button>
                <input type="button" onclick="location.href='adminDashboard.Php';" value = "See active Visitor"></button>
            </div>
            <form method="POST" action = "searched.php">
                <input type = "text" name="search" placeholder = "Search Records">
                <input type="submit" name="saving" value="Search Records">
            </form>

            
                </div>
            </div>
        </div>
    </div>
</body>
</html>
