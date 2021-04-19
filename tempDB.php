<html>
    <head>
    <title>Daten aus einer Datenbank abrufen</title>
    </head>
    <body> 
    
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

            header("Location: http://localhost/pv-a1-main/");
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
            $werte = mysqli_fetch_assoc(mysqli_query($db, "SELECT Temp as _werte FROM Temperatur;"));       

            $index = 1;

            echo"<style>
                p
                {
                    color: blue
                }
            </style>
            <div>						
            <p>Durchschnitt: {$avg['durchschnitt']} °C</p>
            <p>Anzahl: {$anz['anzahl']}</p>
            <p>Minimum: {$min['minimum']} °C</p>
            <p>Maximum: {$max['maximum']} °C</p>                          
            <p></p>
            </div>
            <table border='1'>
            <tr>
                <td>Index&nbsp;&nbsp;</td>
                <td>Werte</td>
            </tr>";

            if($anz["anzahl"] == 0)
            {
                printf(error("Keine messwerte gefunden"));
                mysqli_close($db);
                exit();
            }
            else
            {                   
                $result = mysqli_query($db, "SELECT Temp FROM Temperatur ORDER BY Temp DESC"); 

                if(mysqli_num_rows($result)) 
                {   
                    while($row = mysqli_fetch_array($result)) 
                    { 
                        echo"	               
                        <tr>
                            <td>{$index}</td> 
                            <td>{$row['Temp']}</td>
                        </tr>
                        "; 
                        $index++;
                    } 
                }

                echo"
                <table>
                    <tr>
                    <p></p>
                        <td>
                            <a href=\"Index.html\" style=\"color: purple;\">Zurück zur Eingabe</a>
                        </td>
                    </tr>
                </table>";
            }
        }          
    /* Stackoverflow Lösung
    $sql = "SELECT * FROM MY_TABLE";
    $result = mysqli_query($conn, $sql); // First parameter is just return of "mysqli_connect()" function
    echo "<br>";
    echo "<table border='1'>";
    while ($row = mysqli_fetch_assoc($result)) { // Important line !!! Check summary get row on array ..
        echo "<tr>";
        foreach ($row as $field => $value) { // I you want you can right this line like this: foreach($row as $value) {
            echo "<td>" . $value . "</td>"; // I just did not use "htmlspecialchars()" function. 
        }
        echo "</tr>";
    }
    echo "</table>";
    */
    ?>

</body>
</html>    