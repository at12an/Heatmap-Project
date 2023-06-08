<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Heatmap Infographic</title>
    <style type="text/css">
        body {
            background-color: #F6F1F1;
        }

        h1, h2 {
            font-family: Montserrat, sans-serif;
            color: #146C94;
            text-align: center;
            margin:0;
        }

        h1 {
            padding-top: 1vh;
            font-size: 4vw;
            font-weight:800;
            margin-bottom: 3vh;
        }
        
        h2 {
            font-size: 3.5vw;
            font-weight:600;
            margin-bottom: 5vh;
        }
        pre {
            font-family: 'Roboto Mono', monospace;
            font-variant-numeric: tabular-nums;
            font-weight: 300;
            font-size: 1vw;
        }

        .title {
            font-weight: bold;
            color: #146C94;
        }
    </style>
    <title>SQL TEST</title>
  </head>
  <body>
    <form action="data.php" method="post">
        <h1>Insurance Infographic</h1>
        <?php
            $serverName = "TRAN\MSSQLSERVER01"; //serverName\instanceName
            $connectionInfo = array("Database"=>"TestDB3", "UID"=>"", "PWD"=>"");
            $conn = sqlsrv_connect($serverName, $connectionInfo);

            if (!$conn) {
                echo "Connection could not be established.<br />";
                die(print_r( sqlsrv_errors(), true));
            }

            // echo ''.$_POST['month'].'';
            // echo $_POST['month'];
            $datetime = $_POST['month'];
            // echo $datetime;
            $heatmap = $_POST['heatmap'];
            // BASE QUERY TO BUILD ON
            $query = "Select Company, Company_2, CompanyProd, D_TPD_PROD, TPDTYPE, TPDBB, D_TRAU_PROD, TRAUBB, TRAUREIN, IP_PROD, AGENB_21, AGENB_22, AGENB_23, AGENB_24, AGENB_25, AGENB_26, AGENB_27, AGENB_28, AGENB_29, AGENB_30, AGENB_31, AGENB_32, AGENB_33, AGENB_34, AGENB_35, AGENB_36, AGENB_37, AGENB_38, AGENB_39, AGENB_40, AGENB_41, AGENB_42, AGENB_43, AGENB_44, AGENB_45, AGENB_46, AGENB_47, AGENB_48, AGENB_49, AGENB_50, AGENB_51, AGENB_52, AGENB_53, AGENB_54, AGENB_55, AGENB_56, AGENB_57, AGENB_58, AGENB_59, AGENB_60, AGENB_61, AGENB_62, AGENB_63, AGENB_64 from dbo.$heatmap where month = '$datetime'"; 

            // QUERY TO GET ALL POSTED FILTER INPUTS
            $query2 = "select Filters from dbo.heatmap_filters where HeatMap = '".$heatmap."'order by LevelDisp, LevelOrd ASC";
            $stmt2 = sqlsrv_query($conn, $query2);
            while ($obj = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                // Build query
                $filter = $obj['Filters'];
                $value = $_POST[trim($filter)];
                if (strpos($filter, 'sex') !== true) {
                    $query = $query." and $filter = $value";
                }
            }
            $bcompany = $_POST['basecompany'][0];
            $bdisc = $_POST['basecompany'][2];
            // echo $bcompany;
            // echo $bdisc;
            $queryalt = $query." and Company = $bcompany and Company_2 = $bdisc";
            $query = $query." and (Company != $bcompany or Company_2 != $bdisc)";


            $query = $query." order by Company_2 desc";

            $queryalt = $queryalt." order by Company_2 desc";

            $stmt = sqlsrv_query($conn, $query);

            $stmtalt = sqlsrv_query($conn, $queryalt);

            // echo $queryalt;

            // echo $query;

            if ($stmt == false) {
                echo "stmt false";
            }
            // Print table headings
            // Query to get out table headings 
            $fill = "Company";
            $tempquery = "select HeadDisp from dbo.data_OutputDisp where $heatmap = 1 order by DispOrder asc";
            $tempstmt = sqlsrv_query($conn, $tempquery);
            echo "<pre class='title'>";
            while ($obj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                // Print each heading with 30 max characters
                $desired_length = 30;
                $str = $obj['HeadDisp'];
                $length = strlen($str);
                $spaces = $desired_length - $length;
                echo $str.str_repeat(" ", $spaces);
            }
            echo '</pre>';

            // Print All the Data for Base Company + Disc First
            while ($obj = sqlsrv_fetch_array($stmtalt, SQLSRV_FETCH_ASSOC)) {
                echo '<pre>'; 
                // Get company number and print company name
                // This is done seperately because the Company name exists in a different table to the other filter info
                $company = $obj['Company'];
                $tempquery = "select distinct CompanyDisp from dbo.data_company where Company = $company";
                $tempstmt = sqlsrv_query($conn, $tempquery);
                $tempobj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)['CompanyDisp'];
                // Print company with 30 max characters
                $desired_length = 30;
                $length = strlen($tempobj);
                $spaces = $desired_length - $length;
                echo $tempobj.str_repeat(" ", $spaces);

                // Query to get all the column values that require conversion then convert and display them.
                $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 1 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
                $tempstmt = sqlsrv_query($conn, $tempquery);
                while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                    $company = $obj['Company'];
                    $heading = $obj2['Head'];
                    // Get value column value
                    $tempquery2 = "select * from dbo.data_products where Company = $company and Heading = '$heading'";
                    $tempstmt2 = sqlsrv_query($conn, $tempquery2);
                    $tempindex = "V_".$obj[$heading];
                    // Query to convert the column value
                    $tempobj = sqlsrv_fetch_array($tempstmt2, SQLSRV_FETCH_ASSOC);
                    if ($tempobj != null) {
                        $str = $tempobj[$tempindex];
                    } else {
                        $str = '';
                    }
                    // Print out converted data with 30 max characters
                    $desired_length = 30;
                    $length = strlen($str);
                    $spaces = $desired_length - $length;
                    echo $str.str_repeat(" ", $spaces);
                }
                // Query to get all the column values that dont need conversion (mostly AGENB) and then display them
                $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 0 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
                $tempstmt = sqlsrv_query($conn, $tempquery);

                while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                    $str = $obj[$obj2['Head']];
                    $desired_length = 30;
                    $length = strlen($str);
                    $spaces = $desired_length - $length;
                    echo $str.str_repeat(" ", $spaces);
                }
                echo '</pre>';
            }
            echo '</pre>';

            // This is exact process done above, just applied to the rest of the tuples of the first processed query
            // Then print it for rest of data (excluding Base Company and Disc)
            while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Get company number and print company name
                // This is done seperately because the Company name exists in a different table to the other filter info
                echo '<pre>';
                $company = $obj['Company'];
                $tempquery = "select distinct CompanyDisp from dbo.data_company where Company = $company";
                $tempstmt = sqlsrv_query($conn, $tempquery);
                $tempobj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)['CompanyDisp'];
                $desired_length = 30;
                $length = strlen($tempobj);
                $spaces = $desired_length - $length;
                echo $tempobj.str_repeat(" ", $spaces);
                // Query to get all the column values that require conversion then convert and display them.
                $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 1 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
                $tempstmt = sqlsrv_query($conn, $tempquery);
                while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                    $company = $obj['Company'];
                    $heading = $obj2['Head'];
                    $tempquery2 = "select * from dbo.data_products where Company = $company and Heading = '$heading'";
                    $tempstmt2 = sqlsrv_query($conn, $tempquery2);
                    $tempindex = "V_".$obj[$heading];
                    $tempobj = sqlsrv_fetch_array($tempstmt2, SQLSRV_FETCH_ASSOC);
                    if ($tempobj != null) {
                        $str = $tempobj[$tempindex];
                    } else {
                        $str = '';
                    }
                    $desired_length = 30;
                    $length = strlen($str);
                    $spaces = $desired_length - $length;
                    echo $str.str_repeat(" ", $spaces);
                }
                $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 0 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
                $tempstmt = sqlsrv_query($conn, $tempquery);
                // Query to get all the column values that dont need conversion (mostly AGENB) and then display them
                while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                    $str = $obj[$obj2['Head']];
                    $desired_length = 30;
                    $length = strlen($str);
                    $spaces = $desired_length - $length;
                    echo $str.str_repeat(" ", $spaces);
                }
                echo '</pre>';
            }
            echo '</pre>';
            sqlsrv_free_stmt($stmt);
            sqlsrv_free_stmt($stmt2);
            sqlsrv_close($conn);


        ?>
    </form>
  </body>
</html