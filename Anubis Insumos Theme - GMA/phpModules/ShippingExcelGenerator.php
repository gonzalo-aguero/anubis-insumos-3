<?php
    header("Content-Type: application/vnd.ms-excel;charset=utf-8");
    header("Content-Disposition: attachment; filename=Anubis Shipments.xls");
    // DATABASE VALUES.
    define("GMA_DB_NAME", null);
    define("GMA_DB_USER", null);
    define("GMA_DB_PASSWORD", null);
    define("GMA_DB_HOST", null);
    $connection = mysqli_connect( GMA_DB_HOST, GMA_DB_USER, GMA_DB_PASSWORD, GMA_DB_NAME);
    $sql = "SELECT * FROM gma_shipping_data";
    $result = mysqli_query($connection,$sql);
    ?>
    <table border="1">
        <tr style="display: block;">
            <th style="width: 200; text-align:center;">Direccion</th>
            <th style="width: 200; text-align:center;">Monto</th>
            <th style="width: 200; text-align:center;">Telefono</th>
        </tr>
    <?php
    while ($resultArr = mysqli_fetch_array($result)) {
        ?>
        <tr>
            <td style="width: 200; text-align:center;"><?php echo $resultArr['shipping_addres']; ?></td>
            <td style="width: 200; text-align:center;"><?php echo $resultArr['amount']; ?></td>
            <td style="width: 200; text-align:center;"><?php echo $resultArr['phone']; ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
    mysqli_close($connection);
?>