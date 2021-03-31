<?php
    //Die folgenden beiden Funktionen gibt es eigentlich immer
    function error($str)
    {
        return "<!DOCTYPE html>
        <html lang=\"de\">
        <title>Datenbank</title>
				<head>
					<meta charset=\"utf-8\">
                    <style>
                    body
                    {
                        background-color: lightgrey;
                    }
                    a
                    {
                        color: purple; 
                    }
                    div
                    {
                        color: red;
                    }
                    </style>
                </head>     
                <body> 
                    <form>           
                        <div id=\"success\">$str.</div>
                        <p>
                        <a href=\"Index.html\" style=\"color: purple;\">Zurück zur Eingabe</a>
                        </p>
                    </form>
                </body>
        </html>";
    }
    
    function success($avg, $anz, $min, $max)
	{
        return "<!DOCTYPE html>
        <html lang=\"de\">
        <title>Datenbank</title>
						<head>
						<meta charset=\"utf-8\">
                        <style>
                        body
                        {
                            background-color: lightgrey;
                        }
                        p
                        {
                            color: blue;
                        }
                        </style>	
						</head>
						<body>
							<div>
								<p>Durchschnitt: $avg °C</p>
								<p>Anzahl: $anz</p>
								<p>Minimum: $min °C</p>
								<p>Maximum: $max °C</p>
								<a href=\"Index.html\" style=\"color: purple;\">Zurück zur Eingabe</a>
							</div>
						</body>
					</html>";
	}

    $db = mysqli_connect("localhost", "root", "", "it31_goralewski");

    if (mysqli_connect_errno())
    {
        printf("Verbindung fehlgeschlagen: " . mysqli_connect_error());
        exit();
    }

    if ($_POST['usecase'] == "tempData")
    {
        $temp = mysqli_real_escape_string($db, $_POST['temp']);
        $query = "SELECT Temp FROM Temperatur WHERE Temp = '$temp'";

        $res = mysqli_query($db, $query);

        if(!$row = mysqli_fetch_assoc($res))
        {
            $query = "INSERT INTO Temperatur (Temp) VALUE ('$temp')";
            mysqli_query($db, $query);
        }

        header("Location: http://localhost/pv_a1/");
    }
    else if($_POST['usecase'] == "tempAusgabe")
    {
        $temp = mysqli_real_escape_string($db, $_POST['temp']);

        $query = "SELECT Temp FROM Temperatur WHERE Temp = '$temp'";
        
        $res = mysqli_query($db, $query);

        //Julians vorschlag -> Methode 1
        $avg = "SELECT ROUND(AVG (Temp), 2) as durchschnitt FROM Temperatur";
        $avg =  mysqli_query($db, $avg);
        $avg = mysqli_fetch_assoc($avg);        
        $anz = "SELECT COUNT(Temp) as anzahl FROM Temperatur";
        $anz = mysqli_query($db, $anz);
        $anz = mysqli_fetch_assoc($anz);

        //Methode 2 -> das selbe wie oben aber in einer Zeile
		$min = mysqli_fetch_assoc(mysqli_query($db, "SELECT MIN(Temp) as minimum FROM Temperatur;"));
		$max = mysqli_fetch_assoc(mysqli_query($db, "SELECT MAX(Temp) as maximum FROM Temperatur;"));

        if($anz["anzahl"] == 0) 
        {
             printf(error("Keine messwerte gefunden"));
             mysqli_close($db);
             exit();
        }
        else
        {
            printf(success($avg["durchschnitt"], $anz["anzahl"], $min["minimum"], $max["maximum"]));
        }       
    }
?>