<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.php" media="screen">
    <title>Insurance Infographic</title>
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

      form {
        background-color: #AFD3E2;
        margin-left: 20vw;
        margin-right:20vw;
        padding:5%;
        border-radius: 2%;
      }

      select, label, input[type=submit] {
        font-family: Montserrat, sans-serif;
      }

      select {
        margin-bottom: 2vh;
        font-weight: 600;
      }

      label {
        font-size: 1.2vw;
        font-weight: bold;
      }

      input[type=button], input[type=submit] {
        background-color: #146C94;
        border: none;
        color: white;
        padding: 1.3vw 2.6vw;
        text-decoration: none; 
        cursor: pointer;
        margin-left: auto;
        margin-right:auto;
        margin-top:7vh;
        display:block;
        font-weight: bold;
        font-size: 1vw;
      }

    </style>
    <title>SQL TEST</title>
  </head>
  <body>
    <h1>Insurance Infographic</h1>
    <h2>Heatmaps</h2>
    <form action="data.php" method="post">
        <?php
            // Connect to SQL Database
            $serverName = "TRAN\MSSQLSERVER01"; //serverName\instanceName
            $connectionInfo = array("Database"=>"TestDB3", "UID"=>"", "PWD"=>"");
            $conn = sqlsrv_connect($serverName, $connectionInfo);

            // Check if connection successful
            if (!$conn) {
                echo "Connection could not be established.<br />";
                die( print_r( sqlsrv_errors(), true));
            }

            // Re-POST the month and heatmap values for next page
            echo '<input type="hidden" name="month" value='.$_POST['month'].'>';
            echo '<input type="hidden" name="heatmap" value='.$_POST['heatmap'].'>';

            // Initialise info to organise respective filters for a given heatmap
            $heatmap = $_POST["heatmap"];
            $query = "select * from dbo.heatmap_filters where HeatMap = '" .$heatmap."' order by LevelDisp, LevelOrd ASC";
            $stmt = sqlsrv_query($conn, $query);

            // Query error check
            if ($stmt == false) {
                echo "stmt false";
            }
            
            // Iterate through tuples from query, and generate a dropdown select for each
            while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo '<label for='.$obj["Filters"].'>Select '.$obj["FiltersDisp"].': </label>';
                echo '<select name='.$obj["Filters"].' id='.$obj["Filters"].'>';
                for ($i = 0; true; $i++) {
                    $str = 'V_'.$i;
                    if (!isset($obj[$str])) {
                        break;
                    }
                    if ($obj[$str] != null) {
                        echo '<option value='.$i.'>'.$obj[$str].'</option>';
                    }
                }
                echo'</select><br>';
            }

            // Dropdown select for all companies + discount combinations
            $query2 = "select * from dbo.data_company";
            $stmt2 = sqlsrv_query($conn, $query2);

            echo "<label for='basecompany'>Select Base Company: </label>";
            echo "<select name='basecompany' id='basecompany'>";

            // Iterate over all the companies
            while ($obj = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
                // Get out each discount for a given companies
                $company = $obj['Company'];
                $query3 = "select * from dbo.data_products where Company = $company and Heading = 'Company_2'";
                $stmt3 = sqlsrv_query($conn, $query3);
                $companydisp = $obj['CompanyDisp'];
                while ($obj2 = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
                    for ($i = 0; true; $i++) {
                        $str = 'V_'.$i;
                        if (!isset($obj2[$str])) {
                            break;
                        }
                        if ($obj2[$str] != null) {
                            $disc = $obj2[$str];
                            echo "<option value={'company':$company,'disc':$i>$companydisp $disc</option>";
                        }
                    }
                }
            }

            echo "</select><br>";

            echo "</select><br>";

            echo '<input type="submit" value="Enter Filters">';
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);


        ?>
    </form>
  </body>
</html