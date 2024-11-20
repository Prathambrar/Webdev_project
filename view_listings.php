<?php
include 'db.php';

\ = \->query('SELECT * FROM Cars');
\ = \->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>View Listings</title>
</head>
<body>
    <h2>Available Car Listings</h2>
    <table border="1">
        <tr>
            <th>Model</th>
            <th>Year</th>
            <th>Price</th>
            <th>Description</th>
        </tr>
        <?php foreach (\ as \): ?>
            <tr>
                <td><?= htmlspecialchars(\['model']) ?></td>
                <td><?= \['year'] ?></td>
                <td><?= \['price'] ?></td>
                <td><?= htmlspecialchars(\['description']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
