<?php
function share($v, $sum): float|int
{
    return round($v/$sum,2)*100;
}

function votesCount(array $votesArr, string $vote)
{
    $count = 0;
    foreach ($votesArr as $str) {
        if (strstr($str,$vote,true)) $count++;
        }
    return $count;
}
/*function votesCount(array $votesArr, string $vote)
{
    $counts = array_count_values($votesArr);
    return $counts[$vote];
}*/

/*function votesCount(array $votesArr, string $vote)
{
    $count=0;
    foreach ($votesArr as $v) {
        if ($v == $vote) $count++;
    }
    return $count;
}*/

function printArray($array){
    foreach($array as $v){
        echo $v;
    }
}

$redMessage = '';
$greenMessage = '';

if (file_exists("votes.txt")){
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
                $vote = 'Java';
                break;
        }


        //сохраняем данные в файл
        $data = gmdate("Y/m/d H:i:s")." @ ".$_SERVER['REMOTE_ADDR']." @ ".$vote." @ "."\r\n";
        $file=fopen("votes.txt", "a");
        fwrite($file, $data);
        fclose($file);
        $greenMessage = 'Выбор выполнен.';
    }
    else {
        $redMessage = 'Сделайте Ваш выбор!';
    }


        //Подсчет голосов из log-файла
        $voteString = '';
        if (file_exists("votes.txt")){
       $voteString = file_get_contents("votes.txt");}
        else $redMessage = "Can't reach file votes.txt.<br>";

        $votesArr= explode("@", $voteString);

    //printArray($votesArr);

        $cpp=votesCount($votesArr, 'C++');
        $csharp=votesCount($votesArr,'C#');
        $javascript=votesCount($votesArr,'JavaScript');
        $php=votesCount($votesArr,'PHP');
        $java=votesCount($votesArr,'Java');

        $sum = $cpp + $csharp + $javascript + $php + $java;

        if ($sum > 0){
            $cpp=share($cpp,$sum);
            $csharp=share($csharp,$sum);
            $javascript=share($javascript,$sum);
            $php=share($php,$sum);
            $java=share($java,$sum);
        } else $redMessage = 'Голоса не найдены!';

        //Печать хода голосования из log-файла
        $votes = file("votes.txt");
        $votesTab = str_replace(" @ ","</td><td align=center>", $votes);
        $votesTab = str_replace("<br>","</tr><tr>", $votesTab);
    }
} else $redMessage = "Can't reach file votes.txt.<br>";
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
            if (isset($_GET["lang"]))
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
                            echo '</tr><td> '.$vote. ' </td><tr>';

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
