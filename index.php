<?php
function share($v, $sum): float|int
{
    return round($v/$sum,2)*100;
}

function IP(){
    /*if ($_SERVER['REMOTE_ADDR']=="127.0.0.1")
    return rand(0,255).".".rand(0,255).".".rand(0,255);
    else*/
        return $_SERVER['REMOTE_ADDR'];
}

function userIP($IP){
    //echo $IP."<br>";
    $name="";
    $names = array("Anastasia"=>"26.113.31.57",
        "Igor"=>"26.208.70.215",
        "IRISKA_PC"=>"26.208.70.215",
        "Irina Alekseevna"=>"26.27.154.150",
        "LEVPAVELKO"=>"26.42.27.87",
        "Nastia"=>"26.1.97.67",
        "Oleksii Musiyenko"=>"26.198.238.59",
        "Oleksiy Musiyenko"=>"26.244.69.84",
        "Pismichenko Igor"=>"26.47.228.119",
        "Viktoria"=>"26.238.242.38",
        "Vitaliy"=>"26.190.40.108",
        "Vladislav"=>"26.230.170.42",
        "Vladislav S"=>"26.41.169.173",
        "Vladislav Pavlyuk" =>"26.115.215.169");
    //reset($names);
     $name = array_search($IP, $names);
         if ($name!="") return $name;
         else
             return $IP;
}

function twoDatesCompare($date1, $date2){
    if (gettype($date1) == "string") {
        $datetime1 = date_create($date1);
        //$datetime1 = $datetime1->format('Y-m-d H:i:s');
    }
    if (gettype($date2)=="string")
    {
        $datetime2=date_create($date2);
        date_sub($datetime2,date_interval_create_from_date_string("60 seconds"));
        //$datetime2 = $datetime2->format('Y-m-d H:i:s');
    }
    //echo date('Y-m-d H:i:s', strtotime($datetime1))."     ".date('Y-m-d H:i:s', strtotime($datetime2))."<br>";
    //echo $datetime1->format('Y-m-d H:i:s')." < ".$datetime2->format('Y-m-d H:i:s')."<br>";

    if($datetime1 < $datetime2) //если время1 раньше времени2 после отката времени2 на 60 секунд назад, возвращаем true
        return true;
    else
        return false;
}

function uniqueIP(array $votesArr, string $IP)
{
        $count = 0;
        for ($i = 0; $i < count($votesArr); $i++)
        {
            if (strstr($votesArr[$i][1], $IP))     //если IP найден
            {
                for ($j = $i+1; $j < count($votesArr); $j++) // то начиная со следуюего элемента ищем повтор того же IP
                {
                    if (strstr($votesArr[$j][1], $IP))     // если второй повтор найден
                    {
                        if (twoDatesCompare($votesArr[$i][0], ($votesArr[$j][0])))  $count = 0; //  и разница между повторами положительна
                        //echo $count."<br>";
                        else $count++;
                    }
                }
            }
        }
        if ($count > 0) //если повторов больше 0, IP не уникален
            return false;
        else
            return true;
}
function votesCount(array $votesArr, string $vote)      //подсчет голосов за каждого кандидата
{
    //echo "Вход в votesCount";
    $count = 0;
        for ($i = 0; $i < count($votesArr); $i++) {

            $votesArr2[$i] = $votesArr[$i];

            if (strstr($votesArr[$i][2], $vote, false) // если кагдидат найден
                && uniqueIP($votesArr2, $votesArr[$i][1]) // проверяем уникальность IP
            ) {
                $count++;   //добавляем голос, если он уникален
            }
        }

        return $count;
}


$redMessage = '';
$greenMessage = '';

if (file_exists("log.txt")){
if (!empty($_GET)) {
    if (isset($_GET["lang"]))
    {
        $lang = $_GET["lang"];

        switch ($lang) {
            case "cpp":
                $vote = 'C++';
                break;
            case "csharp":
                $vote = 'C#';
                break;
            case "javascript":
                $vote = 'JavaScript';
                break;
            case "php":
                $vote = 'PHP';
                break;
            case "java":
                $vote = 'Jаva ';
                break;
        }

//$_SERVER['REMOTE_ADDR']
        //сохраняем данные в файл
        $data = gmdate("Y/m/d H:i:s")." @ ".IP()." @ ".$vote."\r\n";
        $file=fopen("log.txt", "a");
        fwrite($file, $data);
        fclose($file);
        $greenMessage = 'Выбор выполнен.';
    }
    else {
        $redMessage = 'Сделайте Ваш выбор!';
    }

    }
} else $redMessage = "Can't reach file votes.txt.<br>";

