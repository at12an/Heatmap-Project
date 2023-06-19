<html lang="en">
  <head>
    <!--Copy paste HTML setup stuff-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!--Importing fonts from google font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Bunch of styling for the form-->
    <!--Can be reused, in your new language-->
    <style type="text/css">
        /*Body refers to the whole page*/
        /*Set background color*/
        body {
            background-color: #F6F1F1;
        }

        /*Font styling for the headers e.g. color, size, margins, allignment */
        h1, h2 {
            font-family: Montserrat, sans-serif;
            color: #146C94;
            text-align: center;
            margin:0;
        }

        /*More font styling*/
        h1 {
            padding-top: 1vh;
            font-size: 4vw;
            font-weight:800;
            margin-bottom: 3vh;
        }
        
        /*More font styling*/
        h2 {
            font-size: 3.5vw;
            font-weight:600;
            margin-bottom: 5vh;
        }

        /*More font styling*/
        pre {
            font-family: 'Roboto Mono', monospace;
            font-variant-numeric: tabular-nums;
            font-weight: 300;
            font-size: 1vw;
        }

        /*More font styling*/
        .title {
            font-weight: bold;
            color: #146C94;
        }
    </style>
    <!--Title of the tab -->
    <title>SQL TEST</title>
    <!--This whole script section just handles the excel download, no need to use this for now-->
    <script type="text/javascript">
        function exportTableToExcel(tableID, filename = ''){
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
        // Specify file name
        filename = filename?filename+'.xls':'excel_data.xls';
        
        // Create download link element
        downloadLink = document.createElement("a");
        
        document.body.appendChild(downloadLink);
    
        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob( blob, filename);
        } else {
            // Create a link to the file
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        
            // Setting the file name
            downloadLink.download = filename;
            
            //triggering the function
            downloadLink.click();
        }
    }
    </script>
    </head>


    <body>
    <!--Title / Header-->
    <h1>Insurance Infographic</h1>
    <!--This is a PHP section-->
    <!--I use this to take POST values and send POST values-->
    <!--It is also able to create its own html elements-->
    <?php
        // Connect to the data base
        $serverName = "TRAN\MSSQLSERVER01"; 
        $connectionInfo = array("Database"=>"TestDB3", "UID"=>"", "PWD"=>"");
        $conn = sqlsrv_connect($serverName, $connectionInfo);

        // Check if connection was successful
        if (!$conn) {
            echo "Connection could not be established.<br />";
            die(print_r( sqlsrv_errors(), true));
        }

        // Get POST values from the form -> Gets out the month and heatmap selected from the form
        $datetime = $_POST['month'];
        $heatmap = $_POST['heatmap'];

        // Using these POST values to create the base query
        // This query selects the heatmap table for the given date time
        $query = "Select * from dbo.$heatmap where month = '$datetime'"; 

        // Query2 to get all the filters out and then add to base query
        $query2 = "select Filters from dbo.heatmap_filters where HeatMap = '".$heatmap."'order by LevelDisp, LevelOrd ASC";
        $stmt2 = sqlsrv_query($conn, $query2);
        // While loops goes through each row in the query2 table
        // With each row -> it takes the filter name + posted values from the form it adds to the query
        // E.g. it will add "sex = 1" or "BENPERIOD = 1" to the base query
        while ($obj = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            // Build query
            $filter = $obj['Filters'];
            $value = $_POST[trim($filter)];
            $query = $query." and $filter = $value";
        }

        // Get POSTED value
        // Specifically it gets the base company + discount selected
        $bcompany = $_POST['basecompany'][0];
        $bdisc = $_POST['basecompany'][2];

        // Queryalt is the query but specifically only for the base company + discount
        $queryalt = $query." and company = $bcompany and company_2 = $bdisc";

        // Remove the base company + discount from the original query so it doesnt print twice
        $query = $query." and (company != $bcompany or company_2 != $bdisc)";

        // Correct the ordering of each query
        $query = $query." order by company_2 desc";
        $queryalt = $queryalt." order by company_2 desc";

        // Run the queries
        $stmt = sqlsrv_query($conn, $query);
        $stmtalt = sqlsrv_query($conn, $queryalt);

        // Check if queries where run successfully
        if ($stmt == false) {
            echo "stmt false";
        }
        
        if ($stmtalt == false) {
            echo "stmtalt false";
        }


        // Print table headings
        // Query to get out table headings 
        // Echo is used to create a html element
        // echo "<some html element>" creates the html element from within the php section
        // Here I start the table, the id is just used to reference the table for scripts - not important 
        echo "<table id ='tabledata'>";

        $fill = "Company";

        // Query for the display headings for the heatmap selected
        $tempquery = "select HeadDisp from dbo.data_OutputDisp where $heatmap = 1 order by DispOrder asc";
        $tempstmt = sqlsrv_query($conn, $tempquery);

        // Check successful
        if ($tempstmt == false) {
            echo "tempstmt false";
        }
        // Create the first row
        echo '<tr>';
        // Create empty entry into first row for the checkboxes
        echo '<th></th>';
        // While loop goes thg=rough each row in the output display headings and adds them as an heading to the first row
        while ($obj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
            // Takes out heading name
            $str = $obj['HeadDisp'];
            // Adds heading name as heading in the html table
            echo "<th>$str</th>";
        }

        // Print All the Data for Base Company + Disc First
        // While loop goes through each row in query alt
        // Query alt goes through the data from the heatmap table for the base company + discount
        // It will go through each row and add the respective data corresponding to each output display heading as table data
        while ($obj = sqlsrv_fetch_array($stmtalt, SQLSRV_FETCH_ASSOC)) {
            // Create new row in html table
            echo '<tr>';
            // First row entry is a checkbox 
            // Add checkbox into html 
            echo '<td><input type="checkbox" id="checkbox1" name="checkbox1"></td>';
            // Get company number and print company name
            // This is done seperately because the Company name exists in a different table to the other filter info
            // Get Company value from the row
            $company = $obj['company'];
            // Create query in company table
            $tempquery = "select distinct CompanyDisp from dbo.data_company where Company = $company";
            // Run the query
            $tempstmt = sqlsrv_query($conn, $tempquery);
            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }
            // Take out the company display name from the first row of the query (the query will only have one row)
            $tempobj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)['CompanyDisp'];
            // Add the company name as table entry
            echo "<td>$tempobj</td>";

            // Query to get all the column values that require conversion then convert and display them.
            $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 1 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
            // Run the query
            $tempstmt = sqlsrv_query($conn, $tempquery);
            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }

            // While loop goes through each row in the Output display table
            while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                // Gets company number
                $company = $obj['company'];
                // Gets heading of the current row
                $heading = $obj2['Head'];
                // Query to get the out values that will correspond to V_n for each heading
                $tempquery2 = "select * from dbo.data_products where Company = $company and Heading = '$heading'";
                // Run query
                $tempstmt2 = sqlsrv_query($conn, $tempquery2);
                if ($tempstmt2 == false) {
                    echo "tempstmt2 false";
                }
                // Take from current row of heatmap table the value of heading e.g. value of sex in the current row is 1
                // So V_1 string is created
                $tempindex = "V_".$obj[$heading];
                // Query to convert the column value
                // Using the V_n made, it will take the V_n column for the associated heading
                // E.g V_1 of sex (heading) will be male
                $tempobj = sqlsrv_fetch_array($tempstmt2, SQLSRV_FETCH_ASSOC);
                if ($tempobj != null) {
                    // If tempobj exists add to string
                    $str = $tempobj[$tempindex];
                } else {
                    // Else leave string as empty
                    $str = '';
                }
                // Add string as row entry
                echo "<td>$str</td>";
            }
            // Query to get all the column values that dont need conversion (mostly AGENB) and then display them
            $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 0 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
            // Run query
            $tempstmt = sqlsrv_query($conn, $tempquery);

            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }

            // For loop goes through each row out OutputDisp and gets name of heading
            while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                // Uses name of heading to get value out of the current row in the heatmap table
                // E.G. AGENB1 is a heading, use AGENB1 to get out the value of AGENB1 in the current tuple of the heatmap table
                $str = $obj[$obj2['Head']];
                // Add value as row entry
                echo "<td>$str</td>";
            }
            // Closing tag for row
            echo '</tr>';
        }

        // This is exact process done above, just applied to the rest of the tuples of the first processed query
        // Then print it for rest of data (excluding Base Company and Disc)
        while ($obj = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Create new row in html table
            echo '<tr>';
            // First row entry is a checkbox 
            // Add checkbox into html 
            echo '<td><input type="checkbox" id="checkbox1" name="checkbox1"></td>';
            // Get company number and print company name
            // This is done seperately because the Company name exists in a different table to the other filter info
            // Get Company value from the row
            $company = $obj['company'];
            // Create query in company table
            $tempquery = "select distinct CompanyDisp from dbo.data_company where Company = $company";
            // Run the query
            $tempstmt = sqlsrv_query($conn, $tempquery);
            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }
            // Take out the company display name from the first row of the query (the query will only have one row)
            $tempobj = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)['CompanyDisp'];
            // Add the company name as table entry
            echo "<td>$tempobj</td>";

            // Query to get all the column values that require conversion then convert and display them.
            $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 1 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
            // Run the query
            $tempstmt = sqlsrv_query($conn, $tempquery);
            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }

            // While loop goes through each row in the Output display table
            while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                // Gets company number
                $company = $obj['company'];
                // Gets heading of the current row
                $heading = $obj2['Head'];
                // Query to get the out values that will correspond to V_n for each heading
                $tempquery2 = "select * from dbo.data_products where Company = $company and Heading = '$heading'";
                // Run query
                $tempstmt2 = sqlsrv_query($conn, $tempquery2);
                if ($tempstmt2 == false) {
                    echo "tempstmt2 false";
                }
                // Take from current row of heatmap table the value of heading e.g. value of sex in the current row is 1
                // So V_1 string is created
                $tempindex = "V_".$obj[$heading];
                // Query to convert the column value
                // Using the V_n made, it will take the V_n column for the associated heading
                // E.g V_1 of sex (heading) will be male
                $tempobj = sqlsrv_fetch_array($tempstmt2, SQLSRV_FETCH_ASSOC);
                if ($tempobj != null) {
                    // If tempobj exists add to string
                    $str = $tempobj[$tempindex];
                } else {
                    // Else leave string as empty
                    $str = '';
                }
                // Add string as row entry
                echo "<td>$str</td>";
            }
            // Query to get all the column values that dont need conversion (mostly AGENB) and then display them
            $tempquery = "select Head from dbo.data_OutputDisp where ConvertData = 0 and Head != '$fill' and $heatmap = 1 order by DispOrder asc";
            // Run query
            $tempstmt = sqlsrv_query($conn, $tempquery);

            // Error check
            if ($tempstmt == false) {
                echo "tempstmt false";
            }

            // For loop goes through each row out OutputDisp and gets name of heading
            while ($obj2 = sqlsrv_fetch_array($tempstmt, SQLSRV_FETCH_ASSOC)) {
                // Uses name of heading to get value out of the current row in the heatmap table
                // E.G. AGENB1 is a heading, use AGENB1 to get out the value of AGENB1 in the current tuple of the heatmap table
                $str = $obj[$obj2['Head']];
                // Add value as row entry
                echo "<td>$str</td>";
            }
            // Closing tag for row
            echo '</tr>';
        }
        // Closing tag for table
        echo '</table';
        // Free memory stuff
        sqlsrv_free_stmt($tempstmt);
        sqlsrv_free_stmt($tempstmt2);
        sqlsrv_free_stmt($stmt);
        sqlsrv_free_stmt($stmt2);
        sqlsrv_close($conn);
    ?>
    <div>
        <!--Button to cause script event that causes excel download-->
        <button onclick="exportTableToExcel('tabledata')">Export Table Data To Excel File</button>
    </div>
  </body>
</html