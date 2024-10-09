<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Algoritmo del simplesso</title>
        <!--<link href="css/myStyle.css" rel="stylesheet" type="text/css">-->
        <style>
            /*corpo della pagina*/
            .mybody_rg {
                background-color: #b8b8b877;
                font-family: 'Times New Roman', Times, serif;
                font-size: 20px;
            }

            /*titolo e nome*/
            .mytitle_db {
                font-family: fantasy;
                font-size: 60px;
                font-style: bold;
                color: rgb(0, 0, 0);
                text-align: center;
            }
            .myname {
                font-family: 'Brush Script MT', cursive;
                font-size: 20px;
                font-style: bold;
                color: rgb(0, 0, 0);
                text-align: left;
            }
            /*forma canonica vettoriale*/
            .mytext {
                padding-left: 14.5cm;
                font-size: 20;
                font-family: 'Times New Roman', Times, serif;
                /*background-color: #eeeeee70;*/
                border-radius: 10px;
                -webkit-border-radius: 10px;
            }
            .mytext:hover {
                box-shadow: 10px 10px 15px #383838c4;
            }

            /*celle per inserimento dati e componenti del form*/
            .mytext1 {
                padding-left: 1px;
            }
            label {
                padding-left: 1px;
            }
            .mytxtcell {
                width: 100px;
                border-radius: 5px;
                -webkit-border-radius: 5px;
                border: black 1px solid;
            }
            .mybutton {
                text-align: center;
                width: 100px;
                height: 20;
                font-size: large;
                background-color: rgb(0, 255, 213);
                border: dimgray 1px solid;
                border-radius: 10px;
                -webkit-border-radius: 10px;
                padding-bottom: 5px;
            }

            /*tabelle per ogni passaggio*/
            .mytable_reg {
                /*font-weight: bold;*/
                text-align: center;
                border-collapse: collapse;
                border-width: 2px;
                box-shadow: 10px 10px 15px #5f5f5fc5;
            }
            .myth {
                border-style: ridge;
                text-align: center;
                font-size: 20;
                background-color: #00ffff00;
            }
            .mytd {
                font-weight: bold;
                width: 100px;
                font-size: 20px; 
                text-align: center;
                border-style: ridge;
                background-color: #0077ff85;
            }
            .mytd_bis {
                font-weight: bold;
                width: 100px;
                font-size: 20px; 
                text-align: center;
                border-top-style: none;
                border-left-style: none;
                border-right-style: none;
                
            }
            .mytd_ter {
                font-weight: bold;
                width: 100px;
                font-size: 20px; 
                text-align: center;
                border-style: ridge;
                background-color: #ff000085;
            }
            .mytd1 {
                font-weight: bold;
                background-color: #1eff0075;
                border-style: ridge;
                border-top-style: double;
                border-color:#EEEEEE;
                min-width: 100px;
                height: 37px;
                text-align: center;
                font-size: 20px;
            }
            .mytd2 {
                font-weight: bold;
                min-width: 100px;
                border-style: ridge;
                border-top-style: double;
                border-color:#EEEEEE;
                width: fit-content;
                text-align: center;
                font-size: 20px;
                background-color: #1eff0075;
                white-space: nowrap;
            }
            .mytd2_bis {
                font-weight: bold;
                min-width: 100px;
                width: fit-content;
                font-size: 20px; 
                text-align: center;
                border-style: ridge;
                border-color:#EEEEEE;
                background-color: #0077ff85;
                white-space: nowrap;
            }
        </style>

        <?php
            
            if (isset($_POST['submit']) || isset($_POST['calc'])) {
                //input
                $n = abs(intval($_POST['variables'])); //n variabili
                $m = abs(intval($_POST['vincoli'])); //n vincoli tecnici e di variabili di scarto
                $N = $n + $m; //n colonne matrice A

                if (isset($_POST['calc'])) {
                    //dopo aver inserito i valori dei coefficienti dei vincoli, quando premo calcola, inserisco i nuovi valori negli array (vengon separati numeratore e denominatore per ogni numero considerato frazione)
                    for ($j=1;$j<=$n;$j++) {
                        $nec=0;
                        for ($k=0;$k<=strlen((string)$_POST["z_$j"]);$k++) { 
                            if (substr($_POST["z_$j"],$k,1)=="/") {
                                $nec=$k;
                            }
                        }
                        if ($nec!=0) {
                            $c[$j] = floatval(substr($_POST["z_$j"],0,$nec));
                            $c1[$j] = floatval(substr($_POST["z_$j"],$nec+1,strlen((string)$_POST["z_$j"])-$nec));
                        }
                        else {
                            $c[$j] = floatval($_POST["z_$j"]);
                            $c1[$j] = 1;
                        }
                    }
    
                    //termini noti
                    for ($i=1;$i<=$m;$i++) {
                        for ($j=1;$j<=$n+1;$j++) {
                            if ($j==$n+1) {
                                $nec=0;
                                for ($k=0;$k<=strlen((string)$_POST["$i-$j"]);$k++) {
                                    
                                    if (substr($_POST["$i-$j"],$k,1)=="/") {
                                        $nec=$k;
                                    }
                                }
                                if ($nec!=0) {
                                    $b[$i] = floatval(substr($_POST["$i-$j"],0,$nec));
                                    $b1[$i] = floatval(substr($_POST["$i-$j"],$nec+1,strlen((string)$_POST["$i-$j"])-$nec));
                                    
                                }
                                else {
                                    $b[$i] = floatval($_POST["$i-$j"]);
                                    $b1[$i] = 1;
                                }
                            }
                        }
                    }  
                
                    //coefficienti vincoli tecnici
                    for ($i=1;$i<=$m;$i++) {
                        for ($j=1;$j<=$n;$j++) {
                            $nec=0;
                            for ($k=0;$k<=strlen((string)$_POST["$i-$j"]);$k++) {
                                
                                if (substr($_POST["$i-$j"],$k,1)=="/") {
                                    $nec=$k;
                                }
                            }
                            if ($nec!=0) {
                                $A[$i][$j] = floatval(substr($_POST["$i-$j"],0,$nec));
                                $A1[$i][$j] = floatval(substr($_POST["$i-$j"],$nec+1,strlen((string)$_POST["$i-$j"])-$nec));
                            }
                            else {
                                $A[$i][$j] = floatval($_POST["$i-$j"]);
                                $A1[$i][$j] = 1;
                            }
                            
                        }
                    }
    
                    //matrice d'identità
                    $kl = 1;
                    for ($i=1;$i<=$m;$i++) {
                        for ($j=$n+1;$j<=$N;$j++) {
                            if ($i == ($j-$n)) {
                                $A[$i][$j] = 1;
                                $A1[$i][$j] = 1;
                                $pivot_past[$kl] = $i . $j;
                            }
                            else {
                                $A[$i][$j] = 0;
                                $A1[$i][$j] = 1;
                            }
                        } 
                        $kl++;   
                    }
                    
                    //inserimento dati nella tabella numeratori
                    //variabili in base
                    for ($i=1;$i<=$m;$i++) {

                        //coefficienti vincoli tecnici
                        for ($j=1;$j<=$N;$j++) {
                            $Num[$i][$j] = $A[$i][$j];
                        }

                        //termini noti vincoli 
                        $Num[$i][$N+1] = $b[$i];
                    }

                    //coefficienti funzione obiettivo inseriti nell'ultima riga della tabella
                    for ($j=1;$j<=$n;$j++) {
                        $Num[$m+1][$j] = $c[$j];
                    }

                    //il resto dell'ultima riga sono zeri
                    for ($j=$n+1;$j<=$N+1;$j++) {
                        $Num[$m+1][$j] = 0;
                    }


                    //inserimento dati nella tabella denominatori
                    //variabili in base
                    for ($i=1;$i<=$m;$i++) {

                        //coefficienti vincoli tecnici
                        for ($j=1;$j<=$N;$j++) {
                            $Den[$i][$j] = $A1[$i][$j];
                        }

                        //termini noti vincoli tecnici
                        $Den[$i][$N+1] = $b1[$i];
                    }

                    //coefficienti funzione obiettivo inseriti nell'ultima riga della tabella
                    for ($j=1;$j<=$n;$j++) {
                        $Den[$m+1][$j] = $c1[$j];
                    }
                    
                    //il resto dell'ultima riga sono 1
                    for ($j=$n+1;$j<=$N+1;$j++) {
                        $Den[$m+1][$j] = 1;
                    }

                    //caso in cui denominatore negativizza la frazione
                    for ($i=1;$i<=$m+1;$i++) {
                        for ($j=1;$j<=$N+1;$j++) {
                            if ($Den[$i][$j]<0) {
                                $Den[$i][$j] *= -1;
                                $Num[$i][$j] *= -1;
                            }
                            if ($Num[$i][$j]==0)
                                $Den[$i][$j] = 1;
                        }
                    }
                }
                if (isset($_POST['submit'])) {
                    //come valore iniziale negli array e matrici inserisco valori 0

                    for ($j=1;$j<=$n;$j++) {
                        $c[$j] = 0;
                        $c1[$j] = 1;
                    }

                    for ($i=1;$i<=$m;$i++) {
                        for ($j=1;$j<=$n+1;$j++) {
                            if ($j==$n+1){
                                $b[$i] = 0;
                                $b1[$i] = 1;
                            }
                        }
                    }

                    for ($i=1;$i<=$m;$i++) {
                        for ($j=1;$j<=$n;$j++) {
                            $A[$i][$j] = 0;
                            $A1[$i][$j] = 1;
                        }
                    }

                    //inserimento dati nella tabella numeratori
                    //variabili in base
                    for ($i=1;$i<=$m;$i++) {

                        //coefficienti vincoli tecnici
                        for ($j=1;$j<=$n;$j++) {
                            $Num[$i][$j] = $A[$i][$j];
                        }

                        //termini noti vincoli tecnici
                        $Num[$i][$N+1] = $b[$i];
                    }

                    //coefficienti funzione obiettivo inseriti nell'ultima riga della tabella
                    for ($j=1;$j<=$n;$j++) {
                        $Num[$m+1][$j] = $c[$j];
                    }

                    //il resto dell'ultima riga sono zeri
                    for ($j=$n+1;$j<=$N+1;$j++) {
                        $Num[$m+1][$j] = 0;
                    }


                    //inserimento dati nella tabella denominatori
                    //variabili in base
                    for ($i=1;$i<=$m;$i++) {

                        //coefficienti vincoli tecnici
                        for ($j=1;$j<=$n;$j++) {
                            $Den[$i][$j] = $A1[$i][$j];
                        }

                        //termini noti vincoli tecnici
                        $Den[$i][$N+1] = $b1[$i];
                    }

                    //coefficienti funzione obiettivo inseriti nell'ultima riga della tabella
                    for ($j=1;$j<=$n;$j++) {
                        $Den[$m+1][$j] = $c1[$j];
                    }
                    
                    //il resto dell'ultima riga sono 1
                    for ($j=$n+1;$j<=$N+1;$j++) {
                        $Den[$m+1][$j] = 1;
                    }
                }    
            }
        ?>
    </head>

    <body class="mybody_rg">
        <a class="myname">Conti&nbsp;&nbsp;Dott.&nbsp;Matteo</a> 
        <p class="mytitle_db">Algoritmo del simplesso</p>
        <p>&nbsp;&nbsp;Risolutore di problemi di programmazione lineare tramite algoritmo del simplesso, riportante tavole del simplesso per ogni passaggio fino alla soluzione (con &nbsp;&nbsp;espressa indicazione del pivot che condurrà al passaggio successivo), in forma canonica:</p>
        <p class="mytext">
            max &nbsp;c<sup>T</sup>x <br>
            sub &nbsp;&nbsp;Ax &#8804; b <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;x &#8805; 0<br><br>
            c &#8712; &#8477;<sup><i>n</i></sup> : coefficienti funzione obiettivo<br>
            b &#8712; &#8477;<sup><i>m</i></sup> : termini noti<br>
            A &#8712; &#8477;<sup><i>n</i>&#10005;<i>m</i></sup> : coefficienti dei vincoli
        </p>
        <br><br>
            
        <!--form dinamico che crea tante caselle di testo quanti coefficienti è necessario inserire-->

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            &nbsp;&nbsp;Inserisci numero di variabili e vincoli e clicca invia:<br><br>
            &nbsp;&nbsp;Numero variabili <i>n</i> (non superiore a 10) <input type="number" name="variables" class="mytxtcell" value="<?php if (isset($_POST['submit']) || isset($_POST['calc'])) {echo $n;}?>" max = "10" min = "1"><br><br>
            &nbsp;&nbsp;Numero vincoli tecnici <i>m</i> (non superiore a 10) <input type="number" name="vincoli" class="mytxtcell" value="<?php if (isset($_POST['submit']) || isset($_POST['calc'])) {echo $m;}?>" max = "10" min  = "1"><br><br>
            &nbsp;&nbsp;<input type="submit" name="submit" value="Invia" class="mybutton"><br>
        

            <?php
                if (isset($_POST['submit']) || isset($_POST['calc']) && $n>0 && $m>0) {
                    echo "<br>&nbsp;&nbsp;Inserisci i coefficienti <i>c<sub>i</sub></i> della funzione obiettivo (inserire i dati non interi unicamente in formato di frazione, ad esempio 3/2, non utilizzare il separatore per le &nbsp;&nbsp;migliaia).<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>Z</label>&nbsp;&nbsp;=&nbsp;\n";
                    echo "<a class=\"mytext1\">\n";

                    //caselle di testo per i coefficienti della funzione obiettivo      
                    for ($j=1;$j<=$n;$j++) {
                        if ($c[$j]==0) {
                            //$c[$j] = rand(-30,30) . "/" . rand(1,30);
                            $c[$j] = "";
                        }
                        //stampando i coefficienti, si tiene conto del caso in cui le variabili siano in numero superiore a 10, al fine di stampare i pedici corretti. Altrettanto viene fatto stampado i vincoli di segno, le variabili in base e fuori base al termine ed le variabili ad intestazione di riche e colonne delle tabelle lungo il procedimento
                        if ($j<=9) {
                            if ($c[$j]>=0) {
                                if ($Den[$m+1][$j] != 1) {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"+" . $c[$j] . "/" . $c1[$j] . "\">&nbsp;x&#832$j\n";
                                }
                                else {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"+" . $c[$j] . "\">&nbsp;x&#832$j\n";
                                }
                            }
                            else {
                                if ($Den[$m+1][$j] != 1) {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"" . $c[$j] . "/" . $c1[$j] . "\">&nbsp;x&#832$j\n";
                                }
                                else {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"$c[$j]\">&nbsp;x&#832$j\n";
                                }
                            }
                        }
                        else {
                            if ($c[$j]>=0) {
                                if ($Den[$m+1][$j] != 1) {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"+" . $c[$j] . "/" . $c1[$j] . "\">&nbsp;x";
                                }
                                else {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"+$c[$j]\">&nbsp;x";
                                }                               
                            }
                            else {
                                if ($Den[$m+1][$j] != 1) {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"" . $c[$j] . "/" . $c1[$j] . "\">&nbsp;x";
                                }
                                else {
                                    echo "&nbsp;&nbsp;<input type=\"text\" name=\"z_$j\" class=\"mytxtcell\" value=\"$c[$j]\">&nbsp;x";
                                }
                            }
                            for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                echo "&#832" . substr($j,$l,1);
                            }
                            echo "\n";
                        }
                        //dopo l'ottavo coefficiente va a capo
                        if (floor($j/8) == $j/8)
                            echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                    } 
                    echo "</a>\n";
                    echo "<br><br>";                       
                    
                    //caselle di testo per i coefficienti dei vincoli tecnici, ogni variabile un coefficiente; 0 nel caso non si voglia inserire quella variabile nel vincolo
                    echo "&nbsp;&nbsp;Inserire i coefficienti <i>a<sub>i, j</sub></i> delle funzioni di vincolo ed i termini noti <i>b<sub>i</sub></i> (inserire i dati non interi unicamente in formato di frazione, non utilizzare il separatore per le &nbsp;&nbsp;migliaia)<br><br>\n";
                    for ($i=1;$i<=$m;$i++) {
                        for ($j=1;$j<=$n+1;$j++) {
                            if ($j == $n+1) {
                                if ($b[$i]==0) {
                                    //$b[$i] = rand(3,100) . "/" . rand(1,60);
                                    $b[$i] = "";
                                }
                                
                                if ($Den[$i][$N+1] != 1) {
                                    echo "&nbsp;&nbsp;&nbsp;&#8804;&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"" . $b[$i] . "/" . $b1[$i] . "\">";
                                }
                                else {
                                    echo "&nbsp;&nbsp;&nbsp;&#8804;&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"$b[$i]\">\n";
                                }

                                if ($b[$i]=="") {
                                    $b[$i] = 0;
                                }
                            }
                            else {
                                //inserisco temporaneamente un valore "" nell'array, solo per stamparlo e poi reinserisco 0 come valore iniziale dei coefficienti
                                if ($A[$i][$j]==0) {
                                    //$A[$i][$j] = rand(-40,50) . "/" . rand(1,50);
                                    $A[$i][$j] = "";
                                }

                                if ($j==1) {
                                    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
                                }

                                if ($j<=9) {
                                    if ($A[$i][$j]>=0) {
                                        if ($Den[$i][$j] != 1) {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"+" . $A[$i][$j] . "/" . $A1[$i][$j] . "\">&nbsp;x&#832$j\n";
                                        }
                                        else {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"+" . $A[$i][$j] . "\">&nbsp;x&#832$j\n";
                                        }                                       
                                    }
                                    else {
                                        if ($Den[$i][$j] != 1) {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"" . $A[$i][$j] . "/" . $A1[$i][$j] . "\">&nbsp;x&#832$j\n";
                                        }
                                        else {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"" . $A[$i][$j] . "\">&nbsp;x&#832$j\n";
                                        }
                                    }
                                }
                                else {
                                    if ($A[$i][$j]>=0) {
                                        if ($Den[$i][$j] != 1) {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"+" . $A[$i][$j] . "/" . $A1[$i][$j] . "\">&nbsp;x";
                                        }
                                        else {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"+" . $A[$i][$j] . "\">&nbsp;x";
                                        }
                                    }
                                    else {
                                        if ($Den[$i][$j] != 1) {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"" . $A[$i][$j] . "/" . $A1[$i][$j] . "\">&nbsp;x";
                                        }
                                        else {
                                            echo "&nbsp;&nbsp;<input type=\"text\" name=\"$i-$j\" class=\"mytxtcell\" value=\"" . $A[$i][$j] . "\">&nbsp;x";
                                        }
                                    }
                                    for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                        echo "&#832" . substr($j,$l,1);
                                    }
                                    echo "\n";
                                }                                 

                                if ($A[$i][$j]=="") {
                                    $A[$i][$j] = 0;
                                }
                            }
                            if (floor($j/8) == $j/8)
                                echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                        echo "<br><br><br>\n";
                    }

                    //stampo i vincoli di segno e correggo quando si va a capo
                    echo "&nbsp;&nbsp;Vincoli di segno delle variabili <br><br>&nbsp;&nbsp;&nbsp;&nbsp;\n";
                    for ($k=1;$k<=$N;$k++) {
                        if ($k<=$n) {
                            if ($k<=9) {echo "&nbsp;&nbsp;x&#832" . $k . ";&nbsp;&#8805;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
                            else {
                                echo "&nbsp;&nbsp;x";
                                for ($l=0;$l<=strlen((string)$k)-1;$l++) {
                                    echo "&#832" . substr($k,$l,1);
                                }
                                echo ";&nbsp;&#8805;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                            }
                            if ($k==$n) {echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";}
                        }
                        else {
                            if ($k<=9) {echo "&nbsp;&nbsp;s&#832" . $k-$n . ";&nbsp;&#8805;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;\n";}
                            else {
                                echo "&nbsp;&nbsp;s";
                                for ($l=0;$l<=strlen((string)$k-$n)-1;$l++) {
                                    echo "&#832" . substr($k-$n,$l,1);
                                }
                                echo ";&nbsp;&#8805;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;\n";
                            }
                            
                        }
                        if ($k<=$n && floor(($k)/14) == ($k)/14) {
                            echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                        if ($k>$n && floor(($k-$n)/14) == ($k-$n)/14) {
                            echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                        }
                    }
                    echo "<br><br>\n";
                    echo "&nbsp;&nbsp;<input type=\"submit\" name=\"calc\" value=\"Calcola\" class=\"mybutton\"><br><br>"; //impulso al calcolo
                    
                    //array variabili di stato, poichè sono le uniche che possono entrare in base
                    for ($k=1;$k<=$n;$k++) {
                        $var[$k] = "x&#832$k;";
                    }
                }
            ?>
        </form>

        <?php   
            /*commento necessario per spiegare la struttura di array e matrici
            
            $c = [
                1 => 200,
                2 => 150
            ];

            $b = [
                1 => 180,
                2 => 100,
                3 => 20
            ];

            //matrice di coefficienti, entrambi i metodi
            $A[1][1] = 3; $A[1][2] = 2; 
            $A[2][1] = 1; $A[2][2] = 2; 
            $A[3][1] = 1; $A[3][2] = -2; 

            $A_bis = [ 
                1 => [
                    1 => 3,
                    2 => 2
                ],

                2 => [
                    1 => 1,
                    2 => 2
                ],

                3 => [
                    1 => 1,
                    2 => -2
                ],
            ];*/
            
            //improcedibilità in caso di selezione di entrambe le alternative checkbox
            if (isset($_POST['calc'])) {
                echo "&nbsp;&nbsp;N.B. la casellina rossa indica il pivot che verrà utilizzato al fine di giungere alla tabella successiva<br><br><br><br>";
                echo "<center>";
                $argo = false;
                $degenere = false;
                $gel = false;
                //controllo situazione assurda di funzione non migliorabile in partenza
                $p=0;
                $q=0;
                $s=0;
                for ($c=1;$c<=$n;$c++) {
                    if ($Num[$m+1][$c]>0) 
                        $p++;
                }
                if ($p==0) {
                    echo "&nbsp;&nbsp;Impossibile migliorare<br>";
                }
                else {
                    //controllo base ammissibile eventualmente degenere: inseriti termini noti nei vincoli anche non tutti nulli
                    for ($c=1;$c<=$m;$c++) {
                        if ($Num[$c][$N+1]==0) {$q++;}
                            

                        if ($Num[$c][$N+1]<0) {$s++;}
                             
                    }
                    if ($q!=0 && $s==0){
                        echo "&nbsp;&nbsp;Sono stati inseriti $q termini noti nulli, perciò la soluzione di base è degenere<br><br><br>";
                    }
                    if ($s==0) {
                        //prima individuazione della posizione del pivot al fine di individuarlo in rosso prima di eseguire i calcoli sulla tabella
                        //individuazione colonna cardine, ovvero quella con il costo marginale superiore
                        //colonna della variabile che entra in base, quella che nella prima riga ha il valore maggiore di 0 più basso, poichè fa crescere la funzione meno rapidamente
                        for ($j=1;$j<=$N;$j++) {    
                            //non è necessario porre >0 poichè se non ve ne fosse almeno uno positivo (che sarà il maggiore) non saremmo giunti qui
                            if ($j == 1) {
                                $j_max_num = $Num[$m+1][$j];
                                $j_max_den = $Den[$m+1][$j];
                                $j_pivot = $j;
                            }
                            //se la frazione è la maggiore, allora, sottraendovi tutte le altre, il risultato sarà positivo: trovo mcm e faccio den comune
                            $c = 1;
                            while ($c>0) {
                                if ($j_max_den >= $Den[$m+1][$j]) {
                                    if (floor($j_max_den*$c/$Den[$m+1][$j]) == $j_max_den*$c/$Den[$m+1][$j]) {
                                        $mcm = $j_max_den*$c;
                                        break 1;
                                    }
                                }
                                else {
                                    if (floor($Den[$m+1][$j]*$c/$j_max_den) == $Den[$m+1][$j]*$c/$j_max_den) {
                                        $mcm = $Den[$m+1][$j]*$c;
                                        break 1;
                                    }
                                }
                                $c++;
                            }
                            
                            if ($j_max_num*($mcm/$j_max_den) - $Num[$m+1][$j]*($mcm/$Den[$m+1][$j]) < 0) {
                                $j_max_num = $Num[$m+1][$j];
                                $j_max_den = $Den[$m+1][$j];
                                $j_pivot = $j;
                            }
                        }

                        //condizione sufficiente di illimitatezza (tutti coefficienti della colonna cardine sono <0)
                        $q=0;
                        for ($v=1;$v<=$m;$v++) {
                            if ($Num[$v][$j_pivot]<0) {
                                $q++;
                            }
                        }
                        if ($q == $m) {
                            echo "&nbsp;&nbsp;Non vi è soluzione, a causa dell'illimitatezza della regione ammissibile e della forma della funzione obiettivo<br>";
                        }
                        else {
                            //semplifico rapporti inseriti
                            for ($p=1;$p<=$n;$p++) {
                                for ($q=1;$q<=$m+1;$q++) {
                                    $MCD=1;
                                    $gi=1;
                                    while ($gi<=abs($Num[$q][$p]) && $gi<=$Den[$q][$p]) {
                                        if (floor(abs($Num[$q][$p])/$gi) == abs($Num[$q][$p])/$gi && floor($Den[$q][$p]/$gi) == $Den[$q][$p]/$gi) {
                                            $MCD = $gi;
                                        }
                                        $gi++;
                                    }
                                    if ($MCD != 1) {
                                        $Num[$q][$p] /= $MCD;
                                        $Den[$q][$p] /= $MCD;
                                    }
                                }
                            }

                            //individuo la linea del cardine, ovvero la variabile uscente, se la condizione di illimitatezza è rispettata
                            $brake = false;
                            $min_den = -1;       //dichiaro un valore solo come confronto per vedere se ci sono state successive assegnazioni
                            $ff = 1;
                            for ($i=1;$i<=$m;$i++) {

                                //faccio in modo di non dividere per 0
                                if ($Num[$i][$j_pivot] != 0) {
                                    $res_num = $Num[$i][$N+1]*$Den[$i][$j_pivot];
                                    $res_den = $Den[$i][$N+1]*$Num[$i][$j_pivot];
                                    
                                    if ($res_num != 0 || $res_den>0) {
                                        if ($res_den<0) {
                                            $res_num  *= -1;
                                            $res_den *= -1;
                                        }
                                        if ($res_num==0) {$res_den = 1;}
                                    }  

                                    //semplifico rapporto
                                    $MCD=1;
                                    $gi=1;
                                    while ($gi<=abs($res_num) && $gi<=$res_den) {
                                        if (floor(abs($res_num)/$gi) == abs($res_num)/$gi && floor($res_den/$gi) == $res_den/$gi) {
                                            $MCD = $gi;
                                        }
                                        $gi++;
                                    }
                                    if ($MCD != 1) {
                                        $res_num /= $MCD;
                                        $res_den /= $MCD;
                                    }
                                    
                                    $first = false;
                                    if ($i == 1) {

                                        //se res minore di 0, allora procedo con il controllo delle successive righe
                                        if ($res_num<0 || $res_den<0) {
                                            
                                            //contollo se tra le possibilità è presente un valore che rispetti gli standard, il primo trovato viene preso come minimo in modo da sovrascrivere la variabile con un valore e poter continuare il ciclo da un punto di partenza esatto
                                            for ($i=2;$i<=$m;$i++) {
                                                if ($Num[$i][$j_pivot]!=0) {
                                                    $rex_num = $Num[$i][$N+1]*$Den[$i][$j_pivot];
                                                    $rex_den = $Den[$i][$N+1]*$Num[$i][$j_pivot];
                                                    if ($rex_num>=0 && $rex_den>0) {
                                                        $argo = true;
                                                        $first = true;
                                                        $min_num = $rex_num;
                                                        $min_den = $rex_den;
                                                        $amb[$ff] = $Num[$i][$j_pivot];         //array che tiene memoria di tutti i numeratori, denominatori e numero riga dei candidati pivot individuati nella colonna pivot
                                                        $amb1[$ff] = $Den[$i][$j_pivot];
                                                        $amb2[$ff] = $i;
                                                        $min[$ff] = $min_num/$min_den;      //array che tiene memoria dei rapporti collegati ai suddetti
                                                        $ff++;
                                                        $i_pivot = $i;
                                                        break 1;
                                                    }
                                                }
                                            }
                                        } 
                                        else {
                                            $min_num = $res_num;
                                            $min_den = $res_den;
                                            $amb[$ff] = $Num[$i][$j_pivot];
                                            $amb1[$ff] = $Den[$i][$j_pivot];
                                            $amb2[$ff] = $i;
                                            $min[$ff] = $min_num/$min_den;
                                            $ff++;
                                            $i_pivot = $i;
                                            $argo = true;
                                            $first = true;
                                        }
                                    }
                                    
                                    //alla prima occasione nella quale non si divide per 0, nel caso fossimo oltre alla riga 2, assegno un valore di partenza se rispetta le condizioni necessarie
                                    if ($brake && $min_den == -1 && $res_num>=0 && $res_den>0) {
                                        $min_num = $res_num;
                                        $min_den = $res_den;
                                        $amb[$ff] = $Num[$i][$j_pivot];
                                        $amb1[$ff] = $Den[$i][$j_pivot];
                                        $amb2[$ff] = $i;
                                        $min[$ff] = $min_num/$min_den;
                                        $ff++;
                                        $i_pivot = $i;
                                        $argo = true;
                                    }

                                    //controllo se il prossimo valore è minore del minimo fin'ora trovato, ovvero faccio differenza tra frazioni per comprendere se positiva
                                    $c = 1;
                                    while ($c>0) {
                                        if ($min_den >= $res_den) {
                                            if (floor($min_den*$c/$res_den) == $min_den*$c/$res_den) {
                                                $mcm = $min_den*$c;
                                                break 1;
                                            }
                                        }
                                        else {
                                            if (floor($res_den*$c/$min_den) == $res_den*$c/$min_den) {
                                                $mcm = $res_den*$c;
                                                break 1;
                                            }
                                        }
                                        $c++;
                                    }
                                    if ($min_num*($mcm/$min_den) - $res_num*($mcm/$res_den) >= 0 && $res_num>=0 && $res_den>0 && $first==false && ($res_num!=$min_num || $res_den!=$min_den)) {
                                        $min_num = $res_num;
                                        $min_den = $res_den;
                                        $amb[$ff] = $Num[$i][$j_pivot];
                                        $amb1[$ff] = $Den[$i][$j_pivot];
                                        $amb2[$ff] = $i;
                                        $min[$ff] = $min_num/$min_den;
                                        $ff++;
                                        $i_pivot = $i;
                                    }
                                    //se un nuovo pivot avesse il medesimo rapporto di uno già individuato, si fa in modo venga preso ed utilizzato il pivot inferiore, oppure il primo individuato se anche il pivot fosse il medesimo
                                    if ($ff>2) {
                                        if ($min[$ff-1] == $min[$ff-2]) {
                                            for ($hg=2;$hg<=$ff-1;$ff++) {
                                                $c=1;
                                                while ($c>0) {
                                                    if ($amb1[$hg-1] >= $amb1[$hg]) {
                                                        if (floor($amb1[$hg-1]*$c/$amb1[$hg]) == $amb1[$hg-1]*$c/$amb1[$hg]) {
                                                            $mcm = $amb1[$hg-1]*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    else {
                                                        if (floor($amb1[$hg]*$c/$amb1[$hg-1]) == $amb1[$hg]*$c/$amb1[$hg-1]) {
                                                            $mcm = $amb1[$hg]*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    $c++;
                                                }
                                                if ($amb[$hg-1]*($mcm/$amb1[$hg-1]) - $amb[$hg]*($mcm/$amb1[$hg]) <= 0) {
                                                    $i_pivot = $amb2[$hg-1];
                                                    break 1;
                                                }
                                                else {
                                                    $i_pivot = $amb2[$hg];
                                                    break 1;
                                                }
                                            }
                                        }
                                    }
                                    $brake = false; 
                                    
                                }
                                else {
                                    $brake = true; 
                                }
                            }
                        }
                        
                        //se è stato individuato il pivot, si procederà con lo stampaggio della tabella
                        if ($argo) {
                            //stampo tabella di partenza                             
                            echo "<center><table class=\"mytable_reg\">";
                            echo "<tr><td class=\"mytd_bis\"></td>";
                            
                            //intestazione colonne
                            $lp = 0;
                            for ($j=1;$j<=$n;$j++) {
                                if ($j<=9) {
                                    echo "<td class=\"myth\">x&#832" . $j . ";</td>";
                                    $lp++;
                                    $hum[$lp] = "x&#832" . $j;
                                }
                                else {
                                    echo "<td class=\"myth\">x";
                                    $lp++;
                                    $hum[$lp] = "x&#832";
                                    for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                        echo "&#832" . substr($j,$l,1);
                                        $hum[$lp] .= substr($j,$l,1);
                                    }
                                    echo ";</td>";
                                }
                                
                            }
                            for ($j=1;$j<=$m;$j++) {
                                if ($j<=9) {
                                    echo "<td class=\"myth\">s&#832" . $j . ";</td>";
                                    $lp++;
                                    $hum[$lp] = "s&#832" . $j;
                                }
                                else {
                                    echo "<td class=\"myth\">s";
                                    $lp++;
                                    $hum[$lp] = "s&#832";
                                    for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                        echo "&#832" . substr($j,$l,1);
                                        $hum[$lp] .= substr($j,$l,1);
                                    }
                                    echo ";</td>";
                                }
                                
                            }
                            echo "<td class=\"mytd_bis\"></td>\n";

                            //intestazione righe iniziale
                            $g = 0;
                            for ($i=1;$i<=$m+1;$i++) {
                                echo "<tr>"; 

                                if ($i==$m+1) {
                                    echo "<td class=\"myth\"></td>\n";
                                }
                                else {
                                    $g++;
                                    if ($g<=9) {
                                        echo "<td class=\"myth\">s&#832" . $g . ";</td>";
                                    }
                                    else {
                                    echo "<td class=\"myth\">s";
                                    for ($l=0;$l<=strlen((string)$g)-1;$l++) {
                                        echo "&#832" . substr($g,$l,1);
                                    }
                                    echo ";</td>";
                                    }
                                }
                                
                                //inserimento valori riga tenendo in considerazione il pivot iniziale, i valori della funzione in frazione (mantenuta possibilità di calcolo decimale)
                                for ($j=1;$j<=$N+1;$j++) {
                                    if ($i != $m+1) {
                                        if ($Den[$i][$j] != 1) {
                                            if ($j != $N+1) {
                                                if ($i == $i_pivot && $j == $j_pivot) {
                                                echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],0,".","") . "/" .  number_format($Den[$i][$j],0,".","") . "</td>\n";
                                                }
                                                else {
                                                echo "<td class=\"mytd\">" . number_format($Num[$i][$j],0,".","") . "/" .  number_format($Den[$i][$j],0,".","") . "</td>\n";
                                                }
                                            }
                                            /*if (floor($Num[$i][$j]) != $Num[$i][$j] && $j != $N+1) {
                                                if ($i == $i_pivot && $j == $j_pivot) {
                                                    echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }
                                                else {
                                                    echo "<td class=\"mytd\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }
                                            }*/
                                            if ($j == $N+1) {
                                                echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],0,".","") . "/" .  number_format($Den[$i][$j],0,".","") . "</td>\n";
                                            }
                                            /*if (floor($Num[$i][$j]) != $Num[$i][$j] && $j == $N+1) {
                                                echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                
                                            }*/
                                        }
                                        else {
                                            if ($j != $N+1) {
                                                if ($i == $i_pivot && $j == $j_pivot) {
                                                echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                                }
                                                else {
                                                echo "<td class=\"mytd\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                                }
                                            }
                                            /*if ($j != $N+1) {
                                                if ($i == $i_pivot && $j == $j_pivot) {
                                                    echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }
                                                else {
                                                    echo "<td class=\"mytd\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }
                                            }*/
                                            if ($j == $N+1) {
                                                echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                                
                                            }
                                            /*if (floor($Num[$i][$j]) != $Num[$i][$j] && $j == $N+1) {
                                                echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                
                                            }*/
                                        }

                                        
                                    }
                                    else {
                                        if ($Den[$i][$j] != 1) {
                                            if ($j!=$N+1) {
                                                //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                    echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,".","") . "/" .  number_format($Den[$i][$j],0,".","") . "</td>\n";
                                                //}
                                                /*else {
                                                    echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }*/
                                            }
                                            else {
                                                //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                    echo "<td class=\"mytd2\">" . "Z - " . number_format($Num[$i][$j],0,".","") . "/" .  number_format($Den[$i][$j],0,".","") . "</td>\n";
                                                //}
                                                /*else {
                                                    echo "<td class=\"mytd2\">" . "Z - " . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }*/
                                            }
                                        }
                                        else {
                                            if ($j!=$N+1) {
                                                //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                    echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                                /*}
                                                else {
                                                    echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }*/
                                            }
                                            else {
                                                //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                    echo "<td class=\"mytd2\">" . "Z - " . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                                /*}
                                                else {
                                                    echo "<td class=\"mytd2\">" . "Z - " . number_format($Num[$i][$j],3,"."," ") . "</td>\n";
                                                }*/
                                            }
                                        }
                                    }

                                    
                                }
                                echo "</tr>\n";
                            }
                            echo "</table><br><br></center>\n";

                            echo "La soluzione di base indviduata non è ottimale<br><br>";
                        }
                    }
                    else {
                        echo "<center>Sono stati inseriti $s termini noti negativi, perciò la soluzione di base non è ammissibile: se non è possibile sostiuirli tutti con valori non negativi, allora la regione ammissibile è vuota e nessun miglioramento potrà essere posto in essere</center>";
                    }
                }
                //funzione che osserva se si può migliorare, quindi se nella funzione obiettivo ci sono coefficineti maggiori di 0, che fanno crescere la funzione (può essere violata in caso di souluzione degenere)
                $migl = false;
                $degenere = false;
                $again=0;
                function miglioramento ($Num,$Den,$N,$m,$migl,$p,$s,$argo,$kl,$degenere,$again) {
                    
                    //consideriamo anche le situazioni di non migliorabilità ed illimitatezza, nonchè di mancata individuazione del pivot in primo giro
                    for ($j=1;$j<=$N;$j++) {
                        
                        if ($Num[$m+1][$j] > 0 && $p != 0 && $s == 0 && $argo) {
                            $migl = true;
                            break;
                        }
                    }

                    return $migl;
                }

                //se il miglioramento è possibile procedo i calcoli necessari
                while (miglioramento($Num,$Den,$N,$m,$migl,$p,$s,$argo,$kl,$degenere,$again)) {
                    $again++;

                    //colonna della variabile che entra in base, quella che nella prima riga ha il coefficiente maggiore di 0 più alto
                    for ($j=1;$j<=$N;$j++) {    

                        //non è necessario porre >0 poichè se non ve ne fosse almeno uno positivo (che sarà il maggiore) non saremmo giunti qui
                        if ($j == 1) {
                            $j_max_num = $Num[$m+1][$j];
                            $j_max_den = $Den[$m+1][$j];
                            $j_pivot = $j;
                        }
                        //se la frazione è la maggiore, allora, sottraendovi tutte le altre, il risultato sarà positivo: trovo mcm e faccio den comune
                        $c = 1;
                        while ($c>0) {
                            if ($j_max_den > $Den[$m+1][$j]) {
                                if (floor($j_max_den*$c/$Den[$m+1][$j]) == $j_max_den*$c/$Den[$m+1][$j]) {
                                    $mcm = $j_max_den*$c;
                                    break 1;
                                }
                            }
                            else {
                                if (floor($Den[$m+1][$j]*$c/$j_max_den) == $Den[$m+1][$j]*$c/$j_max_den) {
                                    $mcm = $Den[$m+1][$j]*$c;
                                    break 1;
                                }
                            }
                            $c++;
                        }
                        
                        if ($j_max_num*($mcm/$j_max_den) - $Num[$m+1][$j]*($mcm/$Den[$m+1][$j]) < 0) {
                            $j_max_num = $Num[$m+1][$j];
                            $j_max_den = $Den[$m+1][$j];
                            $j_pivot = $j;
                        }
                    }
                    
                    //individuo la linea del cardine, ovvero la variabile uscente, se la condizione di illimitatezza è rispettata
                    $brake = false;
                    $min_den = -1;       //dichiaro un valore solo come confronto per vedere se ci sono state successive assegnazioni
                    $ff = 1;
                    $ix = 0; //controllo individuazione pivot
                    for ($i=1;$i<=$m;$i++) {

                        //faccio in modo di non dividere per 0
                        if ($Num[$i][$j_pivot] != 0) {
                            $res_num = $Num[$i][$N+1]*$Den[$i][$j_pivot];
                            $res_den = $Den[$i][$N+1]*$Num[$i][$j_pivot];
                            
                            if ($res_num != 0 || $res_den>0) {
                                if ($res_den<0) {
                                    $res_num  *= -1;
                                    $res_den *= -1;
                                }
                                if ($res_num==0) {$res_den = 1;}
                            }
                            
                            //semplifico rapporto
                            $MCD=1;
                            $gi=1;
                            while ($gi<=abs($res_num) && $gi<=$res_den) {
                                if (floor(abs($res_num)/$gi) == abs($res_num)/$gi && floor($res_den/$gi) == $res_den/$gi) {
                                    $MCD = $gi;
                                }
                                $gi++;
                            }
                            if ($MCD != 1) {
                                $res_num /= $MCD;
                                $res_den /= $MCD;
                            }

                            $first = false;
                            if ($i == 1) {

                                //se res minore di 0, allora procedo con il controllo delle successive righe
                                if ($res_num<0 || $res_den<0) {

                                    //contollo se tra le possibilità è presente un valore che rispetti gli standard, il primo trovato viene preso come minimo in modo da sovrascrivere la variabile con un valore e poter continuare il ciclo da un punto di partenza esatto
                                    for ($i=2;$i<=$m;$i++) {
                                        if ($Num[$i][$j_pivot]!=0) {
                                            $rex_num = $Num[$i][$N+1]*$Den[$i][$j_pivot];
                                            $rex_den = $Den[$i][$N+1]*$Num[$i][$j_pivot];
                                            if ($rex_num>=0 && $rex_den>0) {
                                                $argo = true;
                                                $first = true;
                                                $min_num = $rex_num;
                                                $min_den = $rex_den;
                                                $amb[$ff] = $Num[$i][$j_pivot];
                                                $amb1[$ff] = $Den[$i][$j_pivot];
                                                $amb2[$ff] = $i;
                                                $min[$ff] = $min_num/$min_den;
                                                $ff++;
                                                $ix++;
                                                $i_pivot = $i;
                                                break 1;
                                            }
                                        }
                                    }
                                } 
                                else {
                                    $min_num = $res_num;
                                    $min_den = $res_den;
                                    $amb[$ff] = $Num[$i][$j_pivot];
                                    $amb1[$ff] = $Den[$i][$j_pivot];
                                    $amb2[$ff] = $i;
                                    $min[$ff] = $min_num/$min_den;
                                    $ff++;
                                    $ix++;
                                    $i_pivot = $i;
                                    $argo = true;
                                    $first = true;
                                }
                            }
                                
                            //alla prima occasione nella quale non si divide per 0, nel caso fossimo oltre alla riga 2, assegno un valore di partenza se rispetta le condizioni necessarie
                            if ($brake && $min_den == -1 && $res_num>=0 && $res_den>0) {
                                $min_num = $res_num;
                                $min_den = $res_den;
                                $amb[$ff] = $Num[$i][$j_pivot];
                                $amb1[$ff] = $Den[$i][$j_pivot];
                                $amb2[$ff] = $i;
                                $min[$ff] = $min_num/$min_den;
                                $ff++;
                                $ix++;
                                $i_pivot = $i;
                                $argo = true;
                            }

                            //controllo se il prossimo valore è minore del minimo fin'ora trovato, ovvero faccio differenza tra frazioni per comprendere se positiva
                            $c = 1;
                            while ($c>0) {
                                if ($min_den >= $res_den) {
                                    if (floor($min_den*$c/$res_den) == $min_den*$c/$res_den) {
                                        $mcm = $min_den*$c;
                                        break 1;
                                    }
                                }
                                else {
                                    if (floor($res_den*$c/$min_den) == $res_den*$c/$min_den) {
                                        $mcm = $res_den*$c;
                                        break 1;
                                    }
                                }
                                $c++;
                            }
                            if ($min_num*($mcm/$min_den) - $res_num*($mcm/$res_den) >= 0 && $res_num>=0 && $res_den>0 && $first==false && ($res_num!=$min_num || $res_den!=$min_den)) {
                                $min_num = $res_num;
                                $min_den = $res_den;
                                $amb[$ff] = $Num[$i][$j_pivot];
                                $amb1[$ff] = $Den[$i][$j_pivot];
                                $amb2[$ff] = $i;
                                $min[$ff] = $min_num/$min_den;
                                $ff++;
                                $i_pivot = $i;
                            }
                            if ($ff>2) {
                                if ($min[$ff-1] == $min[$ff-2]) {
                                    for ($hg=2;$hg<=$ff-1;$ff++) {
                                        $c=1;
                                        while ($c>0) {
                                            if ($amb1[$hg-1] >= $amb1[$hg]) {
                                                if (floor($amb1[$hg-1]*$c/$amb1[$hg]) == $amb1[$hg-1]*$c/$amb1[$hg]) {
                                                    $mcm = $amb1[$hg-1]*$c;
                                                    break 1;
                                                }
                                            }
                                            else {
                                                if (floor($amb1[$hg]*$c/$amb1[$hg-1]) == $amb1[$hg]*$c/$amb1[$hg-1]) {
                                                    $mcm = $amb1[$hg]*$c;
                                                    break 1;
                                                }
                                            }
                                            $c++;
                                        }
                                        if ($amb[$hg-1]*($mcm/$amb1[$hg-1]) - $amb[$hg]*($mcm/$amb1[$hg]) <= 0) {
                                            $i_pivot = $amb2[$hg-1];
                                            break 1;
                                        }
                                        else {
                                            $i_pivot = $amb2[$hg];
                                            break 1;
                                        }
                                    }
                                }
                                
                                $brake = false; 
                            }   
                        }
                        else {
                            $brake = true; 
                        }
                    }
                    
                    if ($ix!=0) {
                        $pivot_num = $Num[$i_pivot][$j_pivot];
                        $pivot_den = $Den[$i_pivot][$j_pivot];
                        $pivot_past_bis = $pivot_past[$i_pivot];
                        $pivot_past[$i_pivot] = $i_pivot . $j_pivot;
                        
                        //tengo memoria della tabella del passaggio precedente
                        for ($i=1;$i<=$m+1;$i++) {
                            for ($j=1;$j<=$N+1;$j++) {
                                $Num_bis[$i][$j] = $Num[$i][$j];
                                $Den_bis[$i][$j] = $Den[$i][$j];
                            }
                        }
                        
                        //operazioni elementari
                        //divido per il pivot tutta la riga del pivot
                        for ($j=1;$j<=$N+1;$j++) {
                            $Num[$i_pivot][$j] *= $pivot_den;
                            $Den[$i_pivot][$j] *= $pivot_num;

                            //caso in cui denominatore negativizza la frazione
                            for ($hi=1;$hi<=$m+1;$hi++) {
                                for ($hj=1;$hj<=$N+1;$hj++) {
                                    if ($Den[$hi][$hj]<0) {
                                        $Den[$hi][$hj] *= -1;
                                        $Num[$hi][$hj] *= -1;
                                    }
                                    if ($Num[$hi][$hj]==0)
                                        $Den[$hi][$hj] = 1;
                                }
                            }
                            
                            $MCD=1;
                            $gi=1;
                            while ($gi<=abs($Num[$i_pivot][$j]) && $gi<=$Den[$i_pivot][$j]) {
                                if (floor(abs($Num[$i_pivot][$j])/$gi) == abs($Num[$i_pivot][$j])/$gi && floor($Den[$i_pivot][$j]/$gi) == $Den[$i_pivot][$j]/$gi) {
                                    $MCD = $gi;
                                }
                                $gi++;
                            }

                            if ($MCD != 1) {
                                $Num[$i_pivot][$j] /= $MCD;
                                $Den[$i_pivot][$j] /= $MCD;
                            }
                            
                        }

                        //trasformazione di Gauss: rende la colonna di solo zeri ed 1, insieme modifica anche le altre righe
                        for ($i=1;$i<=$m+1;$i++) {

                            if ($i != $i_pivot) {

                                $rem_num = $Num[$i][$j_pivot]; //frazione che moltiplico a 1 per azzerare frazioni sotto e per trasformare altre righe
                                $rem_den = $Den[$i][$j_pivot];
                                for ($j=1;$j<=$N+1;$j++) {

                                    $rim_num = ($rem_num * $Num[$i_pivot][$j]); //frazione derivante da moltiplicazione tra coeff op elemetare e frazione riga pivot
                                    $rim_den = ($rem_den * $Den[$i_pivot][$j]);

                                    $c = 1;
                                    while ($c>0) {
                                        if ($rim_den >= $Den[$i][$j]) {
                                            if (floor($rim_den*$c/$Den[$i][$j]) == $rim_den*$c/$Den[$i][$j]) {
                                                $mcm = $rim_den*$c;
                                                break 1;
                                            }
                                        }
                                        else {
                                            if (floor($Den[$i][$j]*$c/$rim_den) == $Den[$i][$j]*$c/$min_den) {
                                                $mcm = $Den[$i][$j]*$c;
                                                break 1;
                                            }
                                        }
                                        $c++;
                                    }
                                    $Num[$i][$j] = $Num[$i][$j]*($mcm/$Den[$i][$j]) - $rim_num*($mcm/$rim_den);
                                    $Den[$i][$j] = $mcm;
                                    
                                    //caso in cui denominatore negativizza la frazione
                                    for ($hi=1;$hi<=$m+1;$hi++) {
                                        for ($hj=1;$hj<=$N+1;$hj++) {
                                            if ($Den[$hi][$hj]<0) {
                                                $Den[$hi][$hj] *= -1;
                                                $Num[$hi][$hj] *= -1;
                                            }
                                            if ($Num[$hi][$hj]==0)
                                                $Den[$hi][$hj] = 1;
                                        }
                                    }

                                    $MCD=1;
                                    $gi=1;
                                    while ($gi<=abs($Num[$i][$j]) && $gi<=$Den[$i][$j]) {
                                        if (floor(abs($Num[$i][$j])/$gi) == abs($Num[$i][$j])/$gi && floor($Den[$i][$j]/$gi) == $Den[$i][$j]/$gi) {
                                            $MCD = $gi;
                                        }
                                        $gi++;
                                    }

                                    if ($MCD != 1) {
                                        $Num[$i][$j] /= $MCD;
                                        $Den[$i][$j] /= $MCD;
                                    }
                                }
                            }
                        }
                    
                        //stampo la tabella trasformata
                        echo "<center><br><br><br><br><table class=\"mytable_reg\">";
                        echo "<tr><td class=\"mytd_bis\"></td>";
                        
                        for ($j=1;$j<=$n;$j++) {
                            if ($j<=9) {echo "<td class=\"myth\">x&#832" . $j . ";</td>";}
                            else {
                                echo "<td class=\"myth\">x";
                                for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                    echo "&#832" . substr($j,$l,1);
                                }
                                echo ";</td>";
                            }
                            
                        }
                        for ($j=1;$j<=$m;$j++) {
                            if ($j<=9) {echo "<td class=\"myth\">s&#832" . $j . ";</td>";}
                            else {
                                echo "<td class=\"myth\">s";
                                for ($l=0;$l<=strlen((string)$j)-1;$l++) {
                                    echo "&#832" . substr($j,$l,1);
                                }
                                echo ";</td>";
                            }
                            
                        }

                        //controllo ogni soluzione di base individuata non sia degenere e sia ammissibile
                        $s=0;
                        $q1=0;
                        for ($c=1;$c<=$m;$c++) {
                            if ($Num[$c][$N+1]==0) {
                                $q1++;
                                $degenere = true;
                            }
                            if ($Num[$c][$N+1]<0) {
                                //se ho violato la regione amissibile, ritorno alla situazione precedente
                                /*for ($o=1;$o<=$m+1;$o++) {
                                    for ($j=1;$j<=$N+1;$j++) {
                                        $Num[$o][$j] = $Num_bis[$o][$j];
                                        $Den[$o][$j] = $Den_bis[$o][$j];
                                    }
                                }
                                $pivot_past[$i_pivot] = $pivot_past_bis;*/
                                $s++;
                            }
                        }

                        //intestazioni di riga
                        $ham = false;
                        //$gum = [];
                        $k = 0;
                        $r = 0;
                        $g = 0;
                        //$bell = 0;
                        $rift = false;
                        for ($i=1;$i<=$m+1;$i++) {
                            echo "<tr>"; 

                            if ($i==$m+1) {
                                echo "<td class=\"myth\"></td>\n";
                            }
                            else {
                                //controlli per impostare la variabile che entra in base e quella che esce e vedere i cambiamenti delle scritte laterali
                                for ($y=1;$y<=$N;$y++) {

                                    //controllo se in quella colonna è presente un pivot, anche usato in precedenza, in modo da impostare correttamente i nomi delle variabili in base laterali
                                    $zeri = 0;
                                    $uno = 0;
                                    for ($x=1;$x<=$m+1;$x++) {
                                        if ($Num[$x][$y] == 0) {
                                            $zeri++;
                                        }
                                        if ($Num[$x][$y] == 1 && $Den[$x][$y] == 1) {
                                            $uno++;
                                            $cow = $x;
                                            $bell = $y;
                                        }
                                    }
                                    $base = false;      //controllo che, una colonna con le caratteristiche di un pivot, lo sia o lo sia effettivamente stato e quindi possa essere in base: utilizzo array di posizioni dei pivot sovrascritto ogniqualvolta venga indivuato il successivo per quella riga
                                    if ($zeri == $m && $uno == 1) {
                                        $km = $cow . $bell;
                                        for ($pk=1;$pk<=$m;$pk++) {
                                            if ($pivot_past[$pk]==$km) {        
                                                $base = true;
                                            }
                                            
                                        }
                                    }
                                    if ($zeri == $m && $uno == 1 && $cow == $i && $base) {
                                        //array ed indice delle variabili in base per ogni passaggio che viene sovrascritto ogni volta
                                        $k++;
                                        
                                        if ($bell<=$n) {
                                            if ($bell<=9) {
                                                
                                                $gum[1][$k] = "x&#832$bell;";
                                                $gum[2][$k] = $bell;
                                                $base_past[$again][$k] = $bell;
                                            }
                                            else {
                                                $gum[1][$k] = "x";
                                                for ($l=0;$l<=strlen((string)$bell)-1;$l++) {
                                                    $gum[1][$k] .= "&#832" . substr($bell,$l,1); //se valore almeno 10, si scrive 1 e 0
                                                    $gum[2][$k] .= "&#832" . substr($bell,$l,1);
                                                    $base_past[$again][$k] .= "&#832" . substr($bell,$l,1);
                                                }
                                            }
                                            
                                        }
                                        else {
                                            if ($bell-$n<=9) {
                                                $gum[1][$k] = "s&#832" . $bell-$n . ";";
                                                $gum[2][$k] = $bell;
                                                $base_past[$again][$k] = $bell;
                                            }
                                            else {
                                                $gum[1][$k] = "s";
                                                for ($l=0;$l<=strlen((string)$bell)-1;$l++) {
                                                    $gum[1][$k] .="&#832" . substr($bell-$n,$l,1);
                                                    $gum[2][$k] .="&#832" . substr($bell-$n,$l,1);
                                                    $base_past[$again][$k] .= "&#832" . substr($bell,$l,1);
                                                }
                                            }
                                            
                                        }
                                        $col_b[$k] = $bell; //array numero di colonne di variabili in base
                                        
                                        //stampa il nome delle variabili in base
                                        if ($bell <= $n) {
                                            if ($bell<=9) {echo "<td class=\"myth\">x&#832" . $bell . ";</td>";}
                                            else {
                                                echo "<td class=\"myth\">x";
                                                for ($l=0;$l<=strlen((string)$bell)-1;$l++) {
                                                    echo "&#832" . substr($bell,$l,1);
                                                }
                                                echo ";</td>";
                                            }
                                            
                                        }
                                        else {
                                            if ($bell<=9) {echo "<td class=\"myth\">s&#832" . $bell-$n . ";</td>";}
                                            else {
                                                echo "<td class=\"myth\">s";
                                                for ($l=0;$l<=strlen((string)$bell-$n)-1;$l++) {
                                                    echo "&#832" . substr($bell-$n,$l,1);
                                                }
                                                echo ";</td>";
                                            }
                                            
                                        }
                                        //è un pivot
                                        $ham = true;
                                        //break 1;
                                    }
                                    elseif ($zeri != $m || $uno !=1 || $base == false) {
                                        //array ed indice delle variabili fuori base per ogni passaggio che viene sovrascritto ogni volta
                                        $r++;
                                        
                                        if ($y<=$n) {
                                            if ($y<=9) {$mug[$r] = "x&#832$y;";}
                                            else {
                                                $mug[$r] = "x";
                                                for ($l=0;$l<=strlen((string)$y)-1;$l++) {
                                                    $mug[$r] .= "&#832" . substr($y,$l,1);
                                                }
                                            }
                                        }
                                        else {
                                            if ($y<=9) {$mug[$r] = "s&#832" . $y-$n . ";";}
                                            else {
                                                $mug[$r] = "s";
                                                for ($l=0;$l<=strlen((string)$y-$n)-1;$l++) {
                                                    $mug[$r] .= "&#832" . substr($y-$n,$l,1);
                                                }
                                            }
                                            
                                        }
                                        $col_nb[$r] = $y;
                                        //break 1;
                                        
                                    }    
                                                                
                                }  
                                
                                //se non vi sono stati o non ci sono pivot
                                if ($ham == false) {
                                    $g++;
                                    if ($i+$g<=$n && $i==1) {echo "<td class=\"myth\">x&#832" . $i+$g-1 . ";</td>\n";}
                                    if ($i+$g<=$n && $i!=1) {echo "<td class=\"myth\">x&#832" . $i+$g . ";</td>\n";}
                                    if ($i+$g>$n) {echo "<td class=\"myth\">s&#832" . $i+$g-$n . ";</td>\n";}
                                }                             
                            }   
                            
                            //inserisco i valori trasformati
                            //comprendo dove si trova il pivot che servirà nel passaggio successivo al presente, in modo da indicarlo, sia che si tratti di frazione o meno
                            for ($j=1;$j<=$N+1;$j++) {
                                if ($Den[$i][$j] != 1) {
                                    if ($i != $m+1) {
                                        if ($Num[$m+1][$j]>0) {
                                            
                                            for ($w=1;$w<=$N;$w++) {    

                                                //non è necessario porre >0 poichè se non ve ne fosse almeno uno positivo (che sarà il maggiore) non saremmo giunti qui
                                                if ($w == 1) {
                                                    $j_max_num = $Num[$m+1][$w];
                                                    $j_max_den = $Den[$m+1][$w];
                                                    $j_pivot = $w;
                                                }
                                                //se la frazione è la maggiore, allora, sottraendovi tutte le altre, il risultato sarà positivo: trovo mcm e faccio den comune
                                                $c=1;
                                                while ($c>0) {
                                                    if ($j_max_den >= $Den[$m+1][$w]) {
                                                        if (floor($j_max_den*$c/$Den[$m+1][$w]) == $j_max_den*$c/$Den[$m+1][$w]) {
                                                            $mcm = $j_max_den*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    else {
                                                        if (floor($Den[$m+1][$w]*$c/$j_max_den) == $Den[$m+1][$w]*$c/$j_max_den) {
                                                            $mcm = $Den[$m+1][$w]*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    $c++;
                                                }
                                                
                                                if ($j_max_num*($mcm/$j_max_den) - $Num[$m+1][$w]*($mcm/$Den[$m+1][$w]) < 0) {
                                                    $j_max_num = $Num[$m+1][$w];
                                                    $j_max_den = $Den[$m+1][$w];
                                                    $j_pivot = $w;
                                                }
                                            }
                                            
                                            //individuo la linea del cardine, ovvero la variabile uscente, se la condizione di illimitatezza è rispettata
                                            $brake = false;
                                            $min_den = -1;       //dichiaro un valore solo come confronto per vedere se ci sono state successive assegnazioni
                                            $ff = 1;
                                            for ($h=1;$h<=$m;$h++) {

                                                //faccio in modo di non dividere per 0
                                                if ($Num[$h][$j_pivot] != 0) {
                                                    $res_num = $Num[$h][$N+1]*$Den[$h][$j_pivot];
                                                    $res_den = $Den[$h][$N+1]*$Num[$h][$j_pivot];
                                                    
                                                    if ($res_num != 0 || $res_den>0) {
                                                        if ($res_den<0) {
                                                            $res_num  *= -1;
                                                            $res_den *= -1;
                                                        }
                                                        if ($res_num==0) {$res_den = 1;}
                                                    }

                                                    //semplifico rapporto
                                                    $MCD=1;
                                                    $gi=1;
                                                    while ($gi<=abs($res_num) && $gi<=$res_den) {
                                                        if (floor(abs($res_num)/$gi) == abs($res_num)/$gi && floor($res_den/$gi) == $res_den/$gi) {
                                                            $MCD = $gi;
                                                        }
                                                        $gi++;
                                                    }
                                                    if ($MCD != 1) {
                                                        $res_num /= $MCD;
                                                        $res_den /= $MCD;
                                                    }
                                                    
                                                    $first = false;
                                                    if ($h == 1) {
            
                                                        //se res minore di 0, allora procedo con il controllo delle successive righe
                                                        if ($res_num<0 || $res_den<0) {
            
                                                            //contollo se tra le possibilità è presente un valore che rispetti gli standard, il primo trovato viene preso come minimo in modo da sovrascrivere la variabile con un valore e poter continuare il ciclo da un punto di partenza esatto
                                                            for ($h=2;$h<=$m;$h++) {
                                                                if ($Num[$h][$j_pivot]!=0) {
                                                                    $rex_num = $Num[$h][$N+1]*$Den[$h][$j_pivot];
                                                                    $rex_den = $Den[$h][$N+1]*$Num[$h][$j_pivot];
                                                                    if ($rex_num>=0 && $rex_den>0) {
                                                                        $argo = true;
                                                                        $first = true;
                                                                        $rift = true;
                                                                        $min_num = $rex_num;
                                                                        $min_den = $rex_den;
                                                                        $amb[$ff] = $Num[$h][$j_pivot];
                                                                        $amb1[$ff] = $Den[$h][$j_pivot];
                                                                        $amb2[$ff] = $h;
                                                                        $min[$ff] = $min_num/$min_den;
                                                                        $ff++;
                                                                        $i_pivot = $h;
                                                                        break 1;
                                                                    }
                                                                }
                                                            }
                                                        } 
                                                        else {
                                                            $min_num = $res_num;
                                                            $min_den = $res_den;
                                                            $amb[$ff] = $Num[$h][$j_pivot];
                                                            $amb1[$ff] = $Den[$h][$j_pivot];
                                                            $amb2[$ff] = $h;
                                                            $min[$ff] = $min_num/$min_den;
                                                            $ff++;
                                                            $i_pivot = $h;
                                                            $argo = true;
                                                            $first = true;
                                                            $rift = true;
                                                        }
                                                    }
                                                        
                                                    //alla prima occasione nella quale non si divide per 0, nel caso fossimo oltre alla riga 2, assegno un valore di partenza se rispetta le condizioni necessarie
                                                    if ($brake && $min_den == -1 && $res_num>=0 && $res_den>0) {
                                                        $min_num = $res_num;
                                                        $min_den = $res_den;
                                                        $amb[$ff] = $Num[$h][$j_pivot];
                                                        $amb1[$ff] = $Den[$h][$j_pivot];
                                                        $amb2[$ff] = $h;
                                                        $min[$ff] = $min_num/$min_den;
                                                        $ff++;
                                                        $i_pivot = $h;
                                                        $argo = true;
                                                        $rift = true;
                                                    }
            
                                                    //controllo se il prossimo valore è minore del minimo fin'ora trovato, ovvero faccio differenza tra frazioni per comprendere se positiva
                                                    $c = 1;
                                                    while ($c>0) {
                                                        if ($min_den >= $res_den) {
                                                            if (floor($min_den*$c/$res_den) == $min_den*$c/$res_den) {
                                                                $mcm = $min_den*$c;
                                                                break 1;
                                                            }
                                                        }
                                                        else {
                                                            if (floor($res_den*$c/$min_den) == $res_den*$c/$min_den) {
                                                                $mcm = $res_den*$c;
                                                                break 1;
                                                            }
                                                        }
                                                        $c++;
                                                    }
                                                    if ($min_num*($mcm/$min_den) - $res_num*($mcm/$res_den) >= 0 && $res_num>=0 && $res_den>0 && $first==false && ($res_num!=$min_num || $res_den!=$min_den)) {
                                                        $min_num = $res_num;
                                                        $min_den = $res_den;
                                                        $amb[$ff] = $Num[$h][$j_pivot];
                                                        $amb1[$ff] = $Den[$h][$j_pivot];
                                                        $amb2[$ff] = $h;
                                                        $min[$ff] = $min_num/$min_den;
                                                        $ff++;
                                                        $i_pivot = $h;
                                                    }
                                                    if ($ff>2) {
                                                        if ($min[$ff-1] == $min[$ff-2]) {
                                                            for ($hg=2;$hg<=$ff-1;$ff++) {
                                                                $c=1;
                                                                while ($c>0) {
                                                                    if ($amb1[$hg-1] >= $amb1[$hg]) {
                                                                        if (floor($amb1[$hg-1]*$c/$amb1[$hg]) == $amb1[$hg-1]*$c/$amb1[$hg]) {
                                                                            $mcm = $amb1[$hg-1]*$c;
                                                                            break 1;
                                                                        }
                                                                    }
                                                                    else {
                                                                        if (floor($amb1[$hg]*$c/$amb1[$hg-1]) == $amb1[$hg]*$c/$amb1[$hg-1]) {
                                                                            $mcm = $amb1[$hg]*$c;
                                                                            break 1;
                                                                        }
                                                                    }
                                                                    $c++;
                                                                }
                                                                if ($amb[$hg-1]*($mcm/$amb1[$hg-1]) - $amb[$hg]*($mcm/$amb1[$hg]) <= 0) {
                                                                    $i_pivot = $amb2[$hg-1];
                                                                    break 1;
                                                                }
                                                                else {
                                                                    $i_pivot = $amb2[$hg];
                                                                    break 1;
                                                                }
                                                            }
                                                        }
                                                        
                                                        $brake = false; 
                                                    }
                                                }
                                                else {
                                                    $brake = true; 
                                                }
                                            }
                                        }
                                        
                                        //condizione sufficiente di illimitatezza (tutti coefficienti della colonna cardine sono <0) e impossibilità di individuare il pivot
                                        $argo1=0;
                                        for ($v=1;$v<=$m;$v++) {
                                            if ($Num[$v][$j_pivot]<0 || $Num[$v][$j_pivot]==0) {
                                                $argo1++;
                                            }
                                        }
                                        
                                        //stampo celle con stile in base al valore conteuto, se frazione o no
                                        if ($j != $N+1) {
                                            //affinchè quella cella sia il pivot, deve avere le medesime coordinate, la base deve essere ammissibile, deve essere stato individuato il pivot sucessivo e non deve essersi verificata la condizione di illimitatezza
                                            if ($i == $i_pivot && $j == $j_pivot && $s==0 && $argo1!=$m && $rift) {
                                                echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],0,".","") . "/" . number_format($Den[$i][$j],0,".","") . "</td>\n";
                                            }
                                            else {
                                                echo "<td class=\"mytd\">" . number_format($Num[$i][$j],0,".","") . "/" . number_format($Den[$i][$j],0,".","") . "</td>\n";
                                            }
                                        }
                                        /*if (floor($T[$i][$j]) != $T[$i][$j] && $j != $N+1) {
                                            if ($i == $i_pivot && $j == $j_pivot && $s==0 && $wx!=$m && $argo1!=$m && $rift) {
                                                echo "<td class=\"mytd_ter\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";
                                            }
                                            else {
                                                echo "<td class=\"mytd\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";
                                            }
                                        }*/
                                        if ($j == $N+1) {
                                            echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],0,".","") . "/" . number_format($Den[$i][$j],0,".","") . "</td>\n";           
                                        }
                                        /* if (floor($T[$i][$j]) != $T[$i][$j] && $j == $N+1) {
                                            echo "<td class=\"mytd2_bis\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";                           
                                        }*/
                                    }
                                    else {
                                        if ($j!=$N+1) {
                                            //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,".","") . "/" . number_format($Den[$i][$j],0,".","") . "</td>\n";
                                        /* }
                                            else {
                                                echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,"."," ") . "/" . number_format($Den[$i][$j],0,"."," ") . "</td>\n";
                                            }*/
                                        }
                                        else {
                                            //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                echo "<td class=\"mytd2\">" . "Z - " . number_format(abs($Num[$i][$j]),0,".","") . "/" . number_format($Den[$i][$j],0,".","") . "</td>\n";
                                            /*}
                                            else {
                                                echo "<td class=\"mytd2\">" . "Z - " . number_format(abs($Num[$i][$j]),0,"."," ") . "/" . number_format($Den[$i][$j],0,"."," ") . "</td>\n";
                                            }*/
                                        }
                                    }
                                }
                                else {
                                    if ($i != $m+1) {
                                        if ($Num[$m+1][$j]>0) {
                                            
                                            for ($w=1;$w<=$N;$w++) {    

                                                //non è necessario porre >0 poichè se non ve ne fosse almeno uno positivo (che sarà il maggiore) non saremmo giunti qui
                                                if ($w == 1) {
                                                    $j_max_num = $Num[$m+1][$w];
                                                    $j_max_den = $Den[$m+1][$w];
                                                    $j_pivot = $w;
                                                }
                                                //se la frazione è la maggiore, allora, sottraendovi tutte le altre, il risultato sarà positivo: trovo mcm e faccio den comune
                                                $c=1;
                                                while ($c>0) {
                                                    if ($j_max_den >= $Den[$m+1][$w]) {
                                                        if (floor($j_max_den*$c/$Den[$m+1][$w]) == $j_max_den*$c/$Den[$m+1][$w]) {
                                                            $mcm = $j_max_den*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    else {
                                                        if (floor($Den[$m+1][$w]*$c/$j_max_den) == $Den[$m+1][$w]*$c/$j_max_den) {
                                                            $mcm = $Den[$m+1][$w]*$c;
                                                            break 1;
                                                        }
                                                    }
                                                    $c++;
                                                }
                                                
                                                if ($j_max_num*($mcm/$j_max_den) - $Num[$m+1][$w]*($mcm/$Den[$m+1][$w]) < 0) {
                                                    $j_max_num = $Num[$m+1][$w];
                                                    $j_max_den = $Den[$m+1][$w];
                                                    $j_pivot = $w;
                                                }
                                            }
                                            
                                            //individuo la linea del cardine, ovvero la variabile uscente, se la condizione di illimitatezza è rispettata
                                            $brake = false;
                                            $min_den = -1;       //dichiaro un valore solo come confronto per vedere se ci sono state successive assegnazioni
                                            $ff = 1;
                                            for ($h=1;$h<=$m;$h++) {
                                               
                                                //faccio in modo di non dividere per 0
                                                if ($Num[$h][$j_pivot] != 0) {
                                                    if ($Num[$h][$N+1]==0) {
                                                        $brake = true;
                                                    }
                                                    else {
                                                        $res_num = $Num[$h][$N+1]*$Den[$h][$j_pivot];
                                                        $res_den = $Den[$h][$N+1]*$Num[$h][$j_pivot];
                                                        
                                                        if ($res_num != 0 || $res_den>0) {
                                                            if ($res_den<0) {
                                                                $res_num  *= -1;
                                                                $res_den *= -1;
                                                            }
                                                            if ($res_num==0) {$res_den = 1;}
                                                        }
                                                        
                                                        //semplifico rapporto
                                                        $MCD=1;
                                                        $gi=1;
                                                        while ($gi<=abs($res_num) && $gi<=$res_den) {
                                                            if (floor(abs($res_num)/$gi) == abs($res_num)/$gi && floor($res_den/$gi) == $res_den/$gi) {
                                                                $MCD = $gi;
                                                            }
                                                            $gi++;
                                                        }
                                                        if ($MCD != 1) {
                                                            $res_num /= $MCD;
                                                            $res_den /= $MCD;
                                                        }

                                                        $first = false;
                                                        if ($h == 1) {
                
                                                            //se res minore di 0, allora procedo con il controllo delle successive righe
                                                            if ($res_num<0 || $res_den<0) {
                
                                                                //contollo se tra le possibilità è presente un valore che rispetti gli standard, il primo trovato viene preso come minimo in modo da sovrascrivere la variabile con un valore e poter continuare il ciclo da un punto di partenza esatto
                                                                for ($h=2;$h<=$m;$h++) {
                                                                    if ($Num[$h][$j_pivot]!=0) {
                                                                        $rex_num = $Num[$h][$N+1]*$Den[$h][$j_pivot];
                                                                        $rex_den = $Den[$h][$N+1]*$Num[$h][$j_pivot];
                                                                        if ($rex_num>=0 && $rex_den>0) {
                                                                            $argo = true;
                                                                            $first = true;
                                                                            $rift = true;
                                                                            $min_num = $rex_num;
                                                                            $min_den = $rex_den;
                                                                            $amb[$ff] = $Num[$h][$j_pivot];
                                                                            $amb1[$ff] = $Den[$h][$j_pivot];
                                                                            $amb2[$ff] = $h;
                                                                            $min[$ff] = $min_num/$min_den;
                                                                            $ff++;
                                                                            $i_pivot = $h;
                                                                            break 1;
                                                                        }
                                                                    }
                                                                }
                                                            } 
                                                            else {
                                                                $min_num = $res_num;
                                                                $min_den = $res_den;
                                                                $amb[$ff] = $Num[$h][$j_pivot];
                                                                $amb1[$ff] = $Den[$h][$j_pivot];
                                                                $amb2[$ff] = $h;
                                                                $min[$ff] = $min_num/$min_den;
                                                                $ff++;
                                                                $i_pivot = $h;
                                                                $argo = true;
                                                                $first = true;
                                                                $rift = true;
                                                            }
                                                        }
                                                            
                                                        //alla prima occasione nella quale non si divide per 0, nel caso fossimo oltre alla riga 2, assegno un valore di partenza se rispetta le condizioni necessarie
                                                        if ($brake && $min_den == -1 && $res_num>=0 && $res_den>0) {
                                                            $min_num = $res_num;
                                                            $min_den = $res_den;
                                                            $amb[$ff] = $Num[$h][$j_pivot];
                                                            $amb1[$ff] = $Den[$h][$j_pivot];
                                                            $amb2[$ff] = $h;
                                                            $min[$ff] = $min_num/$min_den;
                                                            $ff++;
                                                            $i_pivot = $h;
                                                            $argo = true;
                                                            $rift = true;
                                                        }
                
                                                        //controllo se il prossimo valore è minore del minimo fin'ora trovato, ovvero faccio differenza tra frazioni per comprendere se positiva
                                                        $c = 1;
                                                        while ($c>0) {
                                                            if ($min_den >= $res_den) {
                                                                if (floor($min_den*$c/$res_den) == $min_den*$c/$res_den) {
                                                                    $mcm = $min_den*$c;
                                                                    break 1;
                                                                }
                                                            }
                                                            else {
                                                                if (floor($res_den*$c/$min_den) == $res_den*$c/$min_den) {
                                                                    $mcm = $res_den*$c;
                                                                    break 1;
                                                                }
                                                            }
                                                            $c++;
                                                        }
                                                        if ($min_num*($mcm/$min_den) - $res_num*($mcm/$res_den) >= 0 && $res_num>=0 && $res_den>0 && $first==false && ($res_num!=$min_num || $res_den!=$min_den)) {
                                                            $min_num = $res_num;
                                                            $min_den = $res_den;
                                                            $amb[$ff] = $Num[$h][$j_pivot];
                                                            $amb1[$ff] = $Den[$h][$j_pivot];
                                                            $amb2[$ff] = $h;
                                                            $min[$ff] = $min_num/$min_den;
                                                            $ff++;
                                                            $i_pivot = $h;
                                                        }
                                                        if ($ff>2) {
                                                            if ($min[$ff-1] == $min[$ff-2]) {
                                                                for ($hg=2;$hg<=$ff-1;$ff++) {
                                                                    $c=1;
                                                                    while ($c>0) {
                                                                        if ($amb1[$hg-1] >= $amb1[$hg]) {
                                                                            if (floor($amb1[$hg-1]*$c/$amb1[$hg]) == $amb1[$hg-1]*$c/$amb1[$hg]) {
                                                                                $mcm = $amb1[$hg-1]*$c;
                                                                                break 1;
                                                                            }
                                                                        }
                                                                        else {
                                                                            if (floor($amb1[$hg]*$c/$amb1[$hg-1]) == $amb1[$hg]*$c/$amb1[$hg-1]) {
                                                                                $mcm = $amb1[$hg]*$c;
                                                                                break 1;
                                                                            }
                                                                        }
                                                                        $c++;
                                                                    }
                                                                    if ($amb[$hg-1]*($mcm/$amb1[$hg-1]) - $amb[$hg]*($mcm/$amb1[$hg]) <= 0) {
                                                                        $i_pivot = $amb2[$hg-1];
                                                                        break 1;
                                                                    }
                                                                    else {
                                                                        $i_pivot = $amb2[$hg];
                                                                        break 1;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $brake = false; 
                                                    }
                                                }
                                                else {
                                                    $brake = true; 
                                                }
                                            }
                                        }

                                        //controllo ogni soluzione di base individuata non sia degenere e sia ammissibile
                                        $s=0;
                                        $q1=0;
                                        for ($c=1;$c<=$m;$c++) {
                                            if ($Num[$c][$N+1]==0) {$q1++;}
                                            if ($Num[$c][$N+1]<0) {
                                                $s++;
                                                $migl = false;
                                            }
                                        }

                                        //condizione sufficiente di illimitatezza (tutti coefficienti della colonna cardine sono <0) e impossibilità di individuare il pivot
                                        $argo1=0;
                                        for ($v=1;$v<=$m;$v++) {
                                            if ($Num[$v][$j_pivot]<0 || $Num[$v][$j_pivot]==0) {
                                                $argo1++;
                                            }
                                        }
                                        
                                        //stampo celle con stile in base al valore conteuto, se frazione o no
                                        if ($j != $N+1) {
                                            //affinchè quella cella sia il pivot, deve avere le medesime coordinate, la base deve essere ammissibile, deve essere stato individuato il pivot sucessivo e non deve essersi verificata la condizione di illimitatezza
                                            if ($i == $i_pivot && $j == $j_pivot && $s==0 && $argo1!=$m && $rift) {
                                                echo "<td class=\"mytd_ter\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                            }
                                            else {
                                                echo "<td class=\"mytd\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                            }
                                        }
                                        /*if (floor($T[$i][$j]) != $T[$i][$j] && $j != $N+1) {
                                            if ($i == $i_pivot && $j == $j_pivot && $s==0 && $wx!=$m && $argo1!=$m && $rift) {
                                                echo "<td class=\"mytd_ter\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";
                                            }
                                            else {
                                                echo "<td class=\"mytd\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";
                                            }
                                        }*/
                                        if ($j == $N+1) {
                                            echo "<td class=\"mytd2_bis\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";           
                                        }
                                        /*if (floor($T[$i][$j]) != $T[$i][$j] && $j == $N+1) {
                                            echo "<td class=\"mytd2_bis\">" . number_format($T[$i][$j],3,"."," ") . "</td>\n";                           
                                        }*/
                                    }
                                    else {
                                        if ($j!=$N+1) {
                                            //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,".","") . "</td>\n";
                                        /* }
                                            else {
                                                echo "<td class=\"mytd1\">" . number_format($Num[$i][$j],0,"."," ") . "/" . number_format($Den[$i][$j],0,"."," ") . "</td>\n";
                                            }*/
                                        }
                                        else {
                                            //if (floor($Num[$i][$j]) == $Num[$i][$j]) {
                                                echo "<td class=\"mytd2\">" . "Z - " . number_format(abs($Num[$i][$j]),0,".","") . "</td>\n";
                                            /*}
                                            else {
                                                echo "<td class=\"mytd2\">" . "Z - " . number_format(abs($Num[$i][$j]),0,"."," ") . "/" . number_format($Den[$i][$j],0,"."," ") . "</td>\n";
                                            }*/
                                        }
                                    }
                                }
                            }
                            echo "</tr>\n";
                        }
                        

                        echo "</table><br></center>\n";
                    }

                    //stampo i risultati dei controlli effettuati durante ogni passaggio  
                    $rt = 0;
                    if ($q1!=0 && $s==0) {
                        echo "<br>&nbsp;&nbsp;Sono stati rilevati $q1 termini noti nulli, perciò la soluzione di base è degenere<br>";
                        $degenere = true;
                        for ($y=1;$y<=$N;$y++) {
                            if ($Num[$m+1][$y]>0) {
                                $rt++;
                            }
                        }
                        /*if ($rt!=0 && ($Num[$i_pivot][$N+1]==0 || $rift == false)) {
                            echo "<br>&nbsp;&nbsp;Nonostante la presenza di costi ridotti positivi, ulteriori cambi di base non migliorerebbero la situazione";
                        }*/
                        //echo "<br><br>";
                    }                
                    if ($s!=0) {
                        echo "<br>&nbsp;&nbsp;Sono stati rilevati $s termini noti negativi, perciò la soluzione di base non è ammissibile: nessun miglioramento potrà essere posto in essere poiché è stato utilizzato il &nbsp;&nbsp;vincolo maggiormente stringente, tolto quello non ammissibile, ma nonostante ciò è stata violata la base imponibile. <br>&nbsp;&nbsp;La soluzione di base ammissibile ottimale, seppur non soddisfi la condizione di ottimalità, è quella precedente<br>";

                    }
                    if ($argo1 == $m) {
                        echo "<br>&nbsp;&nbsp;Non vi è soluzione, a causa dell'illimitatezza della regione ammissibile e della forma della funzione obiettivo<br>";
                        $migl = false;
                        break;
                    }
                    /*if (($rift==false || ($degenere==true && $rt!=0 && $Num[$i_pivot][$N+1]==0)) && $wx != $m) {
                        $migl = false;
                        break;
                    }*/

                    //dico se la soluzione è ottima oppure no
                    if ($rift) {
                        echo "<br>La soluzione di base indviduata non è ottimale<br><br>";
                    }
                    else {
                        echo "<br>Non si individuano ulteriori miglioramenti<br>";
                    }

                    //controllo di non essere in una vecchia base
                    for ($lo=1;$lo<=$again;$lo++) {
                        $wer=0;
                        for ($r=1;$r<=$m;$r++) {
                            for ($r1=1;$r1<=$m;$r1++) {
                                if ($base_past[$lo][$r]==$gum[2][$r1]) {
                                    $wer++;
                                }
                                if ($wer==$m)
                                    $gru = $lo;
                            }
                        }
                        if ($wer==$m && $gru!=$again && $gel==false) {
                            echo "<br>&nbsp;&nbsp;Una continua iterazione del metodo del simplesso, porterebbe ora a rimanere nel medesimo vertice seppur passando continuamente tra basi differenti<br>";
                            $migl = false;
                            $gel = true;
                            break 2;
                        }
                    }
                }
                
                
                //stampo i dati finali di funzione e variabili, se la base è ammissibile, non si è verificata condizione di illimitatetzza, la base è ammissibile, è stato individuato il pivot e sia che possa essere degenere o meno
                if ($q!=$m && $s==0 && $argo && $argo1!=$m) {
                    if ($Den[$m+1][$N+1] != 1) {
                        echo "<br><center>Il valore ottimo della funzione obiettivo è " . number_format(abs($Num[$m+1][$N+1]),0,".","") . "/" . number_format($Den[$m+1][$N+1],0,".","") . "\n";
                    }
                    else {
                        echo "<br><center>Il valore ottimo della funzione obiettivo è " . number_format(abs($Num[$m+1][$N+1]),0,".","") . "\n";
                    }
                    
                    echo "<br><br>La soluzione di base ottima è: {";

                    $sun = 1;
                    while ($sun<=$N) {
                        $sun1 = $sun;
                        //valore assunto dalle variabili in base
                        for ($i=1;$i<=$m;$i++) {
                            //inserisco le variabili in base e no, in ordine di "intestazione", con rispettivi valori della soluzione ottima
                                
                            if ($gum[2][$i] == $sun) {
                                echo $gum[1][$i] . "*&nbsp;=&nbsp;";

                                for ($x=1;$x<=$m+1;$x++) {
                                    if ($x==$i) {
                                        if ($Den[$x][$N+1] != 1) {
                                            echo number_format($Num[$x][$N+1],0,".","") . "/" . number_format($Den[$x][$N+1],0,".","") . "\n";
                                            $sun++;
                                            break 2;
                                        }
                                        else {
                                            echo number_format($Num[$x][$N+1],0,".","") . "\n";
                                            $sun++;
                                            break 2;
                                        }
                                    }
                                }
                            } 
                            if ($i==8)
                                echo "<br>";                               
                        }
                        
                        if ($sun1==$sun) {
                            echo $hum[$sun] . "*&nbsp;=&nbsp;0";
                            $sun++;
                        }
                        if ($sun!=$N+1) {
                            echo "&nbsp;&nbsp;&nbsp;\n";
                        }
                    }
                    echo "}";

                    //forma analitica della funzione obiettivo con le variabili fuori base e relativi valori
                    echo "<br><br>La funzione obiettivo in forma analitica è:&nbsp;&nbsp;&nbsp;Z =&nbsp;";
                    if ($Den[$m+1][$N+1] != 1) {
                        echo number_format(abs($Num[$m+1][$N+1]),0,".","") . "/" . number_format($Den[$m+1][$N+1],0,".","") . "\n";
                    }
                    else {
                        echo number_format(abs($Num[$m+1][$N+1]),0,".","") . "\n";
                    }

                    $check = false;
                    $mi = 0;
                    $de = 1;
                    for ($j=1;$j<=$N;$j++) {
                        for ($i=1;$i<=$n;$i++) {
                            if ($j == $col_nb[$i] && $Num[$m+1][$j]<0) {
                                if ($Den[$m+1][$j] != 1) {
                                    echo "-&nbsp;" . number_format(abs($Num[$m+1][$j]),0,".","") . "/" . number_format($Den[$m+1][$j],0,".","") . "&nbsp;" . $mug[$i] . "&nbsp;&nbsp;";
                                }
                                else {
                                    echo "-&nbsp;" . number_format(abs($Num[$m+1][$j]),0,".","") . "&nbsp;" . $mug[$i] . "&nbsp;&nbsp;";
                                }
                            }
                            elseif ($j == $col_nb[$i]) {
                                //considero il caso in cui costi ridotti positivi ma non continuo a causa loop degenere
                                if ($Den[$m+1][$j] != 1) {
                                    echo "+&nbsp;" . number_format(abs($Num[$m+1][$j]),0,".","") . "/" . number_format($Den[$m+1][$j],0,".","") . "&nbsp;" . $mug[$i] . "&nbsp;&nbsp;";
                                }
                                else {
                                    echo "+&nbsp;" . number_format(abs($Num[$m+1][$j]),0,".","") . "&nbsp;" . $mug[$i] . "&nbsp;&nbsp;";
                                }
                                if ($Num[$m+1][$j]==0) {
                                    $mi++;
                                    $mu[$de] = $j;
                                    $de++;
                                    $check = true; //una variabile fuori base può variare liberamente
                                    $varvar[1][$mi]= $mug[$i];
                                    $varvar[2][$mi]= $j;
                                }
                            }
                        }
                        if ($j==8)
                            echo "<br>";
                    }           
                    echo "<br>";
                    //variare una variabile non in base con costo ridotto nullo
                    if ($check) {
                        $ram=false;
                        for ($z=1;$z<=$mi;$z++) {
                            echo "<br><center>La variabile ";
                            echo $varvar[1][$z];
                            
                            for ($ki=1;$ki<=$de-1;$ki++) {
                                $hyt=0;
                                for ($i=1;$i<=$m;$i++) {
                                    if ($Num[$i][$mu[$ki]]>0 && $mu[$ki]==$varvar[2][$z]) {
                                        $hyt++;
                                    }
                                }
                                if ($hyt!=0) {
                                    echo " può muoversi liberamente in un intervallo limitato.<br>";
                                }
                                else {
                                    echo " può muoversi liberamente in un intervallo illimitato.<br>";
                                    $ram = true;
                                }
                            }
                        }
                        if ($ram) {
                            echo "Perciò le soluzioni sono infinite in un intervallo illimitato</center>";
                        }
                        else {
                            echo "Perciò le soluzioni sono infinite in un intervallo limitato</center>";
                        }
                    }
                    else {
                        echo "<br><center>La soluzione ottima è unica";
                    }
                }
                elseif ($argo==false && $s==0 && $q!=$m) {
                    echo "<br>&nbsp;&nbsp;Non è stato possibile individuare il pivot";
                }
            
            }
            echo "</center>";
        ?>
        
    </body>

</html>
