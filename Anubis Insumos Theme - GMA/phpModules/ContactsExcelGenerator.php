<?php
    // DATABASE VALUES.
    define("GMA_DB_NAME", null);
    define("GMA_DB_USER", null);
    define("GMA_DB_PASSWORD", null);
    define("GMA_DB_HOST", null);
    $connection = mysqli_connect( GMA_DB_HOST, GMA_DB_USER, GMA_DB_PASSWORD, GMA_DB_NAME);
    
    // header("Content-Type: application/vnd.ms-excel; charset=iso-8859-1");
    // header("Content-Disposition: attachment; filename=Anubis Contactos.xls");
    
    // header("Content-Type: application/xls");
    // header("Content-Disposition: attachment; filename=Anubis Contacts.xls);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Anubis Contacts.csv"');
    $delimiter = ',';
    $f = fopen('php://memory', 'w');
    $fields = array('Name', 'Phone', 'Email');
    fputcsv($f, $fields, $delimiter);

    $sql = 'SELECT * FROM gma_contacts';
    $result = mysqli_query($connection,$sql);
    while ($resultArr = mysqli_fetch_array($result)) {
        $row = array(
                $resultArr['name'],
                $resultArr['phone'],
                $resultArr['email'],
            );
        fputcsv($f, $row, $delimiter);
    }
    
    fseek($f, 0);
    fpassthru($f);
    mysqli_close($connection);
?>