//Подсчет голосов из log-файла
$voteString = '';

if (file_exists("log.txt")){
    $voteString = file_get_contents("log.txt");}
else $redMessage = "Can't reach file votes.txt.<br>";

    $votesArr = explode("\r\n", $voteString);
    for ($i = $j = 0; $i < count($votesArr); $i++) {
        $votesArr[$i] = explode("@", $votesArr[$i]);
    }
array_pop($votesArr);


//$test=arrayIPonly($votesArr);
//printArray($votesArr);

$cpp = $csharp = $javascript = $php = $java = 0;

if (isset($votesArr[0][2]))     //если массив не пустой, то считаем голоса
{
    $greenMessage = "Идет поиск";
    $cpp = votesCount($votesArr, 'C++');
    $csharp = votesCount($votesArr, 'C#');
    $javascript = votesCount($votesArr, 'JavaScript');
    $php = votesCount($votesArr, 'PHP');
    $java = votesCount($votesArr, 'Jаva');
} else $redMessage = "Поиск не работает";


$sum = $cpp + $csharp + $javascript + $php + $java;

if ($sum > 0){
    $cpp = share($cpp,$sum);
    $csharp = share($csharp,$sum);
    $javascript = share($javascript,$sum);
    $php = share($php,$sum);
    $java = share($java,$sum);
} else $redMessage = 'Голоса не найдены!';

//Печать хода голосования из log-файла
$votes = file("log.txt");
$votesTab = str_replace(" @ ","</td><td align=center>", $votes);
$votesTab = str_replace("<br>","</tr><tr>", $votesTab);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Votes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<h2 align="center">Интернет голосование</h2>
<h3 align="center">Какому языку программирования Вы отдали бы предпочтение?</h3>

    <div class="container" align="center">
        <div class="row" align="left">
            <form method="get">
                <ul>
                <input type="radio" id="cpp" name="lang" value="cpp">
                <label for="cpp">C++</label><br>

                <input type="radio" id="csharp" name="lang" value="csharp">
                <label for="csharp">C#</label><br>

                <input type="radio" id="javascript" name="lang" value="javascript">
                <label for="javascript">JavaScript</label><br>

                <input type="radio" id="php" name="lang" value="php">
                <label for="php">PHP</label><br>

                <input type="radio" id="java" name="lang" value="java">
                <label for="java">Java</label><br>

                <button type="submit" class="btn btn-primary" name="vote">Голосовать!</button>

                </ul>
            <div style="color: red"><?= $redMessage ?></div>
            <div style="color: green"><?= $greenMessage ?></div>



            <?php
            //if (isset($_GET["lang"]))
                {
                    echo '<div><form method="post"><table border="1" align="bottom">';
                    echo '<h2 align="center">Итоги голосования</h2>';
                    echo '<tr><td align="center"> Язык программирования </td><td align="center"> % голосов </td></tr>';
                    echo '<tr><td align="center">C++</td><td align="center">'.$cpp." %".'</td></tr>';
                    echo '<tr><td align="center">C#</td><td align="center">'.$csharp." %".'</td></tr>';
                    echo '<tr><td align="center">JavaScript</td><td align="center">'.$javascript." %".'</td></tr>';
                    echo '<tr><td align="center">PHP</td><td align="center">'.$php." %".'</td></tr>';
                    echo '<tr><td align="center">Java</td><td align="center">'.$java." %".'</td></tr>';
                    echo '</table></form></div>';

                    echo '<br>';
                    echo '<div><form method="post"><table border="1" align="bottom">';
                    echo '<h2 align="center">Ход голосования</h2>';
                    echo '<tr>
                            <td align="center"> Date Time </td>
                            <td align="center"> Voters IP </td>
                            <td align="center"> Язык программирования </td>
                            </tr>';

                        foreach (array_reverse($votesTab) as $vote)
                            echo '</tr><td> '.userIP($vote). ' </td><tr>';

                   echo'</table></form></div>';
                }
            ?>
            </form>

    </div>

<div>

</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
