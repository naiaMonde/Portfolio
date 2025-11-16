<?php
function chargerProfil($user) {
    $watched = [];
    $watchlist = [];

    // watched.csv
    $f = fopen("Data/$user/watched.csv", "r");
    if ($f) {
        $first = true;
        while (($row = fgetcsv($f)) !== false) {
            if ($first) { $first = false; continue; }
            $watched[] = $row[1];
        }
        fclose($f);
    }

    // watchlist.csv
    $f = fopen("Data/$user/watchlist.csv", "r");
    if ($f) {
        $first = true;
        while (($row = fgetcsv($f)) !== false) {
            if ($first) { $first = false; continue; }
            $watchlist[] = $row[1];
        }
        fclose($f);
    }

    return [$watched, $watchlist];
}

function importationPresent($liste) {
    $res = [];
    foreach ($liste as $u) {
        $res[] = chargerProfil($u);
    }
    return $res;
}

function importationPasPresent($liste) {
    $res = [];
    foreach ($liste as $u) {
        $res[] = chargerProfil($u);
    }
    return $res;
}

function filmRandom($present) {
    $wl_commune = [];

    foreach ($present as $source) {
        foreach ($source[1] as $film) {
            $est_vu = false;

            foreach ($present as $profil) {
                if (in_array($film, $profil[0])) {
                    $est_vu = true;
                    break;
                }
            }
            if (!$est_vu) $wl_commune[] = $film;
        }
    }
    return $wl_commune;
}

function watchlistCommune($present) {
    $wl = filmRandom($present);
    $res = [];

    foreach ($wl as $f) {
        if (count(array_keys($wl, $f)) >= 2 && !in_array($f, $res)) {
            $res[] = $f;
        }
    }
    return $res;
}

function dejaVu($present, $absent) {
    $wl = filmRandom($present);
    $deja = [];

    // Films déjà vus par un absent
    foreach ($absent as $profil) {
        foreach ($profil[0] as $film) {
            if (in_array($film, $wl) && !in_array($film, $deja)) {
                $deja[] = $film;
            }
        }
    }

    // Filtrer : si un présent l’a vu → on enlève
    $final = [];
    foreach ($deja as $film) {
        $vuParPresent = false;
        foreach ($present as $profil) {
            if (in_array($film, $profil[0])) {
                $vuParPresent = true;
                break;
            }
        }
        if (!$vuParPresent) $final[] = $film;
    }

    return $final;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Roulette</title>
</head>
<body>

<h1>Qui est présent ?</h1>

<form method="POST">
    <?php
    $gens = [];

    foreach (scandir("Data") as $entry) {
        if ($entry === "." || $entry === "..") continue;
        echo "<label><input type='checkbox' name='present[]' value='$entry'> $entry</label><br>";
        $gens[] = $entry;
    }
    ?>
    <button type="submit">Valider</button>
</form>

<?php
if (!isset($_POST["present"])) exit;

$present = $_POST["present"];
$absent = array_values(array_diff($gens, $present));

$profilPresent = importationPresent($present);
$profilAbsent  = importationPasPresent($absent);

// Boutons d’actions
echo "<hr>";
echo "<form method='POST'>";
foreach ($present as $p) echo "<input type='hidden' name='present[]' value='$p'>";
echo "
    <button name='action' value='random'>Film random</button>
    <button name='action' value='commune'>Watchlist commune</button>
    <button name='action' value='absent'>Déjà vu par un absent</button>
</form>
";

if (isset($_POST["action"])) {
    echo "<h2>Résultat :</h2>";

    if ($_POST["action"] === "random") {
        $films = filmRandom($profilPresent);
    }
    else if ($_POST["action"] === "commune") {
        $films = watchlistCommune($profilPresent);
    }
    else {
        $films = dejaVu($profilPresent, $profilAbsent);
    }

    if (count($films) === 0) {
        echo "Aucun film trouvé.";
    } else {
        echo $films[array_rand($films)];
    }
}
?>

</body>
</html>
