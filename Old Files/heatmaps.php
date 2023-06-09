<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"><link rel="preconnect" href="https://fonts.googleapis.com">
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
  </head>

  <body>
    <h1>Insurance Infographic</h1>
    <h2>Heatmaps</h2>

    <form method="POST" action="filters.php">
      <label for="heatmap">Select Heatmap:</label>
      <br>
      <select name="heatmap" id="heatmap" action="filter.php">
        <?php

          // Connect to SQL database
          $serverName = "TRAN\MSSQLSERVER01"; //serverName\instanceName
          $connectionInfo = array("Database"=>"TestDB3", "UID"=>"", "PWD"=>"");
          $conn = sqlsrv_connect($serverName, $connectionInfo);
          echo "<option value=1>error option</option>";
          // Check for successful connection
          if ($conn) {
            echo "Connection established.<br />";
          } else {
            echo "Connection could not be established.<br />";
            die(print_r( sqlsrv_errors(), true));
          }
          
          // Get heatmap names and display names
          $query = "select heatmap, heatmapdisp from dbo.heatmap_opts";
          $stmt = sqlsrv_query($conn, $query);

          // Check if query was successful
          if ($stmt == false) {
            echo "stmt false";
          }

          $x = 0;
          while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            echo '<option value='. $obj['heatmap'] .' href="heatmaps.php">'.$obj['heatmapdisp'].'</option>';
          }
          sqlsrv_free_stmt($stmt);
          sqlsrv_close($conn);
              
          ?>
      </select>

      <br>      

      <label for="month">Select Month:</label>
      <br>
      <select name="month" id="month">
        <?php
          $serverName = "TRAN\MSSQLSERVER01"; //serverName\instanceName
          $connectionInfo = array("Database"=>"TestDB3", "UID"=>"", "PWD"=>"");
          $conn = sqlsrv_connect($serverName, $connectionInfo);
      
          if ($conn) {
            echo "Connection established.<br />";
          } else{
            echo "Connection could not be established.<br />";
            die( print_r( sqlsrv_errors(), true));
          }
          
          $query = "select distinct month from dbo.bundledv2";
          
          $stmt = sqlsrv_query($conn, $query);

          if ($stmt == false) {
            echo "stmt false";
          }

          $x = 0;


          while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // $newOption = $_POST[$obj['heatmapdisp']];  
            echo '<option value='. $obj['month']->format('Y-m-d') .'>'.$obj['month']->format('Y-m-d').'</option>';
          }
          sqlsrv_free_stmt($stmt);
          sqlsrv_close($conn);
          // Check if a new option needs to be added
          // if (isset($_POST['newOption'])) {
          //   $newOption = $_POST['newOption'];
          //   echo '<option value="' . $newOption . '">' . $newOption . '</option>';
          // }
              
          ?>
      </select>
      <br>
      <input type="submit" value="Select Heatmap">
    </form>

    <!-- <script>
      function handleChange() {
        var select = document.getElementById("mySelect");
        var selectedOption = select.value;

        var formSection1 = document.getElementById("formSection1");
        var formSection2 = document.getElementById("formSection2");

        // Reset the display of all form sections
        formSection1.style.display = "none";
        formSection2.style.display = "none";

        // Show the relevant form section based on the selected option
        if (selectedOption === "option1") {
          formSection1.style.display = "block";
        } else if (selectedOption === "option2") {
          formSection2.style.display = "block";
        }
      }
    </script> -->

  </body>

</html